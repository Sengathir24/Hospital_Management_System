<?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    if($_POST){
        if(isset($_POST["booknow"])){
            // Always compute the appointment number server-side to avoid race conditions
            $scheduleid = intval($_POST["scheduleid"]);
            $date = $database->real_escape_string($_POST["date"]);

            // Use transaction to ensure count+insert is atomic
            $database->begin_transaction();
            try{
                // Lock the matching appointment rows for this schedule so concurrent transactions serialize
                $sqlCount = "SELECT COUNT(*) AS c FROM appointment WHERE scheduleid = ? FOR UPDATE";
                $stmtCount = $database->prepare($sqlCount);
                $stmtCount->bind_param("i", $scheduleid);
                $stmtCount->execute();
                $resCount = $stmtCount->get_result();
                $rowCount = $resCount->fetch_assoc();
                $apponum = intval($rowCount['c']) + 1;

                // Prepared insert to avoid SQL injection and ensure correctness
                $sqlInsert = "INSERT INTO appointment(pid, apponum, scheduleid, appodate) VALUES (?, ?, ?, ?)";
                $stmtInsert = $database->prepare($sqlInsert);
                $stmtInsert->bind_param("iiis", $userid, $apponum, $scheduleid, $date);
                $execOk = $stmtInsert->execute();

                if(!$execOk){
                    // Insert failed; rollback and show error
                    $database->rollback();
                    // redirect with failure (could be improved to show an error message)
                    header("location: appointment.php?action=booking-failed");
                    exit();
                }

                // Commit the transaction
                $database->commit();

                // Success — redirect to appointment page with the appointment number
                header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");
                exit();

            }catch(Exception $e){
                // Something went wrong — rollback
                $database->rollback();
                header("location: appointment.php?action=booking-failed");
                exit();
            }
        }
    }
 ?>