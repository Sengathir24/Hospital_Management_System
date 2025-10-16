<?php
// Generates PDF invoice using DOMPDF. Assumes dompdf is installed and autoloadable via Composer.
session_start();
if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='p'){
    header("location: ../login.php");
    exit;
}
include(__DIR__ . "/../connection.php");

$useremail = $_SESSION['user'];
$prow = $database->query("select * from patient where pemail='$useremail'");
$pfetch = $prow->fetch_assoc();
$patient_id = $pfetch['pid'];
$patient_name = $pfetch['pname'];

$presc_id = (int)($_GET['id'] ?? 0);
$res = $database->query("SELECT pr.*, m.name as med_name, m.cost_per_pill FROM prescriptions pr JOIN medicines m ON pr.medicine_id=m.id WHERE pr.id=$presc_id AND pr.patient_id=$patient_id");
if($res->num_rows==0){
    echo "Prescription not found or access denied.";
    exit;
}
$pres = $res->fetch_assoc();

// Try to load DOMPDF
if(!file_exists(__DIR__ . '/../vendor/autoload.php')){
    echo "DOMPDF not installed. Please run 'composer require dompdf/dompdf' in the project root.";
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;

$hospital = 'Edoc hospital';
$html = '<!doctype html><html><head><meta charset="utf-8"><title>Bill #' . $presc_id . '</title>';
$html .= '<style>body{font-family: Arial, Helvetica, sans-serif;} table{border-collapse:collapse;width:100%}table,td,th{border:1px solid #ccc;padding:8px}</style>';
$html .= '</head><body>';
$html .= '<h1>' . htmlspecialchars($hospital) . '</h1>';
$html .= '<h2>Invoice / Bill</h2>';
$html .= '<p><strong>Bill ID:</strong> ' . $presc_id . '<br>';
$html .= '<strong>Patient:</strong> ' . htmlspecialchars($patient_name) . '<br>';
$html .= '<strong>Date:</strong> ' . $pres['prescribed_at'] . '</p>';
$html .= '<table><tr><th>Medicine</th><th>Qty</th><th>Price/pill</th><th>Total</th></tr>';
$html .= '<tr><td>' . htmlspecialchars($pres['med_name']) . '</td><td>' . intval($pres['quantity']) . '</td><td>' . number_format($pres['cost_per_pill'],2) . '</td><td>' . number_format($pres['total_cost'],2) . '</td></tr>';
$html .= '</table><p><strong>Grand Total:</strong> ' . number_format($pres['total_cost'],2) . '</p>';
$html .= '<p>Thank you for choosing ' . htmlspecialchars($hospital) . '.</p>';
$html .= '</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output();

$filename = 'Edoc_hospital_bill_' . $presc_id . '.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $pdfOutput;
exit;
