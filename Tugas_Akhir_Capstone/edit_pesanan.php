<?php
include 'koneksi.php';
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$id");
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body onload="hitungTotal()"> <div class="container">
        <h2>Edit Pesanan</h2>
        <form action="proses_edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            
            <label>Nama Pemesan:</label>
            <input type="text" name="nama" value="<?php echo $data['nama_pemesan']; ?>" required>
            
            <label>Nomor HP:</label>
            <input type="text" name="hp" value="<?php echo $data['nomor_hp']; ?>" required>
            
            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?php echo $data['tanggal_pesan']; ?>" required>
            
            <label>Waktu (Hari):</label>
            <input type="number" id="waktu" name="waktu" value="<?php echo $data['waktu_perjalanan']; ?>" oninput="hitungTotal()">
            
            <label>Layanan:</label><br>
            <input type="checkbox" id="inap" name="layanan[]" value="1000000" <?php echo ($data['layanan_penginapan']?'checked':''); ?> onchange="hitungTotal()"> Penginapan<br>
            <input type="checkbox" id="transport" name="layanan[]" value="1200000" <?php echo ($data['layanan_transportasi']?'checked':''); ?> onchange="hitungTotal()"> Transportasi<br>
            <input type="checkbox" id="makan" name="layanan[]" value="500000" <?php echo ($data['layanan_makanan']?'checked':''); ?> onchange="hitungTotal()"> Makan<br>
            
            <label>Jumlah Peserta:</label>
            <input type="number" id="peserta" name="peserta" value="<?php echo $data['jumlah_peserta']; ?>" oninput="hitungTotal()">
            
            <label>Total Tagihan:</label>
            <input type="text" id="tagihan_display" readonly>
            <input type="hidden" id="tagihan_val" name="total_tagihan">
            <input type="hidden" id="harga_val" name="harga_paket">

            <button type="submit" class="btn-save">Update</button>
            <a href="modifikasi_pesanan.php" class="btn-reset">Batal</a>
        </form>
    </div>

    <script>
        function hitungTotal() {
            let waktu = parseInt(document.getElementById('waktu').value) || 0;
            let peserta = parseInt(document.getElementById('peserta').value) || 0;
            let hargaInap = document.getElementById('inap').checked ? 1000000 : 0;
            let hargaTrans = document.getElementById('transport').checked ? 1200000 : 0;
            let hargaMakan = document.getElementById('makan').checked ? 500000 : 0;

            let hargaPaket = hargaInap + hargaTrans + hargaMakan;
            let totalTagihan = waktu * peserta * hargaPaket;

            document.getElementById('tagihan_display').value = "Rp " + totalTagihan.toLocaleString('id-ID');
            document.getElementById('tagihan_val').value = totalTagihan;
            document.getElementById('harga_val').value = hargaPaket;
        }
    </script>
</body>
</html>