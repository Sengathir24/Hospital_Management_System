<?php
include('../connection.php');

echo "🔄 REGRESSION TESTING\n";
echo "=====================\n";

$tests = [
    ['Basic appointment creation', "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (777, 200, 1, CURDATE())"],
    ['Appointment retrieval', "SELECT * FROM appointment LIMIT 1"],
    ['Patient join', "SELECT p.pname, a.appoid FROM patient p JOIN appointment a ON p.pid = a.pid LIMIT 1"],
    ['Schedule join', "SELECT s.title, a.appoid FROM schedule s JOIN appointment a ON s.scheduleid = a.scheduleid LIMIT 1"]
];

$passed = 0;
echo "\nRunning regression tests:\n";

foreach ($tests as $test) {
    $result = $database->query($test[1]);
    
    if ($result) {
        echo "✅ PASS: {$test[0]}\n";
        $passed++;
    } else {
        echo "❌ FAIL: {$test[0]} - " . $database->error . "\n";
    }
}

$percent = round(($passed / count($tests)) * 100, 2);
echo "\nRESULTS: $passed/" . count($tests) . " tests passed ($percent%)\n";

// Clean up
$database->query("DELETE FROM appointment WHERE pid = 777");

if ($percent == 100) {
    echo "✅ REGRESSION: All tests passed\n";
} else {
    echo "⚠️  REGRESSION: Some tests failed\n";
}
?>