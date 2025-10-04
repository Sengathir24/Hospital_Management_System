# Documented Defects

This file records two defects found during validation of the appointment booking code and their status.

## DEF-001: Race condition when assigning appointment number (apponum)
- Severity: High
- Status: Fixed (server-side)
- Description: The original booking flow allowed the client to submit `apponum` or relied on a non-atomic SELECT COUNT(...) then INSERT. Under concurrent booking attempts this could cause duplicate or skipped appointment numbers.
- Location: `patient/booking.php` (client-side generated hidden input `apponum`) and original `patient/booking-complete.php` logic.
- Reproduction steps:
  1. Start the app and ensure DB has an empty or small set of appointments for a schedule (e.g. scheduleid=1).
 2. Run the stress test: `k6 run --vus 20 --duration 30s tests/k6/booking_stress.js`.
 3. Observe duplicate appointment numbers or failed inserts when the code computes `apponum` client-side or uses non-transactional count+insert.
- Fix applied: `patient/booking-complete.php` now computes `apponum` server-side inside a transaction using `SELECT COUNT(*) ... FOR UPDATE` and then performs a prepared `INSERT`. This serializes concurrent appointment assignments and prevents duplicates.

## DEF-002: SQL injection / unsafe SQL string building
- Severity: Medium
- Status: Partially mitigated / Ongoing hardening
- Description: Several files construct SQL queries by interpolating PHP variables directly into strings, e.g. `"select * from appointment where scheduleid=$id"` and other `query()` calls using variables. These are risky if any value can be influenced by users.
- Location examples: `patient/booking.php`, `patient/appointment.php`, `doctor/appointment.php`, and `admin/appointment.php` contain occurrences of raw `->query()` with interpolated variables.
- Reproduction steps:
  1. Find an endpoint that accepts user-supplied values used directly in `->query()` (for example a `GET` param inserted into a query).
  2. Supply a crafted value containing SQL payload and observe altered query behavior if the application runs it.
- Mitigation & next steps: Use prepared statements with parameter binding for any query that includes external input. Where full migration is large, prioritize endpoints that accept `GET`/`POST` parameters (filters, ids) and convert them to prepared statements.
# Defects discovered during booking stress testing

Defect 1: Lost booking or duplicate appointment numbers under high concurrency

- ID: DEF-001
- Severity: High
- Affected area: `patient/booking-complete.php` INSERT into `appointment`
- Description: When many users attempt to book the same schedule at the same time, the server computes appointment numbers by counting existing appointment rows (SELECT * FROM appointment WHERE scheduleid=...) and then inserts a new row with that apponum. This read-then-write without a transactional lock can cause duplicate apponum values or lost bookings if two requests compute the same apponum simultaneously.
- Steps to reproduce:
  1. Run the stress test: `k6 run --vus 20 --duration 30s tests/k6/booking_stress.js` against the local server.
  2. Observe HTTP responses and server logs. Some booking attempts will silently succeed but result in database constraint conflicts or duplicated apportion numbers.
  3. Inspect `appointment` rows for duplicate `apponum` values for a single `scheduleid` or missing expected rows.
- Observed behavior: some booking attempts fail or the resulting appointment numbers are duplicated.
- Suggested fix: use a database transaction and atomic increment (for example, use an AUTO_INCREMENT primary key for appointment and derive the appointment number using a single INSERT that returns the inserted id, or use SELECT ... FOR UPDATE on a counter row). Alternatively enforce uniqueness via a composite unique constraint and handle duplicate-key errors with retry logic.

Defect 2: SQL injection risk and non-parameterized INSERT in booking-complete.php

- ID: DEF-002
- Severity: Medium
- Affected area: `patient/booking-complete.php`
- Description: The code constructs the INSERT SQL by concatenating variables directly into the string: `insert into appointment(pid,apponum,scheduleid,appodate) values ($userid,$apponum,$scheduleid,'$date')`. Although `$userid` is computed server-side, `apponum` and `scheduleid` are directly read from POST. This is a risk and an unsafe practice.
- Steps to reproduce:
  1. Submit a crafted POST to `patient/booking-complete.php` with malicious values in `apponum` or `scheduleid` to attempt to alter SQL. (Do this only in a safe test environment.)
- Suggested fix: Use prepared statements with bound parameters for the INSERT.
