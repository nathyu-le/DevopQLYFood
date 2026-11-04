#!/usr/bin/env python3
"""Public HTTPS health probe. No administrator credentials or response bodies in logs."""
import json
import os
from pathlib import Path
import time
import urllib.error
import urllib.parse
import urllib.request

base=os.environ.get('CPANEL_BASE_URL','').rstrip('/')+'/'
url=urllib.parse.urlsplit(base)
if url.scheme!='https' or not url.hostname or url.username or url.password or url.query or url.fragment:
    raise SystemExit('Configure a valid public HTTPS base URL.')
started=time.monotonic();status=None;healthy=False
try:
    request=urllib.request.Request(base+'health.php',headers={'User-Agent':'DevopQLYFood-CI/1.0 (+https://github.com/nathyu-le/DevopQLYFood)','Accept':'application/json','Cache-Control':'no-cache'})
    with urllib.request.urlopen(request,timeout=20) as response:
        status=response.status;payload=json.load(response)
        healthy=status==200 and isinstance(payload,dict) and payload.get('status')=='ok'
except urllib.error.HTTPError as error:status=error.code
except (urllib.error.URLError,TimeoutError,ValueError):pass
result={'time':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),'http_status':status,'health':'ok' if healthy else 'unavailable','elapsed_ms':round((time.monotonic()-started)*1000,2)}
Path('uptime-results').mkdir(exist_ok=True)
Path('uptime-results/probe.json').write_text(json.dumps(result,indent=2)+'\n')
print(json.dumps(result))
raise SystemExit(0 if healthy else 1)
