<?php
session_start();
$_SESSION['admin_logged_in'] = false;
unset($_SESSION['admin_logged_in']);
session_destroy();
header('Location: login.php?pesan=logout');
exit;
?>
