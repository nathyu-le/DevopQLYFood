#!/usr/bin/env python3
"""Package runtime source only; keep cPanel credentials, DB and .htaccess intact."""
from pathlib import Path
import os
import re
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
        for path in files:
            archive.write(path, path.relative_to(root).as_posix())
        archive.writestr('REVISION', revision + '\n')
    return len(files) + 1

if __name__ == '__main__':
    count = package(ROOT, ROOT / 'dist/cpanel-release.zip', os.environ['APP_REVISION'])
    print(f'Packaged {count} runtime files for cPanel; credentials, DB and .htaccess excluded.')
