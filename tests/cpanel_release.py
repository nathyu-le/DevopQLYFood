#!/usr/bin/env python3
"""Check source release boundaries and reject unsafe/mismatched artifacts offline."""
import importlib.util
import io
import ftplib
from pathlib import Path
import ssl
import tempfile
import urllib.error
from unittest.mock import Mock, patch
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
        'app/operations.php': '<?php // dependency', 'public/operations.php': '<?php // dashboard',
        'public/index.php': '<?php // menu',
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
        assert set(names) == {'app/bootstrap.php', 'app/operations.php', 'public/operations.php', 'public/index.php', 'public/version.php', 'public/assets/app.css', 'REVISION'}
        assert names.index('app/operations.php') < names.index('app/bootstrap.php') < names.index('public/operations.php') < names.index('public/index.php')
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

# A provider can route its connection alias to a service whose certificate uses
# its established canonical name. Both TLS channels must verify that identity.
identity = 'service.example.test'
context = ssl.create_default_context()
assert context.check_hostname and context.verify_mode == ssl.CERT_REQUIRED
ftp = ftplib.FTP_TLS(context=context)
with patch.object(ftp, 'connect') as connect:
    deployer.connect_ftps_control(ftp, 'alias.example.test', 21, identity)
    connect.assert_called_once_with('alias.example.test', 21)
assert ftp.host == identity
ftp.sock = Mock()
with patch.object(ftp, 'voidcmd', return_value='234 Ready'), \
     patch.object(ssl.SSLContext, 'wrap_socket', return_value=Mock()) as wrap:
    ftp.auth()
    assert wrap.call_args.kwargs['server_hostname'] == identity
ftp._prot_p = True
with patch.object(ftplib.FTP, 'ntransfercmd', return_value=(Mock(), 1)), \
     patch.object(ssl.SSLContext, 'wrap_socket', return_value=Mock()) as wrap:
    ftp.ntransfercmd('STOR fixture.txt')
    assert wrap.call_args.kwargs['server_hostname'] == identity
ftp.close()

ftp = ftplib.FTP_TLS(context=context)
ftp.sock = Mock()
ftp.host = identity
with patch.object(ftp, 'putcmd') as command, \
     patch.object(ftp, 'getresp', return_value='234 Ready'), \
     patch.object(ssl.SSLContext, 'wrap_socket',
                  side_effect=ssl.SSLCertVerificationError('hostname mismatch')):
    try:
        ftp.login('fixture_user', 'fixture_password')
        raise AssertionError('Invalid certificate accepted.')
    except ssl.SSLCertVerificationError:
        pass
    assert [call.args[0] for call in command.call_args_list] == ['AUTH TLS']
ftp.close()
print('PASS: connection aliases preserve certificate verification on control/data channels; invalid TLS sends no credentials.')
