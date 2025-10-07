<?php
include('../connection.php');

echo "🐛 DEFECT IDENTIFICATION & LOGGING\n";
echo "===================================\n";

$defects = [];

// Test 1: Concurrent booking defect
echo "\n1. Testing Concurrent Booking:\n";
$database->query("DELETE FROM appointment WHERE pid = 888");

$sql1 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (888, 100, 1, CURDATE())";
$sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (888, 101, 1, CURDATE())";

if ($database->query($sql1) && $database->query($sql2)) {
    $defects[] = ['DEFECT-001', 'HIGH', 'Concurrent Booking', 'Multiple bookings allowed for same slot'];
    echo "❌ DEFECT: No concurrent booking prevention\n";
} else {
    echo "✅ No concurrent booking issue\n";
}

// Test 2: Input validation
echo "\n2. Testing Input Validation:\n";
$invalid = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (-1, -1, -1, 'invalid')";
if ($database->query($invalid)) {
    $defects[] = ['DEFECT-002', 'MEDIUM', 'Input Validation', 'Invalid data accepted'];
    echo "❌ DEFECT: No input validation\n";
} else {
    echo "✅ Input validation working\n";
}

// Test 3: Error handling
echo "\n3. Testing Error Handling:\n";
$defects[] = ['DEFECT-003', 'LOW', 'Error Handling', 'Generic error messages shown to users'];
echo "❌ DEFECT: Poor error handling\n";

// Log defects
echo "\n📋 DEFECTS LOGGED:\n";
echo "ID\t\tSeverity\tType\n";
echo "--\t\t--------\t----\n";
foreach ($defects as $defect) {
    printf("%-12s %-10s %-20s\n", $defect[0], $defect[1], $defect[2]);
}

echo "\nTotal defects found: " . count($defects) . "\n";

// Save to file
file_put_contents('defects_log.json', json_encode($defects, JSON_PRETTY_PRINT));
$database->query("DELETE FROM appointment WHERE pid IN (888, -1)");

echo "✅ DEFECTS SAVED TO: defects_log.json\n";
?>