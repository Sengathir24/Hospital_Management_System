import http from 'k6/http';
import { check } from 'k6';
const BASE='http://localhost';
const EMAIL='patient@edoc.com';
const PASS='123';

export default function(){
  http.post(`${BASE}/login.php`, { useremail: EMAIL, userpassword: PASS }, { redirects: 0 });
  let r = http.post(`${BASE}/patient/booking-complete.php`, { booknow: 'Book now' }, { redirects: 0 });
  check(r, { 'booking complete handled': (res) => res.status === 200 || res.status === 302 });
}
