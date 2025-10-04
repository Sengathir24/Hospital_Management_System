<?php
/*
Suggested remediation for admin/appointment.php queries that are built with string interpolation.

Example problematic pattern: building $sqlmain with interpolated variables/filters.

Suggested pattern:
    // --- BEGIN SUGGESTED SNIPPET ---
    if($_POST){
        $base_sql = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate FROM schedule INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN doctor ON schedule.docid=doctor.docid WHERE 1=1";
        $types = '';
        $params = array();

        if(!empty($_POST['sheduledate']) && preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $_POST['sheduledate'])){
            $base_sql .= " AND schedule.scheduledate = ?";
            $types .= 's';
            $params[] = $_POST['sheduledate'];
        }

        if(!empty($_POST['docid'])){
            $docid = intval($_POST['docid']);
            if($docid>0){
                $base_sql .= " AND doctor.docid = ?";
                $types .= 'i';
                $params[] = $docid;
            }
        }

        $base_sql .= " ORDER BY schedule.scheduledate DESC";

        $stmt = $database->prepare($base_sql);
        if($types != ''){
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $database->query("SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate FROM schedule INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN doctor ON schedule.docid=doctor.docid ORDER BY schedule.scheduledate DESC");
    }
    // --- END SUGGESTED SNIPPET ---

*/
