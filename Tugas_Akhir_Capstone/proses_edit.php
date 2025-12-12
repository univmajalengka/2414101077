<?php
include 'koneksi.php';
$id = $_POST['id'];
$nama = $_POST['nama'];
$hp = $_POST['hp'];
$tanggal = $_POST['tanggal'];
$waktu = $_POST['waktu'];
$peserta = $_POST['peserta'];
$harga_paket = $_POST['harga_paket'];
$tagihan = $_POST['total_tagihan'];

$layanan = isset($_POST['layanan']) ? $_POST['layanan'] : [];
$inap = in_array('1000000', $layanan) ? 1 : 0;
$trans = in_array('1200000', $layanan) ? 1 : 0;
$makan = in_array('500000', $layanan) ? 1 : 0;

$sql = "UPDATE pesanan SET 
        nama_pemesan='$nama', nomor_hp='$hp', tanggal_pesan='$tanggal', 
        waktu_perjalanan='$waktu', jumlah_peserta='$peserta', 
        layanan_penginapan='$inap', layanan_transportasi='$trans', layanan_makanan='$makan',
        harga_paket='$harga_paket', total_tagihan='$tagihan' 
        WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    header("Location: modifikasi_pesanan.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>