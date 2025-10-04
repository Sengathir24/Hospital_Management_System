import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE='http://localhost';
const EMAIL='patient@edoc.com';
const PASS='123';

export default function(){
  http.post(`${BASE}/login.php`, { useremail: EMAIL, userpassword: PASS }, { redirects: 0 });
  const pages = ['index.php','doctors.php','schedule.php','booking.php?id=1','appointment.php','settings.php'];
  for(let p of pages){
    let url = p.includes('booking') ? `${BASE}/patient/${p}` : `${BASE}/patient/${p}`;
    let r = http.get(url);
    check(r, { [`${p} ok`]: (res) => res.status === 200 || res.status === 302 });
    sleep(0.5);
  }
}
