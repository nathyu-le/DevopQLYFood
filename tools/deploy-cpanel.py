#!/usr/bin/env python3
"""Deploy an exact-commit source ZIP to an existing cPanel app using explicit FTPS.

No shell, Docker daemon or cPanel password is needed on the hosting account.
The provider must enable FTPS. Until credentials are configured, this stays off.
"""
import ftplib
import io
import json
import os
from pathlib import PurePosixPath
import re
import ssl
import time
import urllib.error
import urllib.parse
import urllib.request
import zipfile

def validated_entries(archive, revision):
    if not re.fullmatch(r'[0-9a-f]{40}', revision):
        raise ValueError('A full deployment commit SHA is required.')
    names = archive.namelist()
    if len(names) != len(set(names)) or not {'app/bootstrap.php', 'public/version.php', 'REVISION'} <= set(names):
        raise ValueError('Invalid release manifest.')
    for name in names:
        path = PurePosixPath(name)
        if path.is_absolute() or '..' in path.parts or '\\' in name:
            raise ValueError('Unsafe archive path.')
        if name != 'REVISION' and not (
            len(path.parts) >= 2 and path.parts[0] in ('app', 'public')
            and path.suffix in ('.php', '.css', '.js')
        ):
            raise ValueError('Unexpected file in the release.')
    if archive.read('REVISION').decode().strip() != revision:
        raise ValueError('Artifact revision does not match the workflow commit.')
    return sorted(name for name in names if name != 'REVISION') + ['REVISION']

class SiteCheckError(RuntimeError):
    """A safe, controlled diagnostic that contains no response body or secrets."""

def check_site(base, revision=None, stage='site check'):
    suffix = 'health.php' if revision is None else 'version.php'
    # Avoid a proxy serving a stale version response.
    request = urllib.request.Request(base + suffix + '?check=' + str(time.time_ns()),
                                     headers={
                                         'Cache-Control': 'no-cache',
                                         'Accept': 'application/json',
                                         'User-Agent': 'DevopQLYFood-CI/1.0 (+https://github.com/nathyu-le/DevopQLYFood)',
                                     })
    try:
        with urllib.request.urlopen(request, timeout=20) as response:
            if response.status != 200:
                raise SiteCheckError(f'{stage}: HTTP {response.status}.')
            try:
                result = json.load(response)
            except ValueError:
                raise SiteCheckError(f'{stage}: HTTP 200, but the response is not JSON.') from None
    except urllib.error.HTTPError as error:
        raise SiteCheckError(f'{stage}: HTTP {error.code}.') from None
    except urllib.error.URLError:
        raise SiteCheckError(f'{stage}: unable to reach the HTTPS endpoint.') from None
    if not isinstance(result, dict):
        raise SiteCheckError(f'{stage}: unexpected JSON structure.')
    key, expected = ('status', 'ok') if revision is None else ('revision', revision)
    if result.get(key) != expected:
        raise SiteCheckError(f'{stage}: expected health status or commit revision does not match.')

def main():
    required = ('CPANEL_FTP_HOST', 'CPANEL_FTP_USER', 'CPANEL_FTP_PASSWORD',
                'CPANEL_FTP_ROOT', 'CPANEL_BASE_URL', 'APP_REVISION')
    if any(not os.environ.get(key) for key in required):
        raise ValueError('Missing required cPanel deployment settings.')
    revision = os.environ['APP_REVISION']
    base = os.environ['CPANEL_BASE_URL'].rstrip('/') + '/'
    url = urllib.parse.urlsplit(base)
    if url.scheme != 'https' or not url.hostname or url.username or url.password or url.query or url.fragment:
        raise ValueError('CPANEL_BASE_URL must be a clean HTTPS URL.')
    remote_root = os.environ['CPANEL_FTP_ROOT']
    if not remote_root.startswith('/') or any(c in remote_root for c in ('\r', '\n', '\\')) or '..' in PurePosixPath(remote_root).parts:
        raise ValueError('Use an absolute FTP directory without parent traversal.')
    check_site(base, stage='pre-deploy health')
    print('Pre-deploy HTTP health check passed; starting FTPS transfer.', flush=True)
    with zipfile.ZipFile('dist/cpanel-release.zip') as archive:
        names = validated_entries(archive, revision)
        with ftplib.FTP_TLS(context=ssl.create_default_context(), timeout=30) as ftp:
            ftp.connect(os.environ['CPANEL_FTP_HOST'], int(os.environ.get('CPANEL_FTP_PORT') or '21'))
            ftp.login(os.environ['CPANEL_FTP_USER'], os.environ['CPANEL_FTP_PASSWORD'])
            ftp.prot_p()  # Encrypt the data channel too; never fall back to plain FTP.
            ftp.cwd(remote_root)
            origin = ftp.pwd()
            for directory in ('app', 'public', 'config'):
                ftp.cwd(origin)
                ftp.cwd(directory)
            ftp.cwd(origin)
            ftp.voidcmd('TYPE I')
            if ftp.size('config/config.local.php') is None:
                raise ValueError('Existing hosting DB/admin config is required and will be preserved.')
            for name in names:
                parent = PurePosixPath(name).parent
                ftp.cwd(origin)
                for part in parent.parts:
                    if part == '.':
                        continue
                    try:
                        ftp.cwd(part)
                    except ftplib.error_perm:
                        ftp.mkd(part)
                        ftp.cwd(part)
                filename = PurePosixPath(name).name
                temporary = f'.deploy-{revision[:12]}-{filename}'
                ftp.storbinary('STOR ' + temporary, io.BytesIO(archive.read(name)))
                ftp.rename(temporary, filename)
            ftp.quit()
    print('FTPS source transfer completed; checking the deployed website.', flush=True)
    check_site(base, stage='post-deploy health')
    check_site(base, revision, stage='post-deploy revision')
    print('cPanel deployment passed HTTP health and exact commit revision checks.')
    print('Existing DB configuration, database and .htaccess were preserved.')

if __name__ == '__main__':
    try:
        main()
    except SiteCheckError as error:
        raise SystemExit('cPanel deployment stopped: ' + str(error))
    except Exception as error:
        # FTP replies may contain account details. Keep error messages out of logs.
        raise SystemExit('cPanel deployment failed (' + type(error).__name__ + '). Check FTPS settings and the existing app; no deploy success is claimed.')
