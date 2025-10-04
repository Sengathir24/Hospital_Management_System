# Automated test categories (k6)

This folder contains starter k6 scripts organized by testing type:

- `tests/component` — component tests that exercise single PHP pages (GET checks).
- `tests/unit` — unit-like tests that POST edge/invalid input to endpoints.
- `tests/integration` — integration tests chaining login, booking, and appointment listing.

Quick run (PowerShell):

Component tests:
```powershell
k6 run tests/component/component_tests.js
```

Unit tests:
```powershell
k6 run tests/unit/unit_tests.js
```

Integration tests:
```powershell
k6 run tests/integration/integration_test.js
```

Doctor-specific tests (one script per page):
```powershell
# Run a single doctor test, e.g. appointment page
k6 run tests/doctor/appointment_test.js

# Or run the doctor flow (visits many doctor pages)
k6 run tests/doctor/doctor_flow.js
```

Patient-specific tests (one script per page):
```powershell
# Run a single patient test, e.g. booking page
k6 run tests/patient/booking_test.js

# Or run the patient flow (visits many patient pages)
k6 run tests/patient/patient_flow.js
```

Notes:
- Scripts are intentionally lightweight and use the project's default test credentials (patient@edoc.com / 123). Update the scripts if you have other test accounts.
- These scripts are k6 JS so they run single VU by default; you can scale with `--vus` and `--duration` flags for stress/regression.
- For DB-level assertions (no direct DB API in k6), consider adding a protected test API endpoint that returns appointment counts for a schedule; k6 can then assert DB state.
