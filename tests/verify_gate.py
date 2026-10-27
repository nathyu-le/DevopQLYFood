#!/usr/bin/env python3
"""Prove that validation rejects a deliberately broken copy of the application."""
from pathlib import Path
import os
import shutil
import subprocess
import tempfile

ROOT = Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='menu-gate-') as temporary:
    target = Path(temporary)
    for folder in ('app', 'tests'):
        shutil.copytree(ROOT / folder, target / folder, ignore=shutil.ignore_patterns('__pycache__'))
    bootstrap = target / 'app/bootstrap.php'
    original = bootstrap.read_text()
    broken = original.replace('[0-9]{0,8}', '[0-9]{0,12}')
    if broken == original:
        raise SystemExit('Cannot inject the expected validation regression.')
    bootstrap.write_text(broken)
    result = subprocess.run(['docker', 'run', '--rm', '--entrypoint', 'php',
                             '--mount', f'type=bind,src={target},dst=/checks,readonly',
                             os.environ['APP_IMAGE'], '/checks/tests/validation.php'],
                            capture_output=True, text=True)
    expected = 'FAIL: reject invalid price: 1000000000'
    if result.returncode != 1 or expected not in result.stderr:
        raise SystemExit('The regression was not rejected by the expected test.')
    print('PASS: deliberately broken price validation exits 1 at the expected assertion.')
    print('The application source and tested image were not changed.')
