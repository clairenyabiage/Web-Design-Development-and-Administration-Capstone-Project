<?php
require_once 'config/config.php'; // This will also start the session

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
