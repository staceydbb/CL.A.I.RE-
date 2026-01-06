<?php

ob_start();
require_once 'db_connect.php';


if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_to_delete = $_GET['id'];
    $pathologist_id = $_SESSION['user_id'];

    try {
     
        $stmt = $pdo->prepare("DELETE FROM Patients_Table WHERE patient_id = ? AND pathologist_id = ?");
        $stmt->execute([$id_to_delete, $pathologist_id]);
        
        // Check if a row was actually deleted
        if ($stmt->rowCount() > 0) {
            // Return to the cases list with a clear success message
            header("Location: patients.php?msg=Patient+Successfully+Deleted");
        } else {
            // No row affected (either ID didn't exist or didn't belong to this pathologist)
            header("Location: patients.php?error=Record+not+found+or+access+denied");
        }
        exit();
        
    } catch (PDOException $e) {
        // In case of error, redirect back with error message
        header("Location: patients.php?error=" . urlencode("Delete failed: " . $e->getMessage()));
        exit();
    }
} else {
    
    header("Location: patients.php");
    exit();
}

ob_end_flush();
?>
