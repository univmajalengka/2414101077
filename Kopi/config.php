<?php
// config.php
session_start(); // Mulai session di sini agar tersedia di semua halaman

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_kopi';
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>