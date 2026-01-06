<?php

require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session on the server
session_destroy();

// Redirect to homepage.php as requested
header("Location: homepage.php");
exit();
?>
