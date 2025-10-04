import http from 'k6/http';
import { check, sleep } from 'k6';

// Simple login + booking flow test for a single virtual user
// Usage: k6 run tests/k6/login_and_booking.js

export let options = {
  vus: 1,
  duration: '30s',
};

const BASE = 'http://localhost';

// Default test credentials from README
const TEST_EMAIL = 'patient@edoc.com';
const TEST_PASSWORD = '123';
const TEST_SCHEDULE_ID = 1;

function login(email, password){
  // login.php expects form fields useremail and userpassword
  let payload = { useremail: email, userpassword: password };
  let res = http.post(`${BASE}/login.php`, payload, { redirects: 0 });
  // Accept 200 or redirect
  check(res, {
    'login status 200 or 302': (r) => r.status === 200 || r.status === 302,
  });
  return res;
}

function getScheduleAndBook(scheduleId){
  // Read the booking page (booking.php?id=...)
  let res = http.get(`${BASE}/patient/booking.php?id=${scheduleId}`);
  check(res, { 'booking page ok': (r) => r.status === 200 });

  // Attempt booking: use fields expected by booking-complete.php
  // We intentionally post a guessed apponum; server-side race conditions will surface when many users post.
  let payload = {
    scheduleid: scheduleId,
    apponum: 1,
    date: (new Date()).toISOString().slice(0,10),
    booknow: 'Book now',
  };

  let postRes = http.post(`${BASE}/patient/booking-complete.php`, payload, { redirects: 0 });
  // Check for redirect to appointment.php?action=booking-added or 200 OK
  let ok = postRes.status === 200 || postRes.status === 302 || (postRes.headers && postRes.headers['Location'] && postRes.headers['Location'].includes('appointment.php'));
  check(postRes, { 'booking completed or redirect': () => ok });
  return postRes;
}

export default function () {
  let r = login(TEST_EMAIL, TEST_PASSWORD);
  sleep(1);
  getScheduleAndBook(TEST_SCHEDULE_ID);
  sleep(1);
}
