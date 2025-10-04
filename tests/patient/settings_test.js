import http from 'k6/http';
import { check } from 'k6';
const BASE='http://localhost';
const EMAIL='patient@edoc.com';
const PASS='123';

export default function(){
  http.post(`${BASE}/login.php`, { useremail: EMAIL, userpassword: PASS }, { redirects: 0 });
  let r = http.get(`${BASE}/patient/settings.php`);
  check(r, { 'patient settings ok': (res) => res.status === 200 });
}
