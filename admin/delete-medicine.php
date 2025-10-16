<?php
session_start();

if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='a'){
    header("location: ../login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("location: medicines.php");
    exit;
}

include(__DIR__ . "/../connection.php");

$id = intval($_GET['id']);

$sql = "DELETE FROM medicines WHERE id = ?";
$stmt = $database->prepare($sql);
$stmt->bind_param('i', $id);
$ok = $stmt->execute();

if($ok){
    header("location: medicines.php?action=deleted");
}else{
    header("location: medicines.php?action=delete-failed");
}
exit;

?>
