<?php
// CLI: php verify_apponum.php <scheduleid>
// Outputs JSON with appointment sequence info for the schedule

require_once __DIR__ . '/../../connection.php';

if ($argc < 2) {
    fwrite(STDERR, "Usage: php verify_apponum.php <scheduleid>\n");
    exit(2);
}

$scheduleid = intval($argv[1]);
if ($scheduleid <= 0) {
    fwrite(STDERR, "Invalid scheduleid\n");
    exit(2);
}

$stmt = $database->prepare("SELECT apponum FROM appointment WHERE scheduleid = ? ORDER BY apponum ASC");
$stmt->bind_param('i', $scheduleid);
$stmt->execute();
$res = $stmt->get_result();

$apponums = [];
while ($row = $res->fetch_assoc()) {
    $apponums[] = intval($row['apponum']);
}

$total = count($apponums);
$duplicates = [];
$seen = [];
foreach ($apponums as $a) {
    if (isset($seen[$a])) {
        $duplicates[] = $a;
    } else {
        $seen[$a] = true;
    }
}

$missing = [];
if ($total > 0) {
    $max = max($apponums);
    for ($i = 1; $i <= $max; $i++) {
        if (!isset($seen[$i])) {
            $missing[] = $i;
        }
    }
}

$result = [
    'scheduleid' => $scheduleid,
    'total' => $total,
    'duplicates' => array_values(array_unique($duplicates)),
    'missing' => $missing,
    'detail' => $apponums,
];

echo json_encode($result);

?>
