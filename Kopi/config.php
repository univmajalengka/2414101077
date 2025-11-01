<?php
// config.php
session_start(); // Mulai session di sini agar tersedia di semua halaman

$host = 'localhost';
$user = 'tugaspabw_2414101077';
$pass = 'Roeidcafe33719';
$db   = 'tugaspabw_2414101077';
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
