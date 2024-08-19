<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: login_admin.php");
exit;
?>
