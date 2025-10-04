# Static analysis remediation suggestions
Generated from tests/static_analysis/report.txt

## Top files by heuristic hits

- doctor\doctors.php — 13 hits
- admin\doctors.php — 13 hits
- patient\doctors.php — 12 hits
- doctor\settings.php — 12 hits
- admin\appointment.php — 10 hits
- admin\schedule.php — 10 hits
- patient\settings.php — 9 hits
- doctor\appointment.php — 9 hits
- patient\patient.php — 8 hits
- doctor\patient.php — 8 hits
- doctor\schedule.php — 8 hits
- patient\edit-user.php — 7 hits
- patient\index.php — 7 hits
- doctor\edit-doc.php — 7 hits
- admin\edit-doc.php — 7 hits
- admin\index.php — 7 hits
- patient\appointment.php — 6 hits
- patient\booking-complete.php — 6 hits
- patient\schedule.php — 6 hits
- doctor\index.php — 6 hits

## Suggested priority and templates

Remediation priority:
1. Files that accept GET/POST parameters used in SQL (filters, id, search)
2. DELETE/UPDATE/INSERT statements that include variables directly
3. Other queries that use variable interpolation

### doctor\doctors.php — 13 heuristic hits
Sample lines:
- line 112: `$list11 = $database->query("select  docname,docemail from  doctor;");`
- line 169: `$sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";`
- line 171: `$sqlmain= "select * from doctor order by docid desc";`
- line 237: `$spcil_res= $database->query("select sname from specialties where id='$spe'");`
- line 257: `<a href="?action=drop&id='.$docid.'&name='.$name.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>`
- line 294: `You want to delete this record<br>('.substr($nameget,0,40).').`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### admin\doctors.php — 13 heuristic hits
Sample lines:
- line 110: `$list11 = $database->query("select  docname,docemail from  doctor;");`
- line 167: `$sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";`
- line 169: `$sqlmain= "select * from doctor order by docid desc";`
- line 235: `$spcil_res= $database->query("select sname from specialties where id='$spe'");`
- line 255: `<a href="?action=drop&id='.$docid.'&name='.$name.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>`
- line 292: `You want to delete this record<br>('.substr($nameget,0,40).').`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\doctors.php — 12 heuristic hits
Sample lines:
- line 42: `$userrow = $database->query("select * from patient where pemail='$useremail'");`
- line 115: `$list11 = $database->query("select  docname,docemail from  doctor;");`
- line 165: `$sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";`
- line 167: `$sqlmain= "select * from doctor order by docid desc";`
- line 233: `$spcil_res= $database->query("select sname from specialties where id='$spe'");`
- line 289: `You want to delete this record<br>('.substr($nameget,0,40).').`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### doctor\settings.php — 12 heuristic hits
Sample lines:
- line 49: `$userrow = $database->query("select * from doctor where docemail='$useremail'");`
- line 136: `$patientrow = $database->query("select  * from  patient;");`
- line 137: `$doctorrow = $database->query("select  * from  doctor;");`
- line 138: `$appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");`
- line 139: `$schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");`
- line 254: `You want to delete this record<br>('.substr($nameget,0,40).').`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### admin\appointment.php — 10 heuristic hits
Sample lines:
- line 118: `$list110 = $database->query("select  * from  appointment;");`
- line 172: `$list11 = $database->query("select  * from  doctor order by docname asc;");`
- line 216: `$sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid";`
- line 233: `$sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  order by schedule.scheduledate desc";`
- line 350: `<a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>`
- line 425: `$list11 = $database->query("select  * from  doctor;");`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### admin\schedule.php — 10 heuristic hits
Sample lines:
- line 118: `$list110 = $database->query("select  * from  schedule;");`
- line 172: `$list11 = $database->query("select  * from  doctor order by docname asc;");`
- line 216: `$sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid ";`
- line 233: `$sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  order by schedule.scheduledate desc";`
- line 327: `<a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>`
- line 402: `$list11 = $database->query("select  * from  doctor order by docname asc;");`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\settings.php — 9 heuristic hits
Sample lines:
- line 49: `$sqlmain= "select * from patient where pemail=?";`
- line 137: `$patientrow = $database->query("select  * from  patient;");`
- line 138: `$doctorrow = $database->query("select  * from  doctor;");`
- line 139: `$appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");`
- line 140: `$schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");`
- line 255: `You want to delete Your Account<br>('.substr($nameget,0,40).').`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### doctor\appointment.php — 9 heuristic hits
Sample lines:
- line 43: `$userrow = $database->query("select * from doctor where docemail='$useremail'");`
- line 123: `$list110 = $database->query("select * from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  doctor.docid=$userid ");`
- line 185: `$sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  doctor.docid=$userid ";`
- line 309: `<a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>`
- line 384: `$list11 = $database->query("select  * from  doctor;");`
- line 482: `You want to delete this record<br><br>`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\patient.php — 8 heuristic hits
Sample lines:
- line 42: `$sqlmain= "select * from doctor where docemail=?";`
- line 116: `$sqlmain= "select * from patient where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";`
- line 122: `$sqlmain= "select * from patient";`
- line 126: `$sqlmain= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";`
- line 132: `$sqlmain= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";`
- line 156: `//$list12= $database->query("select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=1;");`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### doctor\patient.php — 8 heuristic hits
Sample lines:
- line 42: `$userrow = $database->query("select * from doctor where docemail='$useremail'");`
- line 112: `$sqlmain= "select * from patient where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";`
- line 118: `$sqlmain= "select * from patient";`
- line 122: `$sqlmain= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";`
- line 128: `$sqlmain= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";`
- line 152: `//$list12= $database->query("select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=1;");`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### doctor\schedule.php — 8 heuristic hits
Sample lines:
- line 43: `$userrow = $database->query("select * from doctor where docemail='$useremail'");`
- line 123: `$list110 = $database->query("select  * from  schedule where docid=$userid;");`
- line 177: `$sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid where doctor.docid=$userid ";`
- line 272: `<a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel Session</font></button></a>`
- line 309: `You want to delete this record<br>('.substr($nameget,0,40).').`
- line 313: `<a href="delete-session.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\edit-user.php — 7 heuristic hits
Sample lines:
- line 13: `$result= $database->query("select * from webuser");`
- line 27: `$sqlmain= "select patient.pid from patient inner join webuser on patient.pemail=webuser.email where webuser.email=?;";`
- line 32: `//$resultqq= $database->query("select * from doctor where docid='$id';");`
- line 42: `//$resultqq1= $database->query("select * from doctor where docemail='$email';");`
- line 48: `//$sql1="insert into doctor(docemail,docname,docpassword,docnic,doctel,specialties) values('$email','$name','$password','$nic','$tele',$spec);";`
- line 49: `$sql1="update patient set pemail='$email',pname='$name',ppassword='$password',pnic='$nic',ptel='$tele',paddress='$address' where pid=$id ;";`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\index.php — 7 heuristic hits
Sample lines:
- line 48: `$sqlmain= "select * from patient where pemail=?";`
- line 139: `$patientrow = $database->query("select  * from  patient;");`
- line 140: `$doctorrow = $database->query("select  * from  doctor;");`
- line 141: `$appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");`
- line 142: `$schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");`
- line 176: `$list11 = $database->query("select  docname,docemail from  doctor;");`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### doctor\edit-doc.php — 7 heuristic hits
Sample lines:
- line 13: `$result= $database->query("select * from webuser");`
- line 26: `$result= $database->query("select doctor.docid from doctor inner join webuser on doctor.docemail=webuser.email where webuser.email='$email';");`
- line 27: `//$resultqq= $database->query("select * from doctor where docid='$id';");`
- line 37: `//$resultqq1= $database->query("select * from doctor where docemail='$email';");`
- line 43: `//$sql1="insert into doctor(docemail,docname,docpassword,docnic,doctel,specialties) values('$email','$name','$password','$nic','$tele',$spec);";`
- line 44: `$sql1="update doctor set docemail='$email',docname='$name',docpassword='$password',docnic='$nic',doctel='$tele',specialties=$spec where docid=$id ;";`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### admin\edit-doc.php — 7 heuristic hits
Sample lines:
- line 13: `$result= $database->query("select * from webuser");`
- line 26: `$result= $database->query("select doctor.docid from doctor inner join webuser on doctor.docemail=webuser.email where webuser.email='$email';");`
- line 27: `//$resultqq= $database->query("select * from doctor where docid='$id';");`
- line 37: `//$resultqq1= $database->query("select * from doctor where docemail='$email';");`
- line 43: `//$sql1="insert into doctor(docemail,docname,docpassword,docnic,doctel,specialties) values('$email','$name','$password','$nic','$tele',$spec);";`
- line 44: `$sql1="update doctor set docemail='$email',docname='$name',docpassword='$password',docnic='$nic',doctel='$tele',specialties=$spec where docid=$id ;";`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### admin\index.php — 7 heuristic hits
Sample lines:
- line 111: `$list11 = $database->query("select  docname,docemail from  doctor;");`
- line 142: `$patientrow = $database->query("select  * from  patient;");`
- line 143: `$doctorrow = $database->query("select  * from  doctor;");`
- line 144: `$appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");`
- line 145: `$schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");`
- line 293: `$sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where schedule.scheduledate>='$today'  and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\appointment.php — 6 heuristic hits
Sample lines:
- line 42: `$sqlmain= "select * from patient where pemail=?";`
- line 57: `$sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  patient.pid=$userid ";`
- line 334: `//        <a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>`
- line 401: `<a href="delete-appointment.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;`
- line 410: `$sqlmain= "select * from doctor where docid=?";`
- line 420: `$sqlmain= "select sname from specialties where id=?";`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\booking-complete.php — 6 heuristic hits
Sample lines:
- line 21: `$sqlmain= "select * from patient where pemail=?";`
- line 37: `// Use transaction to ensure count+insert is atomic`
- line 41: `$sqlCount = "SELECT COUNT(*) AS c FROM appointment WHERE scheduleid = ? FOR UPDATE";`
- line 49: `// Prepared insert to avoid SQL injection and ensure correctness`
- line 50: `$sqlInsert = "INSERT INTO appointment(pid, apponum, scheduleid, appodate) VALUES (?, ?, ?, ?)";`
- line 56: `// Insert failed; rollback and show error`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### patient\schedule.php — 6 heuristic hits
Sample lines:
- line 42: `$sqlmain= "select * from patient where pemail=?";`
- line 116: `$sqlmain= "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today'  order by schedule.scheduledate asc";`
- line 127: `$sqlmain= "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and (doctor.docname='$keyword' or doctor.docname like '$keyword%' or doctor.docname like '%$keyword' or doctor.docname like '%$keyword%' or schedule.title='$keyword' or schedule.title like '$keyword%' or schedule.title like '%$keyword' or schedule.title like '%$keyword%' or schedule.scheduledate like '$keyword%' or schedule.scheduledate like '%$keyword' or schedule.scheduledate like '%$keyword%' or schedule.scheduledate='$keyword' )  order by schedule.scheduledate asc";`
- line 155: `$list11 = $database->query("select DISTINCT * from  doctor;");`
- line 156: `$list12 = $database->query("select DISTINCT * from  schedule GROUP BY title;");`
- line 309: `//    <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel Session</font></button></a>`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---
### doctor\index.php — 6 heuristic hits
Sample lines:
- line 50: `$userrow = $database->query("select * from doctor where docemail='$useremail'");`
- line 136: `$patientrow = $database->query("select  * from  patient;");`
- line 137: `$doctorrow = $database->query("select  * from  doctor;");`
- line 138: `$appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");`
- line 139: `$schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");`
- line 294: `$sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";`

Suggested fix pattern (example):

Use prepared statements and parameter binding instead of interpolating variables.

PHP (mysqli) example:
```php
// Example: convert `SELECT * FROM appointment WHERE scheduleid=$id`
$sql = 'SELECT * FROM appointment WHERE scheduleid = ?';
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
```

If the code builds the SQL across multiple lines, collect the parameters and bind them in order. Example for multiple filters:
```php
$sql = 'SELECT ... FROM ... WHERE 1=1';
$types = '';
$params = array();
if (!empty(\$_POST['sheduledate'])) {
    $sql .= ' AND schedule.scheduledate = ?';
    $types .= 's';
    $params[] = \$_POST['sheduledate'];
}
$stmt = $database->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
```

---