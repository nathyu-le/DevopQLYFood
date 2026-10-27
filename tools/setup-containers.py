#!/usr/bin/env python3
"""Create local Docker configuration without overwriting existing credentials."""
from pathlib import Path
import os
import secrets

ROOT = Path(__file__).resolve().parents[1]

def main():
    previous = os.umask(0o077)
    try:
        directory = ROOT / '.secrets'
        directory.mkdir(mode=0o700, exist_ok=True)
        for name in ('db_password', 'db_root_password', 'admin_password'):
            path = directory / name
            try:
                with path.open('x', encoding='utf-8') as handle:
                    handle.write(secrets.token_urlsafe(32) + '\n')
            except FileExistsError:
                if not path.read_text().strip():
                    raise SystemExit(f'{name} is empty; repair it before starting Docker.')
        target = ROOT / '.env'
        try:
            with target.open('x', encoding='utf-8') as handle:
                handle.write((ROOT / '.env.example').read_text())
        except FileExistsError:
            pass
    finally:
        os.umask(previous)
    print('Docker configuration ready. Existing files were preserved.')
    print('Default admin username: admin; password file: .secrets/admin_password')
    print('Keep .env and .secrets/ outside Git and outside screenshots/logs.')

if __name__ == '__main__':
    main()
