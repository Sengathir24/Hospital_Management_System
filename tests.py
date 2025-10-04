import os
import subprocess
import sys
import datetime


def list_test_folders(base_dir):
    return sorted([name for name in os.listdir(base_dir)
                   if os.path.isdir(os.path.join(base_dir, name)) and not name.startswith('__')])


def find_k6_files(folder_path):
    js_files = []
    for root, _, files in os.walk(folder_path):
        for f in files:
            if f.endswith('.js'):
                js_files.append(os.path.join(root, f))
    return sorted(js_files)


def run_k6_file(js_path, output_dir):
    base = os.path.basename(js_path)
    ts = datetime.datetime.now().strftime('%Y%m%d-%H%M%S')
    log_name = f"k6-{os.path.splitext(base)[0]}-{ts}.log"
    log_path = os.path.join(output_dir, log_name)
    print(f"Running: {js_path}")
    with open(log_path, 'w', encoding='utf-8') as logfile:
        # Run k6 and capture stdout+stderr
        proc = subprocess.Popen(['k6', 'run', js_path], stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True)
        for line in proc.stdout:
            logfile.write(line)
            logfile.flush()
            print(line, end='')
        proc.wait()
    return log_path, proc.returncode


def parse_k6_log_for_summary(log_path):
    # Parse basic summary values from the k6 log file
    summary = {
        'checks_total': None,
        'checks_failed': None,
        'http_reqs': None,
        'http_req_failed': None,
        'exit_code': 0,
    }
    with open(log_path, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if line.startswith('checks_total') or 'checks_total' in line:
                # e.g. checks_total.......: 2260    73.983458/s
                parts = line.split(':')
                if len(parts) >= 2:
                    try:
                        summary['checks_total'] = int(parts[1].strip().split()[0])
                    except Exception:
                        pass
            if line.startswith('checks_failed') or 'checks_failed' in line:
                parts = line.split(':')
                if len(parts) >= 2:
                    try:
                        # last token is like '0 out of 2260'
                        tok = parts[1].strip().split()[:1]
                        # fallback: search for 'out of'
                        if 'out of' in parts[1]:
                            # pattern: '0.00%   0 out of 2260'
                            toks = parts[1].strip().split()
                            for i, t in enumerate(toks):
                                if t == 'out' and i>=1:
                                    summary['checks_failed'] = int(toks[i-1])
                        else:
                            summary['checks_failed'] = 0
                    except Exception:
                        pass
            if line.startswith('http_reqs') or 'http_reqs' in line:
                parts = line.split(':')
                if len(parts) >= 2:
                    try:
                        summary['http_reqs'] = int(parts[1].strip().split()[0])
                    except Exception:
                        pass
            if line.startswith('http_req_failed') or 'http_req_failed' in line:
                parts = line.split(':')
                if len(parts) >= 2:
                    try:
                        # e.g. http_req_failed................: 0.00%  0 out of 2260
                        toks = parts[1].strip().split()
                        for i, t in enumerate(toks):
                            if t == 'out' and i>=1:
                                summary['http_req_failed'] = int(toks[i-1])
                    except Exception:
                        pass
    return summary


def run_tests_in_folder(folder_path):
    print(f"Running tests in: {folder_path}\n")
    js_files = find_k6_files(folder_path)
    if not js_files:
        print('No k6 .js files found in this folder.')
        return

    # create a timestamped subfolder inside k6-logs for this run
    base_logs_dir = os.path.join(os.path.dirname(__file__), 'k6-logs')
    ts_folder = datetime.datetime.now().strftime('%Y%m%d-%H%M%S')
    output_dir = os.path.join(base_logs_dir, ts_folder)
    os.makedirs(output_dir, exist_ok=True)

    results = []
    for js in js_files:
        log_path, rc = run_k6_file(js, output_dir)
        summary = parse_k6_log_for_summary(log_path)
        summary['script'] = js
        summary['log'] = log_path
        summary['exit_code'] = rc
        results.append(summary)

    # Print concise report
    print('\n=== K6 Test Summary ===')
    total_scripts = len(results)
    passed = 0
    for r in results:
        ok = (r.get('checks_failed') in (None, 0)) and r.get('exit_code', 0) == 0
        status = 'PASS' if ok else 'FAIL'
        if ok:
            passed += 1
        print(f"{status}: {os.path.basename(r['script'])}  checks_failed={r.get('checks_failed')} http_req_failed={r.get('http_req_failed')} log={r['log']}")

    print(f"\n{passed}/{total_scripts} scripts passed (based on parsed k6 logs and exit codes)")


def main():
    base_tests_dir = os.path.join(os.path.dirname(__file__), 'tests')
    if not os.path.exists(base_tests_dir):
        print("No 'tests' folder found.")
        return

    folders = list_test_folders(base_tests_dir)
    options = folders + ['all']
    print("Select a folder to run tests from:")
    for idx, name in enumerate(options, 1):
        print(f"{idx}. {name}")

    choice = input("Enter your choice number: ").strip()
    try:
        choice_idx = int(choice) - 1
        if choice_idx < 0 or choice_idx >= len(options):
            raise ValueError
    except ValueError:
        print("Invalid choice.")
        return

    selected = options[choice_idx]
    if selected == 'all':
        # run all top-level folders sequentially
        for f in folders:
            run_tests_in_folder(os.path.join(base_tests_dir, f))
    else:
        run_tests_in_folder(os.path.join(base_tests_dir, selected))


if __name__ == "__main__":
    # ensure k6 is available
    try:
        subprocess.run(['k6', '--version'], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    except Exception:
        print('k6 is not installed or not in PATH. Please install k6 to run these tests: https://k6.io/docs/getting-started/installation')
        sys.exit(1)
    main()