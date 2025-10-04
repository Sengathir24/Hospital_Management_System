import http from 'k6/http';
import { check } from 'k6';

// Component tests: submit forms where present and validate responses
const BASE = 'http://localhost';

export default function () {
  // --- Patient role: login and submit patient forms ---
  const P_EMAIL = 'patient@edoc.com';
  const P_PASS = '123';

  let loginRes = http.post(`${BASE}/login.php`, { useremail: P_EMAIL, userpassword: P_PASS }, { redirects: 0 });
  check(loginRes, { 'patient logged in': (r) => r.status === 200 || r.status === 302 });

  // Visit booking page (GET)
  let bookingPage = http.get(`${BASE}/patient/booking.php?id=1`);
  check(bookingPage, { 'patient booking page ok': (r) => r.status === 200 });

  // Try booking-complete (submit required hidden fields)
  let today = (new Date()).toISOString().slice(0,10);
  let bookRes = http.post(`${BASE}/patient/booking-complete.php`, { scheduleid: 1, apponum: 1, date: today, booknow: 'Book now' }, { redirects: 0 });
  check(bookRes, { 'patient booking-complete accepted': (r) => r.status === 200 || r.status === 302 });

  // Edit user (submit plausible fields)
  let editUserRes = http.post(`${BASE}/patient/edit-user.php`, { pname: 'Test Patient', pemail: 'patient@edoc.com', ppassword: '123', dobs: today }, { redirects: 0 });
  check(editUserRes, { 'patient edit-user accepted': (r) => r.status === 200 || r.status === 302 });

  // Delete account endpoint (if present) - attempt safe GET/POST
  let delAcc = http.get(`${BASE}/patient/delete-account.php`);
  check(delAcc, { 'patient delete-account page ok': (r) => r.status === 200 || r.status === 302 });

  // --- Doctor role: login and submit doctor forms ---
  const D_EMAIL = 'doctor@edoc.com';
  const D_PASS = '123';

  let dlogin = http.post(`${BASE}/login.php`, { useremail: D_EMAIL, userpassword: D_PASS }, { redirects: 0 });
  check(dlogin, { 'doctor logged in': (r) => r.status === 200 || r.status === 302 });

  // Edit doctor details
  let editDocRes = http.post(`${BASE}/doctor/edit-doc.php`, { docname: 'Dr Test', docemail: 'doctor@edoc.com', docpassword: '123' }, { redirects: 0 });
  check(editDocRes, { 'doctor edit-doc accepted': (r) => r.status === 200 || r.status === 302 });

  // Create a schedule entry (if the page accepts POST fields like title, nop, date, time)
  let scheduleRes = http.post(`${BASE}/doctor/schedule.php`, { title: 'Test Session', nop: 5, date: today, time: '09:00' }, { redirects: 0 });
  check(scheduleRes, { 'doctor schedule post accepted or ok': (r) => r.status === 200 || r.status === 302 });

  // Visit doctor settings
  let settings = http.get(`${BASE}/doctor/settings.php`);
  check(settings, { 'doctor settings ok': (r) => r.status === 200 });
}
