#!/usr/bin/env python3
"""Package runtime source only; keep cPanel credentials, DB and .htaccess intact."""
from pathlib import Path
import os
import re
import stat
import zipfile

ROOT = Path(__file__).resolve().parents[1]

def package(root, output, revision):
    if not re.fullmatch(r'[0-9a-f]{40}', revision):
        raise ValueError('A full commit SHA is required.')
    files = []
    for directory in ('app', 'public'):
        for path in sorted((root / directory).rglob('*')):
            if path.is_symlink():
                raise ValueError('Symlinks are not allowed in the source package.')
            if path.is_file() and path.suffix in ('.php', '.css', '.js'):
                files.append(path)
    if not (root / 'app/bootstrap.php') in files or not (root / 'public/version.php') in files:
        raise ValueError('Required application files are missing.')
    output.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(output, 'w', zipfile.ZIP_DEFLATED) as archive:
        contents = [(path.relative_to(root).as_posix(), path.read_bytes()) for path in files]
        contents.append(('REVISION', (revision + '\n').encode()))
        for name, data in contents:
            # Static files must remain readable by the hosting web server,
            # regardless of checkout umask or ZipFile.writestr's 0600 default.
            info = zipfile.ZipInfo(name)
            info.create_system = 3
            info.external_attr = (stat.S_IFREG | 0o644) << 16
            info.compress_type = zipfile.ZIP_DEFLATED
            archive.writestr(info, data)
    return len(files) + 1

if __name__ == '__main__':
    count = package(ROOT, ROOT / 'dist/cpanel-release.zip', os.environ['APP_REVISION'])
    print(f'Packaged {count} runtime files for cPanel; credentials, DB and .htaccess excluded.')
