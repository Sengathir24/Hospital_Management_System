<?php
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

$stmt = $database->query("SELECT pr.id, pr.medicine_id, pr.quantity, pr.total_cost, pr.prescribed_at, m.name AS med_name FROM prescriptions pr JOIN medicines m ON pr.medicine_id=m.id WHERE pr.patient_id=$patient_id ORDER BY pr.prescribed_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/patient.css">
    <title>My Prescriptions</title>
    <style>
        .presc-card{ background:#fff;padding:18px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06); }
        .presc-table td,.presc-table th{ padding:10px 12px;text-align:left; }
        .bill-actions a{ display:inline-block;padding:6px 10px;border-radius:6px;background:var(--btnnicetext);color:#fff;text-decoration:none;margin-right:6px }
        @media (max-width:800px){ .presc-table thead{ display:none } .presc-table tr{ display:block;margin-bottom:10px } .presc-table td{ display:block;text-align:right; } .presc-table td:before{ float:left;font-weight:600;content:attr(data-label); }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php $active='prescriptions'; include(__DIR__ . '/menu.php'); ?>
        <div class="dash-body" style="margin-top:30px; padding:20px;">
            <div class="presc-card">
                <h2 style="margin-top:0;">My Prescriptions</h2>
                <div style="overflow:auto;">
                <table class="sub-table presc-table" border="0" style="width:100%">
                    <thead>
                        <tr><th>ID</th><th>Medicine</th><th>Qty</th><th>Cost</th><th>Date</th><th>Bill</th></tr>
                    </thead>
                    <tbody>
                        <?php while($r = $stmt->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID"><?php echo $r['id'] ?></td>
                                <td data-label="Medicine"><?php echo htmlspecialchars($r['med_name']) ?></td>
                                <td data-label="Qty"><?php echo $r['quantity'] ?></td>
                                <td data-label="Cost"><?php echo number_format($r['total_cost'],2) ?></td>
                                <td data-label="Date"><?php echo $r['prescribed_at'] ?></td>
                                <td data-label="Bill" class="bill-actions">
                                    <a class="non-style-link" href="download-bill.php?id=<?php echo $r['id'] ?>">Print</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
