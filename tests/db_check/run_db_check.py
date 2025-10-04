#!/usr/bin/env python3
"""
Run the PHP DB check and print a human summary.
Usage: python run_db_check.py <scheduleid>
"""
import json
import subprocess
import sys
import os

if len(sys.argv) < 2:
    print("Usage: python run_db_check.py <scheduleid>")
    sys.exit(2)

scheduleid = sys.argv[1]
php_script = os.path.join(os.path.dirname(__file__), 'verify_apponum.php')

proc = subprocess.run(['php', php_script, scheduleid], capture_output=True, text=True)
if proc.returncode != 0:
    print('PHP script failed:')
    print(proc.stderr)
    sys.exit(proc.returncode)

try:
    data = json.loads(proc.stdout)
except Exception as e:
    print('Failed to parse JSON output from PHP script')
    print(proc.stdout)
    sys.exit(3)

print(f"Schedule {data['scheduleid']}: total appointments = {data['total']}")
if data['duplicates']:
    print('Duplicates found:', data['duplicates'])
else:
    print('No duplicate appointment numbers')

if data['missing']:
    print('Missing numbers in sequence:', data['missing'][:20], ('... (+%d)' % (len(data['missing'])-20) if len(data['missing'])>20 else ''))
else:
    print('No missing numbers in sequence')

if data['duplicates'] or data['missing']:
    sys.exit(1)
else:
    sys.exit(0)
