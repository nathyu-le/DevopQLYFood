#!/usr/bin/env python3
"""Real Apache/PHP/MariaDB checks, including container recreation.

Run only against the disposable Compose project whose name ends in _ci.
The CI workflow starts and removes that project. Never point this at a VPS DB.
"""
import http.cookiejar
import json
import os
from pathlib import Path
import re
import secrets
import subprocess
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
NAME = 'Container CI ' + secrets.token_hex(10)
CHECKS = []

def compose(*args):
    return subprocess.run(['docker', 'compose', *args], cwd=ROOT,
                          check=True, text=True, capture_output=True).stdout

def check(condition, message):
    if not condition:
        raise AssertionError(message)
    CHECKS.append(message)
    print('PASS: ' + message, flush=True)

def main():
    if os.environ.get('RUN_CONTAINER_TESTS') != '1' or not os.environ.get('COMPOSE_PROJECT_NAME', '').endswith('_ci'):
        raise SystemExit('Only RUN_CONTAINER_TESTS=1 with an isolated *_ci Compose project is allowed.')
    base = 'http://127.0.0.1:' + os.environ.get('APP_PORT', '8080') + '/'
    password = (ROOT / '.secrets/admin_password').read_text().strip()
    client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

    def request(path, fields=None):
        data = urllib.parse.urlencode(fields).encode() if fields is not None else None
        try:
            with client.open(base + path, data=data, timeout=10) as response:
                return response.status, response.read().decode(), response.geturl()
        except urllib.error.HTTPError as error:
            return error.code, error.read().decode(), error.geturl()

    def token(body):
        match = re.search(r'name="csrf" value="([a-f0-9]+)"', body)
        if not match:
            raise AssertionError('Missing CSRF token.')
        return match.group(1)

    def login():
        _, body, _ = request('login.php')
        status, body, url = request('login.php', {'csrf': token(body), 'username': 'admin', 'password': password})
        check(status == 200 and url.endswith('index.php'), 'administrator can log in through Apache')
        return token(body)

    def query(statement):
        script = r'''
        $c=require '/var/www/html/config/config.local.php';$d=$c['db'];
        $p=new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',$d['host'],$d['port'],$d['name']),$d['user'],$d['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        $q=$p->prepare($argv[1]);$q->execute([$argv[2]]);echo json_encode($q->fetch(PDO::FETCH_ASSOC));
        '''
        return json.loads(compose('exec', '-T', 'app', 'php', '-r', script, statement, NAME))

    select = 'SELECT id, name, price, is_available FROM dishes WHERE name=?'
    try:
        status, body, _ = request('health.php')
        check(status == 200 and json.loads(body)['status'] == 'ok', 'health check reaches MariaDB')
        _, body, _ = request('version.php')
        check(json.loads(body)['revision'] == os.environ['APP_REVISION'], 'running image has the expected commit revision')
        for path in ('config/config.local.php', 'database/schema.sql', '.secrets/admin_password', 'README.md'):
            check(request(path)[0] == 404, 'DocumentRoot excludes ' + path)
        csrf = login()
        fields = {'csrf': csrf, 'action': 'create', 'name': NAME, 'category': 'main', 'price': '45000', 'description': '<script>bad()</script>', 'is_available': '1'}
        check(request('index.php', {**fields, 'csrf': 'bad'})[0] == 403, 'invalid CSRF blocks a write')
        request('index.php', {**fields, 'price': '-1'})
        check(query(select) is False, 'invalid price is absent from the real database')
        request('index.php', fields)
        before = query(select)
        check(before is not False and int(before['price']) == 45000, 'HTTP create persists a dish in MariaDB')
        _, body, _ = request('index.php?q=' + urllib.parse.quote(NAME))
        check(NAME in body and '45.000 đ' in body, 'reload and search read the inserted dish')
        check('&lt;script&gt;bad()&lt;/script&gt;' in body and '<script>bad()</script>' not in body, 'stored description is escaped')
        dish_id = str(before['id'])
        request('index.php', {**fields, 'action': 'update', 'id': dish_id, 'price': '50000', 'is_available': '0'})
        row = query(select)
        check(int(row['price']) == 50000 and int(row['is_available']) == 0, 'HTTP update changes price and availability')

        # A real recreation of both containers, with the same named DB volume.
        compose('up', '-d', '--no-build', '--force-recreate', '--wait', '--wait-timeout', '180')
        after = query(select)
        check(after == row, 'same ID, name, price and status survive recreation of app and DB')
        evidence_directory = os.environ.get('CONTAINER_EVIDENCE_DIR')
        if evidence_directory:
            destination = Path(evidence_directory)
            destination.mkdir(parents=True, exist_ok=True)
            (destination / 'persistence.json').write_text(json.dumps({
                'revision': os.environ['APP_REVISION'],
                'compose_project': os.environ['COMPOSE_PROJECT_NAME'],
                'before_recreate': row, 'after_recreate': after,
                'same_record_preserved': after == row,
                'scope': 'temporary Docker staging database, not cPanel',
            }, ensure_ascii=False, indent=2) + '\n')
        csrf = login()
        _, body, _ = request('index.php?q=' + urllib.parse.quote(NAME))
        check(NAME in body and '50.000 đ' in body, 'the recreated web container reads the persistent record')
        request('index.php', {'csrf': csrf, 'action': 'delete', 'id': dish_id})
        check(query(select) is False, 'HTTP delete removes the test record')
        print(f'{len(CHECKS)} container checks passed; persistence verified.', flush=True)
    finally:
        query('DELETE FROM dishes WHERE name=?')

if __name__ == '__main__':
    main()
