import http from 'k6/http';
import { check } from 'k6';

// Unit-like checks: POST invalid/edge inputs to endpoints and verify server handles gracefully
const BASE = 'http://localhost';

export default function () {
  // Invalid login (bad email format)
  let res1 = http.post(`${BASE}/login.php`, { useremail: 'invalid-email', userpassword: 'x' }, { redirects: 0 });
  check(res1, { 'invalid login handled': (r) => r.status === 200 || r.status === 302 });

  // Booking-complete with missing fields
  let res2 = http.post(`${BASE}/patient/booking-complete.php`, { booknow: 'Book now' }, { redirects: 0 });
  check(res2, { 'booking-complete missing fields handled': (r) => r.status === 200 || r.status === 302 });
}
