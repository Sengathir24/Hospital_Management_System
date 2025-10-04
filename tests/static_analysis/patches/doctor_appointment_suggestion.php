<?php
/*
Suggested remediation for doctor/appointment.php where queries use $userid interpolated

Example problematic line found: $sqlmain = "... where doctor.docid=$userid ";

Suggested safe pattern:
    // --- BEGIN SUGGESTED SNIPPET ---
    $base_sql = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate FROM schedule INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN doctor ON schedule.docid=doctor.docid WHERE doctor.docid = ?";

    $types = 'i';
    $params = [$userid];

    if($_POST && !empty($_POST['sheduledate'])){
        $sheduledate = $_POST['sheduledate'];
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $sheduledate)){
            $base_sql .= " AND schedule.scheduledate = ?";
            $types .= 's';
            $params[] = $sheduledate;
        }
    }

    $base_sql .= " ORDER BY appointment.appodate ASC";

    $stmt = $database->prepare($base_sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    // --- END SUGGESTED SNIPPET ---

Notes: this keeps the same filters but avoids injecting variables into SQL strings.
*/
