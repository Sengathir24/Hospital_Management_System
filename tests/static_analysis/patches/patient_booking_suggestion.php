<?php
/*
Suggested safe replacement snippet for patient/booking.php
This is a non-destructive suggestion file — do NOT copy blindly. Review and apply
the changes to the real file under version control.

Context (original pattern found):
    $sql2 = "select * from appointment where scheduleid=$id";
    $result12 = $database->query($sql2);
    $apponum = ($result12->num_rows)+1;

Suggested replacement (server-side count using prepared statement):
    // --- BEGIN SUGGESTED SNIPPET ---
    $sql2 = "SELECT COUNT(*) AS c FROM appointment WHERE scheduleid = ?";
    $stmt2 = $database->prepare($sql2);
    if (!$stmt2) {
        // handle prepare error
        error_log('Prepare failed: ' . $database->error);
        // fallback: safer default
        $apponum = 1;
    } else {
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $row2 = $res2->fetch_assoc();
        $apponum = intval($row2['c']) + 1;
    }
    // --- END SUGGESTED SNIPPET ---

Notes:
- Prefer computing the appointment number entirely on the server. If you expect concurrent bookings, do this in a transaction with a FOR UPDATE row-lock or use an atomic DB mechanism.
- This snippet assumes $id is an integer (cast or validated before use).
*/
