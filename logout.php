<?php
session_start();
session_destroy();
header("Location: /smartcare_hms/login.php");
exit();
?>