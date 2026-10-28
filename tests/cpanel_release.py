#!/usr/bin/env python3
"""Check source release boundaries and reject unsafe/mismatched artifacts offline."""
import importlib.util
import io
from pathlib import Path
import tempfile
import urllib.error
from unittest.mock import patch
import zipfile

ROOT = Path(__file__).resolve().parents[1]

def module(name, filename):
    spec = importlib.util.spec_from_file_location(name, ROOT / 'tools' / filename)
    result = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(result)
    return result

packager = module('packager', 'package-cpanel.py')
deployer = module('deployer', 'deploy-cpanel.py')
revision = 'a' * 40
with tempfile.TemporaryDirectory() as directory:
    root = Path(directory)
    fixtures = {
        'app/bootstrap.php': '<?php // app', 'public/version.php': '<?php // version',
        'public/assets/app.css': 'body {}', 'config/config.local.php': 'PRIVATE_CONFIG',
        '.secrets/password': 'PRIVATE_PASSWORD', 'database/schema.sql': 'PRIVATE_DB',
        '.htaccess': 'EXISTING_HOSTING_RULES', 'README.md': 'not runtime',
    }
    for name, data in fixtures.items():
        path = root / name; path.parent.mkdir(parents=True, exist_ok=True); path.write_text(data)
    # Reproduce a restrictive source mode; deployment assets must still be 0644.
    (root / 'public/assets/app.css').chmod(0o600)
    output = root / 'release.zip'
    packager.package(root, output, revision)
    with zipfile.ZipFile(output) as archive:
        names = deployer.validated_entries(archive, revision)
        assert set(names) == {'app/bootstrap.php', 'public/version.php', 'public/assets/app.css', 'REVISION'}
        assert names[-1] == 'REVISION'
        assert b'PRIVATE_' not in b''.join(archive.read(name) for name in names)
        assert all((archive.getinfo(name).external_attr >> 16) & 0o777 == 0o644 for name in names)
        assert (root / 'public/assets/app.css').stat().st_mode & 0o777 == 0o600
        try:
            deployer.validated_entries(archive, 'b' * 40)
            raise AssertionError('Mismatched revision accepted.')
        except ValueError:
            pass
    for forbidden in ('config/config.local.php', '../escape.php', '/app/evil.php'):
        content = io.BytesIO()
        with zipfile.ZipFile(content, 'w') as archive:
            for name, value in {'app/bootstrap.php': '', 'public/version.php': '', 'REVISION': revision, forbidden: ''}.items():
                archive.writestr(name, value)
        with zipfile.ZipFile(content) as archive:
            try:
                deployer.validated_entries(archive, revision)
                raise AssertionError('Unsafe artifact accepted: ' + forbidden)
            except ValueError:
                pass
print('PASS: cPanel package excludes config, secrets, database and .htaccess.')
print('PASS: mismatched revisions and unsafe archive paths are rejected before upload.')
print('PASS: all runtime files and REVISION use archive mode 0644, even with source mode 0600.')

def response(payload):
    stream = io.BytesIO(payload)
    stream.status = 200
    return stream

with patch.object(deployer.urllib.request, 'urlopen', return_value=response(b'{"status":"ok"}')) as fetch:
    deployer.check_site('https://example.test/', stage='pre-deploy health')
    request = fetch.call_args.args[0]
    assert request.get_header('User-agent').startswith('DevopQLYFood-CI/')
    assert request.get_header('Accept') == 'application/json'
for status in (403, 404, 503):
    error = urllib.error.HTTPError('https://example.test/', status, 'PRIVATE_CREDENTIAL', {}, io.BytesIO(b'PRIVATE_RESPONSE'))
    with patch.object(deployer.urllib.request, 'urlopen', side_effect=error):
        try:
            deployer.check_site('https://example.test/', stage='pre-deploy health')
            raise AssertionError('Unhealthy site accepted.')
        except deployer.SiteCheckError as failure:
            assert f'pre-deploy health: HTTP {status}' in str(failure)
            assert 'PRIVATE_' not in str(failure)
for payload in (b'<html>PRIVATE_RESPONSE</html>', b'{"status":"unavailable"}', b'[]'):
    with patch.object(deployer.urllib.request, 'urlopen', return_value=response(payload)):
        try:
            deployer.check_site('https://example.test/', stage='pre-deploy health')
            raise AssertionError('Invalid health response accepted.')
        except deployer.SiteCheckError as failure:
            assert 'PRIVATE_' not in str(failure)
with patch.object(deployer.urllib.request, 'urlopen', return_value=response(b'{"revision":"wrong"}')):
    try:
        deployer.check_site('https://example.test/', revision, stage='post-deploy revision')
        raise AssertionError('Wrong deployed revision accepted.')
    except deployer.SiteCheckError as failure:
        assert 'post-deploy revision' in str(failure)
print('PASS: HTTP gates reject 403/404/503, invalid JSON/health and wrong revisions; logs contain only stage/status.')
