<?php
// doctor prescribe - integrated with doctor menu and safer DB handling
session_start();
if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='d'){
    header("location: ../login.php");
    exit;
}
include(__DIR__ . "/../connection.php");

$useremail = $_SESSION['user'];
$userrow = $database->prepare("SELECT * FROM doctor WHERE docemail = ?");
$userrow->bind_param('s', $useremail);
$userrow->execute();
$userres = $userrow->get_result();
$userfetch = $userres->fetch_assoc();
$doctor_id = (int)$userfetch['docid'];

// fetch patients and medicines (safe reads)
$patients = $database->query("SELECT pid, pname FROM patient ORDER BY pname");
$meds = $database->query("SELECT id, name, quantity, cost_per_pill FROM medicines ORDER BY name");

$error = '';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['prescribe'])){
    $patient_id = (int)$_POST['patient_id'];
    $medicine_id = (int)$_POST['medicine_id'];
    $qty = (int)$_POST['quantity'];

    $stmt = $database->prepare("SELECT quantity, cost_per_pill FROM medicines WHERE id = ?");
    $stmt->bind_param('i', $medicine_id);
    $stmt->execute();
    $mres = $stmt->get_result();
    if($mres->num_rows==0){
        $error = "Medicine not found.";
    }else{
        $m = $mres->fetch_assoc();
        if($qty<=0 || $qty > (int)$m['quantity']){
            $error = "Invalid quantity or insufficient stock.";
        }else{
            $total = $qty * (float)$m['cost_per_pill'];

            // use transaction to insert prescription and decrement stock atomically
            $database->begin_transaction();
            try{
                $ins = $database->prepare("INSERT INTO prescriptions (patient_id, doctor_id, medicine_id, quantity, total_cost) VALUES (?, ?, ?, ?, ?)");
                $ins->bind_param('iiiid', $patient_id, $doctor_id, $medicine_id, $qty, $total);
                $ins->execute();

                $up = $database->prepare("UPDATE medicines SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
                $up->bind_param('iii', $qty, $medicine_id, $qty);
                $up->execute();

                if($up->affected_rows===0){
                    // insufficient stock (concurrent change)
                    $database->rollback();
                    $error = "Insufficient stock when saving. Try again.";
                }else{
                    $database->commit();
                    header("Location: prescribe.php?success=1");
                    exit;
                }
            }catch(Exception $e){
                $database->rollback();
                $error = "Server error while saving prescription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Prescribe Medicine</title>
    <style>
        /* small enhancements for prescribe form */
        .prescribe-form{ max-width:720px; background:#fff; padding:18px; border-radius:8px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
        .prescribe-form label{ display:block; margin-bottom:10px; }
        .prescribe-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    </style>
</head>
<body>
    <div class="container">
        <?php $active='prescribe'; include(__DIR__ . '/menu.php'); ?>
        <div class="dash-body" style="margin-top:30px; padding:20px;">
            <h2>Prescribe Medicine</h2>
            <?php if($error!=''): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">Prescription recorded.</div>
            <?php endif; ?>

            <form method="post" class="prescribe-form">
                <div class="prescribe-grid">
                    <label>Patient<br>
                        <select name="patient_id" required class="input-text">
                            <option value="">Select patient</option>
                            <?php $patients->data_seek(0); while($p = $patients->fetch_assoc()): ?>
                                <option value="<?php echo $p['pid'] ?>"><?php echo htmlspecialchars($p['pname']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>
                    <label>Medicine<br>
                        <select name="medicine_id" required class="input-text">
                            <option value="">Select medicine</option>
                            <?php $meds->data_seek(0); while($m = $meds->fetch_assoc()): ?>
                                <option value="<?php echo $m['id'] ?>"><?php echo htmlspecialchars($m['name']) ?> (Available: <?php echo $m['quantity'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </label>
                    <label>Quantity<br><input type="number" name="quantity" min="1" value="1" required class="input-text"></label>
                    <div style="align-self:end;"><button type="submit" name="prescribe" class="login-btn btn-primary btn">Prescribe</button></div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
