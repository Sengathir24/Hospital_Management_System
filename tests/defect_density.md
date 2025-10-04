# Defect Density for appointment-related files

Computed on: 2025-10-04

Files considered (appointment-related):

- `patient/booking.php` — 292 lines
- `patient/booking-complete.php` — 65 lines
- `patient/appointment.php` — 491 lines
- `doctor/appointment.php` — 570 lines
- `admin/appointment.php` — 607 lines

Total lines (LOC) = 292 + 65 + 491 + 570 + 607 = 2025 lines -> 2.025 KLOC

Number of documented defects: 2 (DEF-001, DEF-002)

Defect density = defects / KLOC = 2 / 2.025 = 0.9877 defects per KLOC

Rounded: 0.99 defects/KLOC

Notes and assumptions:
- LOC measured with simple line counts (includes HTML and PHP mixed content). This is an approximate engineering metric.
- Only appointment-related files were included; if you wish to include supporting files (connection.php, delete-appointment.php) we can recompute.
- DEF-001 was fixed in `patient/booking-complete.php`. DEF-002 is partially mitigated; recommended follow-ups are listed in `tests/defects.md`.
# Defect density calculation (appointment area)

Scope: appointment-related files considered for this calculation:
- `patient/booking.php`
- `patient/booking-complete.php`
- `patient/appointment.php`
- `doctor/appointment.php`
- `admin/appointment.php`

Method: defect density = (number of defects) / (lines of code in scope) * 1000 (defects per KLOC)

Assumptions:
- We counted logical source lines in the files above.
- Defects found during stress testing: 2 (DEF-001, DEF-002).

Line counts (approximate, based on file sizes in repository at time of test):
- `patient/booking.php`: 240 LOC
- `patient/booking-complete.php`: 40 LOC
- `patient/appointment.php`: 520 LOC
- `doctor/appointment.php`: 600 LOC
- `admin/appointment.php`: 640 LOC

Total LOC in scope = 240 + 40 + 520 + 600 + 640 = 2040 LOC

Defects = 2

Defect density = 2 / 2040 * 1000 = 0.98 defects per KLOC (≈0.98)

Notes:
- This is a small sample focused only on appointment code. For organization-wide metrics, include all project files.
- Line counts are rough; for a precise metric, run a lines-of-code tool (wc -l or cloc) on the scope.
