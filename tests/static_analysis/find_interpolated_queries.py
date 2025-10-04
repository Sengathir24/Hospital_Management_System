#!/usr/bin/env python3
"""
Scan PHP files under doctor/, admin/, patient/ for string concatenation or interpolation
that looks like SQL built with variables (heuristic). Produces a small report to stdout and
writes `tests/static_analysis/report.txt`.
"""
import os
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
TARGET_DIRS = ['patient', 'doctor', 'admin']
OUT = Path(__file__).resolve().parent

SQL_PATTERN = re.compile(r"\b(select|insert|update|delete)\b", re.I)
VAR_IN_SQL = re.compile(r"\$[a-zA-Z_][a-zA-Z0-9_]*")
CONCAT_PATTERN = re.compile(r'\.|\\.\s*\$')

findings = []

for td in TARGET_DIRS:
    base = ROOT / td
    if not base.exists():
        continue
    for p in base.rglob('*.php'):
        try:
            text = p.read_text(encoding='utf-8', errors='ignore')
        except Exception:
            continue
        # find lines with SQL keywords and variable names inside the same line or nearby
        for i, line in enumerate(text.splitlines(), start=1):
            if SQL_PATTERN.search(line):
                # if the SQL line contains a variable or concatenation operator nearby, flag
                if VAR_IN_SQL.search(line) or ("." in line and "$" in line):
                    findings.append((str(p.relative_to(ROOT)), i, line.strip()))
                else:
                    # also check small window of next 2 lines for variable usage
                    window = '\n'.join(text.splitlines()[i:i+2])
                    if VAR_IN_SQL.search(window) or ("." in window and "$" in window):
                        findings.append((str(p.relative_to(ROOT)), i, line.strip()))

report_lines = []
report_lines.append('Static SQL interpolation scan - heuristic results')
report_lines.append('Scanned folders: ' + ', '.join(TARGET_DIRS))
report_lines.append('')
if not findings:
    report_lines.append('No obvious interpolated/concatenated SQL patterns found by heuristic.')
else:
    report_lines.append(f'Found {len(findings)} potential issues:')
    for f, ln, code in findings:
        report_lines.append(f'- {f}:{ln} -> {code}')

out_path = OUT / 'report.txt'
out_path.write_text('\n'.join(report_lines), encoding='utf-8')
print('\n'.join(report_lines))
print('\nReport written to', out_path)
