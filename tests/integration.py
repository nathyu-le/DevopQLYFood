#!/usr/bin/env python3
"""Exercise real PHP + MySQL CRUD over HTTP in an isolated *_test database.

Requires RUN_INTEGRATION_TESTS=1 and TEST_DB_HOST/PORT/NAME/USER/PASSWORD.
Refuses to overwrite config.local.php. Creates only a uniquely named test dish;
removes it and the temporary config in finally. Never use a deployed database.
"""
import html
import http.cookiejar
import json
import os
from pathlib import Path
import re
import secrets
import socket
import subprocess
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
CONFIG = ROOT / 'config/config.local.php'
NAME = 'CI Cơm gà ' + secrets.token_hex(8)
ADMIN_PASSWORD = secrets.token_urlsafe(24)
CHECKS = []

def check(condition, message):
    if not condition:
        raise AssertionError(message)
    CHECKS.append(message)
    print('PASS: ' + message, flush=True)

def main():
    if os.environ.get('RUN_INTEGRATION_TESTS') != '1':
        raise SystemExit('Set RUN_INTEGRATION_TESTS=1 to run the isolated integration checks.')
    if not os.environ.get('TEST_DB_NAME', '').endswith('_test'):
        raise SystemExit('TEST_DB_NAME must end with _test. Never use a deployment database.')
    if CONFIG.exists():
        raise SystemExit('Refusing to overwrite config/config.local.php. Use a clean test checkout.')
    env = dict(os.environ, TEST_ADMIN_PASSWORD=ADMIN_PASSWORD)
    php_setup = r'''
    $db = ['host'=>getenv('TEST_DB_HOST') ?: '127.0.0.1', 'port'=>(int)(getenv('TEST_DB_PORT') ?: 3306),
      'name'=>getenv('TEST_DB_NAME'), 'user'=>getenv('TEST_DB_USER'), 'password'=>getenv('TEST_DB_PASSWORD')];
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['name']), $db['user'], $db['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $pdo->exec(file_get_contents('database/schema.sql'));
    $config = ['db'=>$db, 'admin'=>['username'=>'test_admin', 'password_hash'=>password_hash(getenv('TEST_ADMIN_PASSWORD'), PASSWORD_DEFAULT)]];
    umask(0077);
    file_put_contents('config/config.local.php', "<?php\nreturn ".var_export($config,true).";\n", LOCK_EX);
    '''
    setup = subprocess.run(['php', '-r', php_setup], cwd=ROOT, env=env, capture_output=True)
    if setup.returncode:
        CONFIG.unlink(missing_ok=True)
        raise SystemExit('Test setup failed. Verify the isolated test database and PHP extensions.')
    server = None
    try:
        with socket.socket() as listener:
            listener.bind(('127.0.0.1', 0))
            port = listener.getsockname()[1]
        base = f'http://127.0.0.1:{port}/'
        server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', '-t', 'public'], cwd=ROOT, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        jar = http.cookiejar.CookieJar()
        client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
        def request(path, fields=None, opener=client):
            data = urllib.parse.urlencode(fields).encode() if fields is not None else None
            try:
                with opener.open(base + path, data=data, timeout=8) as result:
                    return result.status, result.read().decode(), result.geturl(), result.headers
            except urllib.error.HTTPError as error:
                return error.code, error.read().decode(), error.geturl(), error.headers
        def token(body):
            found = re.search(r'name="csrf" value="([a-f0-9]+)"', body)
            if not found:
                raise AssertionError('Missing CSRF token in rendered form.')
            return found.group(1)
        for _ in range(50):
            try:
                if request('health.php')[0] == 200:
                    break
            except (OSError, urllib.error.URLError):
                pass
            time.sleep(.1)
        status, body, url, headers = request('index.php')
        check(status == 200 and url.endswith('login.php'), 'anonymous client is redirected to login')
        status, body, _, _ = request('login.php', {'csrf': token(body), 'username': 'test_admin', 'password': 'wrong'})
        check(status == 200 and 'chưa đúng' in body, 'wrong credentials are rejected')
        status, body, url, headers = request('login.php', {'csrf': token(body), 'username': 'test_admin', 'password': ADMIN_PASSWORD})
        check(status == 200 and url.endswith('index.php'), 'configured administrator can log in')
        check("default-src 'self'" in headers.get('Content-Security-Policy', ''), 'HTML response includes security headers')
        csrf = token(body)
        fields = {'csrf': csrf, 'action': 'create', 'name': NAME, 'category': 'main', 'price': '45000', 'description': '<script>alert(1)</script>', 'is_available': '1'}
        status, _, _, _ = request('index.php', {**fields, 'csrf': 'invalid'})
        check(status == 403, 'write without a valid CSRF token is rejected')
        status, body, _, _ = request('index.php', {**fields, 'price': '-1'})
        check(status == 200 and 'Chưa lưu được' in body and NAME not in re.sub(r'<form\b.*?</form>', '', body, flags=re.S), 'negative price is rejected before persistence')
        status, body, _, _ = request('index.php', fields)
        match = re.search(r'data-dish-id="(\d+)"[^>]*>.*?' + re.escape(NAME), body, re.S)
        check(status == 200 and match is not None, 'create stores a dish and renders it after redirect')
        dish_id = match.group(1)
        status, body, _, _ = request('index.php?q=' + urllib.parse.quote(NAME))
        check(status == 200 and NAME in body and '45.000 đ' in body, 'reload and search read the stored dish')
        check('&lt;script&gt;alert(1)&lt;/script&gt;' in body and '<script>alert(1)</script>' not in body, 'dish description is escaped when displayed')
        direct_query = r'''
        $cfg=require 'config/config.local.php'; $d=$cfg['db'];
        $p=new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',$d['host'],$d['port'],$d['name']),$d['user'],$d['password']);
        $q=$p->prepare('SELECT price,is_available FROM dishes WHERE name=?');$q->execute([getenv('TEST_DISH_NAME')]);
        echo json_encode($q->fetch(PDO::FETCH_ASSOC));
        '''
        def database_row():
            result = subprocess.run(['php', '-r', direct_query], cwd=ROOT, env=dict(env, TEST_DISH_NAME=NAME), capture_output=True, text=True, check=True)
            return json.loads(result.stdout)
        check(int(database_row()['price']) == 45000, 'independent SQL query confirms the inserted record')
        status, body, _, _ = request('index.php', {**fields, 'action': 'update', 'id': dish_id, 'price': '50000'})
        check(status == 200 and int(database_row()['price']) == 50000, 'update changes the price in MySQL')
        status, body, _, _ = request('index.php', {'csrf': csrf, 'action': 'toggle', 'id': dish_id})
        check(status == 200 and int(database_row()['is_available']) == 0, 'availability toggle persists in MySQL')
        status, body, _, _ = request('index.php?category=drink&q=' + urllib.parse.quote(NAME))
        check(status == 200 and 'data-dish-id=' not in body, 'category filter excludes a dish from another category')
        status, _, _, _ = request('index.php?edit=' + dish_id)
        check(status == 200, 'edit form loads the existing dish')
        status, _, _, _ = request('index.php', {'csrf': csrf, 'action': 'delete', 'id': dish_id})
        check(status == 200 and database_row() is False, 'delete removes the record from MySQL')
        status, _, _, _ = request('index.php', {'csrf': csrf, 'action': 'update', 'id': dish_id})
        check(status == 404, 'updating a missing record returns 404')
        check(request('health.php')[0] == 200, 'health endpoint confirms the configured table is accessible')
        check(request('logout.php')[0] == 405, 'logout only accepts POST')
        status, _, url, _ = request('logout.php', {'csrf': csrf})
        check(status == 200 and url.endswith('login.php'), 'logout ends the authenticated session')
        print(f'{len(CHECKS)} HTTP/MySQL integration checks passed.', flush=True)
    finally:
        cleanup = r'''
        $cfg=require 'config/config.local.php'; $d=$cfg['db'];
        $p=new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',$d['host'],$d['port'],$d['name']),$d['user'],$d['password']);
        $p->prepare('DELETE FROM dishes WHERE name=?')->execute([getenv('TEST_DISH_NAME')]);
        '''
        subprocess.run(['php', '-r', cleanup], cwd=ROOT, env=dict(env, TEST_DISH_NAME=NAME), stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        if server is not None:
            server.terminate()
            server.wait(timeout=10)
        CONFIG.unlink(missing_ok=True)

if __name__ == '__main__':
    main()
