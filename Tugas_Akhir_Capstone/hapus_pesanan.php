<?php
include 'koneksi.php';
$id = $_GET['id'];
$sql = "DELETE FROM pesanan WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    header("Location: modifikasi_pesanan.php");
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>