<?php
session_start();
unset($_SESSION['user_logged_in'], $_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_username']);
header('Location: ../index.php');
exit;
