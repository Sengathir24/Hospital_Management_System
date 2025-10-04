import http from 'k6/http';
import { check, sleep } from 'k6';

// Stress test that simulates 20 users trying to book the same schedule concurrently
// Usage: k6 run --vus 20 --duration 30s tests/k6/booking_stress.js

export let options = {
  vus: 20,
  duration: '30s',
};

const BASE = 'http://localhost';

// Default patient credentials from README
const TEST_EMAIL = 'patient@edoc.com';
const TEST_PASSWORD = '123';
const SCHEDULE_ID = 1;

function login(email, password){
  let payload = { useremail: email, userpassword: password };
  return http.post(`${BASE}/login.php`, payload, { redirects: 0 });
}

function book(scheduleId, apponum){
  let payload = {
    scheduleid: scheduleId,
    apponum: apponum,
    date: (new Date()).toISOString().slice(0,10),
    booknow: 'Book now',
  };
  return http.post(`${BASE}/patient/booking-complete.php`, payload, { redirects: 0 });
}

export default function () {
  // Login (stateless POST per VU)
  let loginRes = login(TEST_EMAIL, TEST_PASSWORD);
  check(loginRes, { 'logged in': (r) => r.status === 302 || r.status === 200 });

  // Choose an apponum in a small range to provoke collisions under load
  let apponum = (__VU % 20) + 1;

  let res = book(SCHEDULE_ID, apponum);

  // Consider booking successful if server redirects to appointment.php or returns 200
  let success = res.status === 200 || res.status === 302 || (res.headers && res.headers['Location'] && res.headers['Location'].includes('appointment.php'));
  check(res, { 'booking request successful or redirect': () => success });

  sleep(0.5);
}
