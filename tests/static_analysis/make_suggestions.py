#!/usr/bin/env python3
"""
Parse tests/static_analysis/report.txt and produce tests/static_analysis/suggestions.md
with prioritized files and example prepared-statement templates. Does not modify source files.
"""
from pathlib import Path
from collections import Counter, defaultdict
import re

ROOT = Path(__file__).resolve().parent
report_path = ROOT / 'report.txt'
out_path = ROOT / 'suggestions.md'

if not report_path.exists():
    print('report.txt not found; run find_interpolated_queries.py first')
    raise SystemExit(1)

lines = report_path.read_text(encoding='utf-8').splitlines()

issue_re = re.compile(r'^-\s+([^:]+):([0-9]+)\s+->\s+(.*)$')

per_file = defaultdict(list)

for ln in lines:
    m = issue_re.match(ln)
    if m:
        fname, lnum, code = m.groups()
        per_file[fname].append((int(lnum), code))

counts = Counter({f: len(v) for f, v in per_file.items()})

top_files = [f for f,_ in counts.most_common(20)]

out = []
out.append('# Static analysis remediation suggestions')
out.append('Generated from tests/static_analysis/report.txt')
out.append('')

if not counts:
    out.append('No issues found by the heuristic scanner.')
else:
    out.append('## Top files by heuristic hits')
    out.append('')
    for f, c in counts.most_common(20):
        out.append(f'- {f} — {c} hits')

    out.append('')
    out.append('## Suggested priority and templates')
    out.append('')
    out.append('Remediation priority:')
    out.append('1. Files that accept GET/POST parameters used in SQL (filters, id, search)')
    out.append('2. DELETE/UPDATE/INSERT statements that include variables directly')
    out.append('3. Other queries that use variable interpolation')
    out.append('')

    for f in top_files:
        out.append(f'### {f} — {counts[f]} heuristic hits')
        out.append('Sample lines:')
        for lnum, code in per_file[f][:6]:
            out.append(f'- line {lnum}: `{code}`')
        out.append('')
        out.append('Suggested fix pattern (example):')
        out.append('\nUse prepared statements and parameter binding instead of interpolating variables.\n')
        out.append('PHP (mysqli) example:')
        out.append('```php')
        out.append("// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`")
        out.append("$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';")
        out.append("$stmt = $database->prepare($sql);")
        out.append("$stmt->bind_param('i', $id);")
        out.append("$stmt->execute();")
        out.append("$res = $stmt->get_result();")
        out.append('```')
        out.append('')
        out.append('If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:')
        out.append('```php')
        out.append("$sql = 'SELECT ... FROM ... WHERE 1=1';")
        out.append("$types = '';")
        out.append("$params = array();")
        out.append("if (!empty(\$_POST['sheduledate'])) {")
        out.append("    $sql .= ' AND schedule.scheduledate = ?';")
        out.append("    $types .= 's';")
        out.append("    $params[] = \\$_POST['sheduledate'];")
        out.append("}")
        out.append("$stmt = $database->prepare($sql);")
        out.append("if ($types) { $stmt->bind_param($types, ...$params); }")
        out.append("$stmt->execute();")
        out.append('```')
        out.append('')
        out.append('---')

out_path.write_text('\n'.join(out), encoding='utf-8')
print('Wrote suggestions to', out_path)
