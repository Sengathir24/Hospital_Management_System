<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='a'){
    header("location: ../login.php");
    exit;
}
include(__DIR__ . "/../connection.php");

// Handle add medicine (use prepared statement)
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_medicine'])){
    $name = trim($_POST['name']);
    $quantity = (int)$_POST['quantity'];
    $cost = (float)$_POST['cost_per_pill'];
    if($name!=='' && $quantity>=0 && $cost>=0){
        $sqlIns = "INSERT INTO medicines (name, quantity, cost_per_pill) VALUES (?, ?, ?)";
        $stmtIns = $database->prepare($sqlIns);
        $stmtIns->bind_param('sid', $name, $quantity, $cost);
        $stmtIns->execute();
        header("Location: medicines.php");
        exit;
    }
}

// Handle delete action via GET - merge the single-file flow here
if(isset($_GET['action']) && $_GET['action']==='delete' && isset($_GET['id'])){
    $delId = intval($_GET['id']);
    $sqlDel = "DELETE FROM medicines WHERE id = ?";
    $stmtDel = $database->prepare($sqlDel);
    $stmtDel->bind_param('i', $delId);
    $stmtDel->execute();
    header("Location: medicines.php");
    exit;
}

$meds = $database->query("SELECT * FROM medicines ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Manage Medicines</title>
</head>
<body>
    <div class="container">
        <?php $active='medicines'; include(__DIR__ . '/menu.php'); ?>
        <div class="dash-body" style="margin-top:30px; padding:20px;">
            <h2>Manage Medicines</h2>
            <form method="post" style="max-width:600px;margin-bottom:20px;">
                <label>Medicine name<br><input type="text" name="name" required class="input-text"></label><br>
                <label>Quantity<br><input type="number" name="quantity" min="0" value="0" required class="input-text"></label><br>
                <label>Cost per pill<br><input type="number" name="cost_per_pill" step="0.01" min="0" value="0.00" required class="input-text"></label><br>
                <button type="submit" name="add_medicine" class="login-btn btn-primary btn">Add Medicine</button>
            </form>

            <h3>Available Medicines</h3>
            <table class="sub-table" border="0" style="width:90%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Cost/pill</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $meds->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo htmlspecialchars($row['name']) ?></td>
                            <td><?php echo $row['quantity'] ?></td>
                            <td><?php echo number_format($row['cost_per_pill'],2) ?></td>
                            <td>
                                <a href="medicines.php?action=delete&id=<?php echo $row['id'] ?>" onclick="return confirm('Delete medicine ID <?php echo $row['id'] ?>?')" class="non-style-link">
                                    <button class="btn-primary-soft btn">Delete</button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
