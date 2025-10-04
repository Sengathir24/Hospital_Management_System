import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE='http://localhost';
const EMAIL='doctor@edoc.com';
const PASS='123';

export default function(){
  http.post(`${BASE}/login.php`, { useremail: EMAIL, userpassword: PASS }, { redirects: 0 });
  const pages = ['index.php','appointment.php','doctors.php','patient.php','schedule.php','settings.php'];
  for(let p of pages){
    let r = http.get(`${BASE}/doctor/${p}`);
    check(r, { [`${p} ok`]: (res) => res.status === 200 || res.status === 302 });
    sleep(0.5);
  }
}
