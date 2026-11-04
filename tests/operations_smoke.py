#!/usr/bin/env python3
"""Week 3 recovery exercises. Disposable *_ci Compose project and DB only."""
import http.cookiejar
import json
import os
from pathlib import Path
import re
import secrets
import subprocess
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / 'week3-results'
CHECKS = []

def check(condition, message):
    if not condition:
        raise AssertionError(message)
    CHECKS.append(message)
    print('PASS: ' + message, flush=True)

def demo(stage, data):
    """Print selected disposable-fixture results for a live technical demo."""
    print('DEMO_' + stage + ' ' + json.dumps(data, ensure_ascii=False), flush=True)

def demo_rows(values):
    # All rows belong to the freshly created, guarded *_ci database. Still keep
    # the console view to the fields the presenter needs; never print config.
    fields = ('id', 'name', 'price', 'is_available')
    return [{key: row[key] for key in fields} for row in values]

def compose(*args, success=True):
    result = subprocess.run(['docker', 'compose', *args], cwd=ROOT, text=True, capture_output=True)
    if success and result.returncode:
        raise AssertionError('Compose/PHP operation failed; private details omitted.')
    return result

PHP_DB = r'''
$c=require '/var/www/html/config/config.local.php';$d=$c['db'];
if(!preg_match('/_ci\z/',$d['name'])){exit(2);}
$p=new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',$d['host'],$d['port'],$d['name']),$d['user'],$d['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
'''

def php(code, *args):
    r = compose('exec', '-T', '--user', 'www-data', 'app', 'php', '-r', code, *map(str,args))
    return json.loads(r.stdout)

def rows():
    return php(PHP_DB + "require '/var/www/html/app/backup_lib.php'; echo json_encode(backup_rows($p));")

def main():
    if (os.environ.get('RUN_OPERATIONS_TESTS') != '1'
        or not os.environ.get('COMPOSE_PROJECT_NAME', '').endswith('_ci')
        or not os.environ.get('DB_NAME', '').endswith('_ci')):
        raise SystemExit('Only isolated CI project and *_ci DB are allowed.')
    OUT.mkdir(exist_ok=True)
    demo('SCOPE', {'environment': 'isolated Docker staging',
                   'revision': os.environ['APP_REVISION']})
    base='http://127.0.0.1:'+os.environ.get('APP_PORT','8080')+'/'
    password=(ROOT/'.secrets/admin_password').read_text().strip()
    client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(path, fields=None):
        data=urllib.parse.urlencode(fields).encode() if fields is not None else None
        try:
            with client.open(base+path,data=data,timeout=15) as r:return r.status,r.read().decode(),r.geturl()
        except urllib.error.HTTPError as e:return e.code,e.read().decode(),e.geturl()
    def token(body):
        match=re.search(r'name="csrf" value="([a-f0-9]+)"',body)
        if not match:raise AssertionError('Missing form token')
        return match.group(1)
    def state():
        status,body,_=request('ops-api.php')
        check(status==200,'authenticated collector responds while probing real DB')
        return json.loads(body)
    def capture(label):
        subprocess.run(['node','tools/capture-operations.mjs',label],cwd=ROOT,check=True,timeout=45)
    def save(name, data):
        (OUT/name).write_text(json.dumps(data,ensure_ascii=False,indent=2)+'\n')

    check(request('ops-api.php')[0]==401,'anonymous visitors cannot read metrics or logs')
    check(request('ops-api.php',{'action':'backup'})[0]==401,'anonymous backup requests are rejected')
    _,body,_=request('login.php')
    status,body,url=request('login.php',{'csrf':token(body),'username':'admin','password':password})
    check(status==200 and url.endswith('index.php'),'staging administrator can authenticate')
    csrf=token(body)
    check(request('ops-api.php',{'action':'backup','csrf':'invalid'})[0]==403,'CSRF blocks backup writes')
    check(request('ops-api.php',{'action':'restore','csrf':csrf})[0]==400,'restore has no HTTP control surface')
    check(php(PHP_DB+'echo json_encode(count($p->query("SELECT id FROM dishes")->fetchAll())===0);'),
          'recovery exercise starts with an empty disposable database')
    name='Week3 recovery '+secrets.token_hex(6)
    dish_id=php(PHP_DB+'$q=$p->prepare("INSERT INTO dishes (name,category,price,description,is_available) VALUES (?,\'main\',45000,\'Isolated recovery fixture\',1)");$q->execute([$argv[1]]);echo json_encode((int)$p->lastInsertId());',name)
    before=rows()
    demo('BEFORE_DELETE', {'rows': demo_rows(before)})
    created=compose('exec','-T','--user','www-data','app','php','app/backup.php','--keep=14')
    backup=json.loads(created.stdout)
    check(backup['row_count']==1,'backup script captures all test rows with SHA256')
    demo('BACKUP_CREATED', {'row_count': backup['row_count'],
                            'sha256': backup['sha256']})
    permissions=php('require "/var/www/html/app/backup_lib.php";$f=backup_directory()."/".$argv[1];echo json_encode(["file"=>fileperms($f)&0777,"directory"=>fileperms(backup_directory())&0777]);',backup['file'])
    check(permissions=={'file':384,'directory':448},'backup file mode 0600 and directory mode 0700 are enforced')
    check(request('var/backups/'+backup['file'])[0] in (403,404),'private backups cannot be downloaded over HTTP')
    php(PHP_DB+'$q=$p->prepare("DELETE FROM dishes WHERE id=? AND name=?");$q->execute([$argv[1],$argv[2]]);echo json_encode(true);',dish_id,name)
    after_delete=rows()
    check(after_delete==[],'only the named test record is deleted to simulate data loss')
    demo('AFTER_DELETE', {'rows': demo_rows(after_delete)})
    wrong=compose('exec','-T','--user','www-data','app','php','app/restore.php','--file='+backup['file'],'--confirm-db=wrong_ci',success=False)
    check(wrong.returncode!=0 and rows()==[],'wrong database confirmation is refused without writing')
    tampered=php('require "/var/www/html/app/backup_lib.php";$f=backup_directory()."/".$argv[1];$a=json_decode(file_get_contents($f),true);$a["sha256"]=str_repeat("0",64);$name="menu-20000101-000000-deadbeef.json";file_put_contents(backup_directory()."/".$name,json_encode($a));echo json_encode($name);',backup['file'])
    bad=compose('exec','-T','--user','www-data','app','php','app/restore.php','--file='+tampered,'--confirm-db='+os.environ['DB_NAME'],success=False)
    check(bad.returncode!=0 and rows()==[],'corrupt snapshot is refused before any DELETE or INSERT')
    restored=compose('exec','-T','--user','www-data','app','php','app/restore.php','--file='+backup['file'],'--confirm-db='+os.environ['DB_NAME'])
    after=rows()
    check(json.loads(restored.stdout)['verified'] and before==after,'restore recreates exact IDs values and timestamps after deletion')
    demo('AFTER_RESTORE', {'rows': demo_rows(after), 'all_rows_match': before==after})
    save('backup-restore.json',{'scope':'isolated Docker staging test database, not cPanel','revision':os.environ['APP_REVISION'],
        'before':before,'after_delete':[],'after_restore':after,'all_rows_match':before==after,
        'sha256':backup['sha256'],'permissions':permissions,'wrong_confirmation_blocked':True,'corruption_blocked':True})
    for _ in range(3):
        compose('exec','-T','--user','www-data','app','php','app/backup.php','--keep=2');time.sleep(1.05)
    count=php('require "/var/www/html/app/backup_lib.php";echo json_encode(count(glob(backup_directory()."/menu-*.json")));')
    check(count==2,'retention removes only old managed snapshots and keeps two newest in the exercise')
    ddl=php(PHP_DB+'try{$p->exec("CREATE TABLE week3_forbidden_probe (id INT)");echo json_encode(false);}catch(PDOException $e){echo json_encode(true);}')
    check(ddl,'application database user cannot create schema objects')
    fallback=compose('exec','-T','--user','www-data','app','php','-d','open_basedir=/var/www/html','-r',
        'require "app/operations.php";echo json_encode(ops_resources([]));')
    fallback=json.loads(fallback.stdout)
    check(fallback['cpu_percent'] is None and fallback['memory_source']=='PHP allocation, not account RAM',
          'restricted hosting falls back honestly without warning output or invented CPU values')
    for _ in range(3): state();time.sleep(5.2)
    normal=state()
    check(normal['latest']['cpu_percent'] is not None and normal['latest']['memory_mb']>0,'CPU and RAM samples come from real OS/cgroup measurements')
    check(normal['latest']['health']=='ok' and normal['alerts']['database_unavailable']['state']=='ok','baseline database is healthy with no database alert')
    demo('ALERT_NORMAL', {'health': normal['latest']['health'],
                          'state': normal['alerts']['database_unavailable']['state']})
    capture('normal')
    outage = None
    try:
        compose('stop','db')
        outage_status=request('health.php')[0]
        check(outage_status==503,'stopping staging MariaDB causes a real HTTP 503')
        for _ in range(3):time.sleep(5.2);outage=state()
        check(outage['alerts']['database_unavailable']['state']=='firing','two consecutive failed probes activate the database alert')
        demo('ALERT_FIRING', {'http_status': outage_status,
                              'health': outage['latest']['health'],
                              'state': outage['alerts']['database_unavailable']['state']})
        check(any(x.get('event')=='database_unavailable' for x in outage['logs']),'timestamped application logs record the database incident')
        capture('firing')
    finally:
        compose('up','-d','--no-build','--wait','--wait-timeout','180','db')
    for _ in range(3):time.sleep(5.2);recovered=state()
    recovered_status=request('health.php')[0]
    check(recovered_status==200 and recovered['alerts']['database_unavailable']['state']=='ok','restarting DB restores HTTP health and resolves the alert')
    demo('ALERT_RECOVERED', {'http_status': recovered_status,
                            'health': recovered['latest']['health'],
                            'state': recovered['alerts']['database_unavailable']['state']})
    check(rows()==before,'incident recovery preserves restored data')
    request('index.php?q=W3_SECRET_LOG_PROBE')
    final=state()
    serialized=json.dumps(final)
    for secret in [password,(ROOT/'.secrets/db_password').read_text().strip(),'W3_SECRET_LOG_PROBE']:
        check(secret not in serialized,'metrics and logs omit credentials and request query content')
    capture('recovered')
    save('alert-lifecycle.json',{'scope':'isolated Docker staging, not live hosting','revision':os.environ['APP_REVISION'],
        'normal':normal['latest'],'firing':{'latest':outage['latest'],'alert':outage['alerts']['database_unavailable']},
        'recovered':{'latest':recovered['latest'],'alert':recovered['alerts']['database_unavailable']},
        'transitions':[x for x in final['logs'] if x.get('event')=='alert_transition']})
    save('application-events.json',final['logs'])
    save('checks.json',{'checks':CHECKS,'count':len(CHECKS),'scope':'isolated Docker staging','revision':os.environ['APP_REVISION']})
    print(f'{len(CHECKS)} Week 3 checks passed; outage alert and restore verified.',flush=True)

if __name__=='__main__':main()
