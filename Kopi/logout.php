<?php
// logout.php
require 'config.php';

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("location: login.php");
exit;
?>