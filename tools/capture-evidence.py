#!/usr/bin/env python3
"""Capture a real anonymous login page and its health/revision for Week 2."""
import argparse
import json
from pathlib import Path
import shutil
import struct
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

AGENT = 'DevopQLYFood-CI/1.0 (+https://github.com/nathyu-le/DevopQLYFood)'

def fetch(url):
    request = urllib.request.Request(url + '?evidence=' + str(time.time_ns()),
                                     headers={'User-Agent': AGENT, 'Cache-Control': 'no-cache'})
    for attempt in range(3):
        try:
            with urllib.request.urlopen(request, timeout=15) as response:
                return response.status, response.read(250000)
        except urllib.error.HTTPError as error:
            if error.code not in (502, 503, 504, 520, 521, 522, 523, 524) or attempt == 2:
                raise RuntimeError(f'Evidence endpoint returned HTTP {error.code}.') from None
            time.sleep(attempt + 1)
    raise RuntimeError('Evidence endpoint could not be checked.')

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--base-url', required=True)
    parser.add_argument('--label', required=True, choices=['live-before', 'staging', 'live-after'])
    parser.add_argument('--output-dir', required=True)
    parser.add_argument('--expected-revision')
    arguments = parser.parse_args()
    base = arguments.base_url.rstrip('/') + '/'
    url = urllib.parse.urlsplit(base)
    if url.username or url.password or url.query or url.fragment or not url.hostname:
        raise SystemExit('Use a base URL without credentials or query parameters.')
    if url.scheme != 'https' and not (url.scheme == 'http' and url.hostname in ('localhost', '127.0.0.1')):
        raise SystemExit('Only HTTPS or local HTTP is allowed.')
    _, health_body = fetch(base + 'health.php')
    _, version_body = fetch(base + 'version.php')
    _, login_body = fetch(base + 'login.php')
    health, version = json.loads(health_body), json.loads(version_body)
    if health.get('status') != 'ok':
        raise SystemExit('Cannot capture evidence from an unhealthy app.')
    revision = version.get('revision')
    if arguments.expected_revision and revision != arguments.expected_revision:
        raise SystemExit('The app revision does not match the tested image/commit.')
    html = login_body.decode('utf-8', 'replace')
    if 'Vào bếp thôi.' not in html or 'assets/app.css' not in html:
        raise SystemExit('The response is not the expected login page.')
    chrome = shutil.which('google-chrome') or shutil.which('chromium')
    if not chrome:
        raise SystemExit('Chrome is required to capture the browser screenshot.')
    destination = Path(arguments.output_dir).resolve()
    destination.mkdir(parents=True, exist_ok=True)
    screenshot = destination / (arguments.label + '-login.png')
    with tempfile.TemporaryDirectory(prefix='menu-chrome-') as profile:
        result = subprocess.run([
            chrome, '--headless', '--disable-dev-shm-usage', '--hide-scrollbars',
            '--window-size=1440,1000', '--force-device-scale-factor=1',
            '--timeout=12000', '--virtual-time-budget=4000',
            '--user-data-dir=' + profile, '--user-agent=' + AGENT,
            '--screenshot=' + str(screenshot), base + 'login.php',
        ], capture_output=True, timeout=40)
        if result.returncode != 0 or not screenshot.is_file():
            raise SystemExit('Browser screenshot failed; no screenshot success is claimed.')
    data = screenshot.read_bytes()
    if data[:8] != b'\x89PNG\r\n\x1a\n' or len(data) < 10000:
        raise SystemExit('The browser did not produce a valid evidence PNG.')
    width, height = struct.unpack('>II', data[16:24])
    metadata = {'label': arguments.label, 'base_url': base, 'health': 'ok',
                'revision': revision, 'screenshot': screenshot.name,
                'width': width, 'height': height, 'captured_at_unix': int(time.time()),
                'authenticated': False}
    (destination / (arguments.label + '-check.json')).write_text(
        json.dumps(metadata, ensure_ascii=False, indent=2) + '\n')
    print(f'Captured {arguments.label}: health ok, revision {revision}, PNG {width}x{height}.')

if __name__ == '__main__':
    main()
