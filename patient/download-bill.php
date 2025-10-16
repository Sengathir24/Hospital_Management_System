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
$patient_name = $pfetch['pname'];

$presc_id = (int)($_GET['id'] ?? 0);
$res = $database->query("SELECT pr.*, m.name as med_name, m.cost_per_pill FROM prescriptions pr JOIN medicines m ON pr.medicine_id=m.id WHERE pr.id=$presc_id AND pr.patient_id=$patient_id");
if($res->num_rows==0){
    echo "Prescription not found or access denied.";
    exit;
}
$pres = $res->fetch_assoc();

// Render a styled invoice page and generate PDF client-side (no server installs required)
$hospital = 'Edoc hospital';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Bill #<?php echo $presc_id ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root{--brand:#0d6efd;--muted:#6c757d;--card:#ffffff;--bg:#f4f7fb}
    body{font-family: Inter, Arial, Helvetica, sans-serif;background:var(--bg);padding:20px;margin:0}
    .wrap{max-width:900px;margin:24px auto}
    .card{background:var(--card);border-radius:10px;padding:28px;box-shadow:0 8px 30px rgba(13,110,253,0.06)}
    .header{display:flex;justify-content:space-between;align-items:center}
    .brand{display:flex;align-items:center}
    .brand-logo{width:64px;height:64px;border-radius:8px;background:linear-gradient(135deg,var(--brand),#6610f2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;margin-right:14px}
    .brand h1{margin:0;font-size:20px}
    .meta{text-align:right;color:var(--muted)}
    .invoice-title{margin-top:18px;margin-bottom:20px}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:12px;border-bottom:1px solid #eef2f7;text-align:left}
    th{background:#fbfdff;color:var(--muted);font-weight:600}
    .totals{margin-top:18px;display:flex;justify-content:flex-end}
    .totals .box{background:#f9fbff;padding:14px 18px;border-radius:8px;border:1px solid #eef6ff}
    .btns{display:flex;gap:10px;margin-top:18px}
    .btn{background:var(--brand);color:#fff;padding:10px 14px;border-radius:8px;text-decoration:none;border:none;cursor:pointer}
    .btn.secondary{background:#6c757d}
    @media print{ .btns{display:none} }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card" id="invoice">
      <div class="header">
        <div class="brand">
          <div class="brand-logo">E</div>
          <div>
            <h1><?php echo htmlspecialchars($hospital) ?></h1>
            <div style="color:var(--muted)">Invoice / Bill</div>
          </div>
        </div>
        <div class="meta">
          <div><strong>Bill ID:</strong> <?php echo $presc_id ?></div>
          <div><strong>Date:</strong> <?php echo $pres['prescribed_at'] ?></div>
          <div style="margin-top:8px"><strong>Patient:</strong> <?php echo htmlspecialchars($patient_name) ?></div>
        </div>
      </div>

      <div class="invoice-title"></div>

      <table>
        <thead>
          <tr><th>Medicine</th><th style="width:120px">Qty</th><th style="width:140px">Price / pill</th><th style="width:160px">Total</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo htmlspecialchars($pres['med_name']) ?></td>
            <td><?php echo intval($pres['quantity']) ?></td>
            <td><?php echo number_format($pres['cost_per_pill'],2) ?></td>
            <td><?php echo number_format($pres['total_cost'],2) ?></td>
          </tr>
        </tbody>
      </table>

      <div class="totals">
        <div class="box">
          <div style="font-size:14px;color:var(--muted)">Grand Total</div>
          <div style="font-size:20px;font-weight:700">Rs.<?php echo number_format($pres['total_cost'],2) ?></div>
        </div>
      </div>

      <div class="btns">
        <button class="btn" id="downloadPdf">Download PDF</button>
        <button class="btn secondary" id="printBtn">Print / Save as PDF</button>
      </div>

      <p style="margin-top:14px;color:var(--muted)">Thank you for choosing <?php echo htmlspecialchars($hospital) ?>.</p>
    </div>
  </div>

  <!-- html2pdf bundle (uses html2canvas + jsPDF) - CDN, no server installs required -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
  <script>
    (function(){
      const invoice = document.getElementById('invoice');
      const filename = 'Edoc_hospital_bill_<?php echo $presc_id ?>.pdf';
      document.getElementById('downloadPdf').addEventListener('click', () => {
        const opt = {
          margin:       10,
          filename:     filename,
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true },
          jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        // Generate and save the PDF
        html2pdf().set(opt).from(invoice).save();
      });

      document.getElementById('printBtn').addEventListener('click', () => {
        window.print();
      });

      // Auto-trigger download after render (optional; comment out if undesired or popup-blocked)
      setTimeout(() => {
        document.getElementById('downloadPdf').click();
      }, 600);
    })();
  </script>
</body>
</html>
