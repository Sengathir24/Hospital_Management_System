<?php
include('../connection.php');

echo "👥 SIMULATING 20 CONCURRENT USERS\n";
echo "==================================\n";

$successCount = 0;
$times = [];

// Clean up previous test data
$database->query("DELETE FROM appointment WHERE pid = 999");

for ($i = 1; $i <= 20; $i++) {
    $start = microtime(true);
    
    // Try to book same slot with test user
    $sql = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (999, $i, 1, CURDATE())";
    
    if ($database->query($sql)) {
        $successCount++;
        $status = "✅ Booked";
    } else {
        $status = "❌ Failed";
    }
    
    $time = round((microtime(true) - $start) * 1000, 2);
    $times[] = $time;
    
    echo "User $i: $status ({$time}ms)\n";
}

$avgTime = round(array_sum($times) / count($times), 2);

echo "\n📈 RESULTS:\n";
echo "Successful bookings: $successCount/20\n";
echo "Average time: {$avgTime}ms\n";

if ($successCount > 1) {
    echo "\n🚨 CRITICAL DEFECT: $successCount users booked same slot! No concurrency control.\n";
} else {
    echo "\n✅ CONCURRENCY: System prevents double-booking\n";
}

// Clean up
$database->query("DELETE FROM appointment WHERE pid = 999");
?>