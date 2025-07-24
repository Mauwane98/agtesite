<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the admin login page
header('Location: admin-login.html');
exit;
?>
