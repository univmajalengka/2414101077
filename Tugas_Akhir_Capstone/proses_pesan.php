<?php
include 'koneksi.php';

// Ambil data dari form
$nama = $_POST['nama'];
$hp = $_POST['hp'];
$tanggal = $_POST['tanggal'];
$waktu = $_POST['waktu'];
$peserta = $_POST['peserta'];
$harga_paket = $_POST['harga_paket'];
$total_tagihan = $_POST['total_tagihan'];

// Cek checkbox (dikirim dalam array layanan, kita perlu manual cek satu2 atau logika sederhana)
// Namun karena name checkbox sama, lebih aman cek manual dari $_POST atau manipulasi JS.
// Untuk mempermudah sesuai form di atas, kita deteksi manual berdasarkan value paket harga yg dikirim.
// Cara lebih robust adalah mengirim status checkbox via hidden input, tapi ini simplifikasi:

$layanan = isset($_POST['layanan']) ? $_POST['layanan'] : [];
$inap = in_array('1000000', $layanan) ? 1 : 0;
$trans = in_array('1200000', $layanan) ? 1 : 0;
$makan = in_array('500000', $layanan) ? 1 : 0;

// Query Insert [cite: 59]
$sql = "INSERT INTO pesanan (nama_pemesan, nomor_hp, tanggal_pesan, waktu_perjalanan, jumlah_peserta, layanan_penginapan, layanan_transportasi, layanan_makanan, harga_paket, total_tagihan) 
        VALUES ('$nama', '$hp', '$tanggal', '$waktu', '$peserta', '$inap', '$trans', '$makan', '$harga_paket', '$total_tagihan')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Pemesanan Berhasil Disimpan!'); window.location='modifikasi_pesanan.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>