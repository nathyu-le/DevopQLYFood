#!/usr/bin/env python3
"""Check source release boundaries and reject unsafe/mismatched artifacts offline."""
import importlib.util
import io
from pathlib import Path
import tempfile
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
    output = root / 'release.zip'
    packager.package(root, output, revision)
    with zipfile.ZipFile(output) as archive:
        names = deployer.validated_entries(archive, revision)
        assert set(names) == {'app/bootstrap.php', 'public/version.php', 'public/assets/app.css', 'REVISION'}
        assert names[-1] == 'REVISION'
        assert b'PRIVATE_' not in b''.join(archive.read(name) for name in names)
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
