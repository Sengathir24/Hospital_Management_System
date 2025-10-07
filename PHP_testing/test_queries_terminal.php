<?php
include('../connection.php');

echo "📊 DATABASE QUERY VALIDATION\n";
echo "============================\n";

// Test 1: Table structure
echo "\n1. Appointment Table Structure:\n";
$result = $database->query("DESCRIBE appointment");
echo "Field\t\tType\t\tNull\tKey\n";
echo "-----\t\t----\t\t----\t---\n";
while ($row = $result->fetch_assoc()) {
    printf("%-15s %-15s %-5s %-5s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'], 
        $row['Key']
    );
}

// Test 2: Index analysis
echo "\n2. Index Analysis:\n";
$result = $database->query("SHOW INDEX FROM appointment");
echo "Index\t\tColumn\t\tUnique\n";
echo "-----\t\t------\t\t------\n";
while ($row = $result->fetch_assoc()) {
    $unique = $row['Non_unique'] ? 'No' : 'Yes';
    printf("%-15s %-15s %-5s\n", 
        $row['Key_name'], 
        $row['Column_name'], 
        $unique
    );
}

// Test 3: Performance
echo "\n3. Query Performance:\n";
$start = microtime(true);
$result = $database->query("SELECT COUNT(*) as count FROM appointment");
$row = $result->fetch_assoc();
$time = round((microtime(true) - $start) * 1000, 2);
echo "Count query: {$time}ms for {$row['count']} appointments\n";

echo "\n✅ DATABASE VALIDATION: COMPLETE\n";
?>