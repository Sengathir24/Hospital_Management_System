<?php
echo "📈 DEFECT DENSITY CALCULATION\n";
echo "=============================\n";

// Get defects
$defects = file_exists('defects_log.json') ? json_decode(file_get_contents('defects_log.json'), true) : [];
$defectCount = count($defects);
$loc = 5000; // Estimated lines of code
$density = ($defectCount / $loc) * 1000;

echo "\nMETRICS:\n";
echo "Total Defects: $defectCount\n";
echo "Lines of Code: $loc\n";
echo "Defect Density: " . round($density, 2) . " defects/KLOC\n";

// Quality assessment
echo "\nQUALITY ASSESSMENT:\n";
if ($density <= 1) {
    echo "✅ EXCELLENT: <1 defects/KLOC\n";
} elseif ($density <= 5) {
    echo "✅ GOOD: 1-5 defects/KLOC\n";
} elseif ($density <= 10) {
    echo "⚠️  AVERAGE: 5-10 defects/KLOC\n";
} else {
    echo "❌ POOR: >10 defects/KLOC\n";
}

echo "\nINDUSTRY STANDARDS:\n";
echo "Excellent: 0-1 defects/KLOC\n";
echo "Good: 1-5 defects/KLOC\n";
echo "Average: 5-10 defects/KLOC\n";
echo "Poor: 10+ defects/KLOC\n";
?>