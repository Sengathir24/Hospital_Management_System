import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE = 'http://localhost';
const EMAIL = 'patient@edoc.com';
const PASS = '123';
const SCHEDULE_ID = 1;

export default function () {
  // Login
  let loginRes = http.post(`${BASE}/login.php`, { useremail: EMAIL, userpassword: PASS }, { redirects: 0 });
  check(loginRes, { 'login ok': (r) => r.status === 302 || r.status === 200 });
  sleep(1);

  // Read booking page
  let b = http.get(`${BASE}/patient/booking.php?id=${SCHEDULE_ID}`);
  check(b, { 'booking page ok': (r) => r.status === 200 });
  sleep(1);

  // Attempt booking with a safe apponum guess (server will recalc)
  let post = http.post(`${BASE}/patient/booking-complete.php`, { scheduleid: SCHEDULE_ID, apponum: 1, date: new Date().toISOString().slice(0,10), booknow: 'Book now' }, { redirects: 0 });
  check(post, { 'booking attempt OK': (r) => r.status === 200 || r.status === 302 });
  sleep(1);

  // View appointment listing
  let app = http.get(`${BASE}/patient/appointment.php`);
  check(app, { 'appointment list ok': (r) => r.status === 200 });
}
