# k6 tests for eDoc appointment flows

This folder contains k6 scripts to test login and appointment booking flows and a stress test to reproduce concurrency issues.

Prerequisites:
- Install k6: https://k6.io/docs/getting-started/installation
- Ensure the webapp is running on http://localhost (default XAMPP setup). Adjust BASE in the scripts if your server uses a different host/port.

Files:
- `login_and_booking.js` - simple VU that logs in and attempts one booking.
- `booking_stress.js` - stress test that runs many concurrent booking attempts. Use --vus 20 to simulate 20 concurrent users.

Default test credentials (from project README):

- Patient: email `patient@edoc.com` / password `123`

Run examples (PowerShell):
```powershell
# Quick single-VU smoke test
k6 run tests/k6/login_and_booking.js

# Stress test (20 concurrent VUs for 30s)
k6 run --vus 20 --duration 30s tests/k6/booking_stress.js
```

Interpreting results:
- Look at the HTTP status codes and any non-200/302 responses.
- k6 summary shows requests/s, failures, and detailed checks if any check failed. If many booking requests return errors or you see duplicates in the DB, you have a concurrency defect.

Important notes and manual verification:
- The scripts use example credentials `testpatient@example.com` / `password`. Update them to match test accounts in your local database.
- The booking scripts submit `apponum` computed on the client side for simplicity; in-product the server computes it. The stress test purposefully causes collisions to reproduce race conditions.

Regression test (20 users booking at once):

Run this to reproduce and validate the concurrency issue and then inspect the DB:

```powershell
# Regression stress test (20 concurrent users)
k6 run --vus 20 --duration 30s tests/k6/booking_stress.js
```

After the test: check the `appointment` table in your `edoc` database for duplicate `apponum` values for the same `scheduleid` or missing rows.
