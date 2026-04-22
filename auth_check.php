<?php
session_start();

// Not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /smartcare_hms/login.php");
    exit();
}

// Role check function
function allow_roles($roles = []) {
    if (!in_array($_SESSION['role'], $roles)) {
        echo "<h3 style='text-align:center;margin-top:50px;'>⛔ Access Denied</h3>";
        exit();
    }
}
?>