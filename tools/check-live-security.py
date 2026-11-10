#!/usr/bin/env python3
"""Read-only public route checks, without reading private response bodies."""
import json
import os
from pathlib import Path
import urllib.error
import urllib.parse
import urllib.request

base=os.environ['CPANEL_BASE_URL'].rstrip('/')+'/'
u=urllib.parse.urlsplit(base)
if u.scheme!='https' or not u.hostname or u.username or u.password or u.query or u.fragment:raise SystemExit('Invalid HTTPS base URL.')
headers={'User-Agent':'DevopQLYFood-CI/1.0 (+https://github.com/nathyu-le/DevopQLYFood)','Cache-Control':'no-cache'}
checks=[]
def probe(url):
    try:
        with urllib.request.urlopen(urllib.request.Request(url,headers=headers),timeout=20) as r:return r.status,r.geturl()
    except urllib.error.HTTPError as e:return e.code,url
for route in ['assets/operations.css','assets/operations.js','assets/demo.css','assets/demo.js']:
    status,_=probe(base+route);assert status==200,route+' is not available';checks.append({'route':route,'status':status})
for route in ['ops-api.php','demo-api.php','demo-health.php']:
    status,_=probe(base+route);assert status==401,'Private API must require admin authentication';checks.append({'route':route,'status':status})
for route in ['operations.php','demo.php','week4.php']:
    status,end=probe(base+route);assert status==200 and urllib.parse.urlsplit(end).path.endswith('/login.php'),'Administrator page authentication redirect failed';checks.append({'route':route,'anonymous_redirect':'login.php'})
if u.path.endswith('/public/'):
    parent=base[:-len('public/')]
    for route in ['var/operations/state.json','var/classroom-demo/latest-backup.json','config/config.local.php','database/schema.sql']:
        status,_=probe(parent+route);assert status in (403,404),'Private project route is exposed';checks.append({'route':route,'status':status})
Path('live-security-results').mkdir(exist_ok=True)
Path('live-security-results/checks.json').write_text(json.dumps({'scope':'live cPanel HTTP access controls, not OS firewall','revision':os.environ.get('APP_REVISION'),'checks':checks},indent=2)+'\n')
print(f'{len(checks)} live cPanel security route checks passed; no credentials or private response bodies read.')
