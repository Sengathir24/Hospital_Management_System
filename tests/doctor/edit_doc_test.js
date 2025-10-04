import http from 'k6/http';
import { check } from 'k6';
const BASE='http://localhost';
const EMAIL='doctor@edoc.com';
const PASS='123';

export default function(){
  http.post(`${BASE}/login.php`, { useremail: EMAIL, userpassword: PASS }, { redirects: 0 });
  let r = http.get(`${BASE}/doctor/edit-doc.php?id=1`);
  check(r, { 'edit doc page ok': (res) => res.status === 200 });
}
