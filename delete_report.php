<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    session_write_close();
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$record_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM Patient_Records_Table WHERE record_id = ?");
    $stmt->execute([$record_id]);
} catch (PDOException $e) {
    // Optional: log error or show message
}

header("Location: dashboard.php");
exit();
