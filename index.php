<?php
require_once 'config/config.php';

// Check if user is logged in
if (isLoggedIn()) {
    if (hasRole('lecturer')) {
        header("Location: views/dashboard_lecturer.php");
    } else {
        header("Location: views/dashboard_student.php");
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
