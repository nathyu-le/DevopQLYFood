#!/usr/bin/env python3
"""Exercise the browser demo API and prove the MariaDB menu stays unchanged."""
import http.cookiejar
import json
import os
from pathlib import Path
import re
import subprocess
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT=Path(__file__).resolve().parents[1]
if os.environ.get('RUN_OPERATIONS_TESTS')!='1' or not os.environ.get('COMPOSE_PROJECT_NAME','').endswith('_ci'):
    raise SystemExit('Run only on the disposable staging stack.')
base='http://127.0.0.1:'+os.environ.get('APP_PORT','8080')+'/'
client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
def request(path,fields=None):
    data=urllib.parse.urlencode(fields).encode() if fields is not None else None
    try:
        with client.open(base+path,data=data,timeout=15) as r:return r.status,r.read().decode(),r.geturl()
    except urllib.error.HTTPError as e:return e.code,e.read().decode(),e.geturl()
def check(value,message):
    if not value:raise AssertionError(message)
    print('PASS: '+message,flush=True)
def php(code):
    p=subprocess.run(['docker','compose','exec','-T','--user','www-data','app','php','-r',code],cwd=ROOT,text=True,capture_output=True)
    if p.returncode:raise AssertionError('Private demo storage check failed; details omitted.')
    return p.stdout
main_before=php('require "app/backup_lib.php";[$p]=backup_connection();echo backup_digest(backup_rows($p));')
check(request('demo-api.php')[0]==401,'anonymous access to demo controls is rejected')
check(request('demo-health.php')[0]==401,'demo health requires an administrator')
_,body,_=request('login.php');csrf=re.search(r'name="csrf" value="([a-f0-9]+)"',body).group(1)
password=(ROOT/'.secrets/admin_password').read_text().strip()
check(request('login.php',{'csrf':csrf,'username':'admin','password':password})[0]==200,'administrator signs in for the interactive demo')
_,body,_=request('demo.php');csrf=re.search(r'id="demo-csrf" value="([a-f0-9]+)"',body).group(1)
check(request('demo-api.php',{'csrf':'invalid','action':'prepare'})[0]==403,'CSRF blocks demo state changes')
def act(action):
    code,body,_=request('demo-api.php',{'csrf':csrf,'action':action})
    check(code==200,'demo action '+action+' succeeds')
    return json.loads(body)
def state():return json.loads(request('demo-api.php')[1])
check(request('demo-api.php',{'csrf':csrf,'action':'arbitrary'})[0]==400,'only allowlisted demo actions are accepted')
s=act('prepare');before=s['probe']['rows']
check(s['driver_available'] and len(before)==1,'one fixture is created in a real private SQLite database')
check(request('demo-api.php',{'csrf':csrf,'action':'delete'})[0]==409,'fixture deletion requires a matching backup first')
s=act('backup');check(s['backup']['row_count']==1,'interactive backup records the fixture')
archive=act('download');check(archive['rows']==before and archive['format']=='devopqlyfood-isolated-demo-v1','download exports only the current isolated demo snapshot')
check(request('var/classroom-demo/latest-backup.json')[0] in (403,404),'private snapshot is blocked over public HTTP')
s=act('delete');check(s['probe']['rows']==[],'demo fixture is actually removed from SQLite')
php('$p="var/classroom-demo/latest-backup.json";$a=json_decode(file_get_contents($p),true);$a["sha256"]=str_repeat("0",64);file_put_contents($p,json_encode($a));')
check(request('demo-api.php',{'csrf':csrf,'action':'restore'})[0]==409 and state()['probe']['rows']==[],'corrupt snapshot is rejected before restoration')
# Restore the same validated archive exported above, without using a public upload surface.
encoded=json.dumps(archive,ensure_ascii=False).encode().hex()
php('file_put_contents("var/classroom-demo/latest-backup.json",hex2bin("'+encoded+'"));')
s=act('restore');check(s['restored'] and s['probe']['rows']==before,'restore reproduces exact fixture data and timestamps')
s=act('disconnect');check(request('demo-health.php')[0]==503,'disconnecting the private SQLite file causes an actual demo HTTP 503')
time.sleep(5.3);s=state();check(s['alert']['state']=='firing','two failed samples activate the demo alert')
s=act('reconnect');check(request('demo-health.php')[0]==200,'reconnecting the database restores actual demo HTTP 200')
time.sleep(5.3);s=state();check(s['alert']['state']=='ok' and s['probe']['rows']==before,'two healthy samples clear the alert and preserve restored data')
main_after=php('require "app/backup_lib.php";[$p]=backup_connection();echo backup_digest(backup_rows($p));')
check(main_before==main_after,'all interactive demo actions leave the MariaDB menu unchanged')
check(request('health.php')[0]==200,'the main website remains healthy during the isolated exercise')
print('Interactive demo backup restore outage and recovery checks passed.',flush=True)
