<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pemesanan - Wisata Majalengka</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Form Pemesanan Paket Wisata</h1>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="modifikasi_pesanan.php">Modifikasi Pesanan</a>
        </nav>
    </header>

    <div class="container">
        <div class="form-wrapper">
            <form action="proses_pesan.php" method="POST" onsubmit="return validateForm()">
                
                <label for="nama">Nama Pemesan:</label>
                <input type="text" id="nama" name="nama" required>

                <label for="hp">Nomor HP/Telp:</label>
                <input type="text" id="hp" name="hp" required>

                <label for="tanggal">Tanggal Pesan:</label>
                <input type="date" id="tanggal" name="tanggal" required>

                <label for="waktu">Waktu Pelaksanaan Perjalanan (Hari):</label>
                <input type="number" id="waktu" name="waktu" min="1" value="1" required oninput="hitungTotal()">

                <label>Pelayanan Paket Perjalanan:</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="inap" name="layanan[]" value="1000000" onchange="hitungTotal()"> 
                    <label for="inap">Penginapan (Rp 1.000.000)</label><br>

                    <input type="checkbox" id="transport" name="layanan[]" value="1200000" onchange="hitungTotal()"> 
                    <label for="transport">Transportasi (Rp 1.200.000)</label><br>

                    <input type="checkbox" id="makan" name="layanan[]" value="500000" onchange="hitungTotal()"> 
                    <label for="makan">Servis/Makan (Rp 500.000)</label>
                </div>

                <label for="peserta">Jumlah Peserta:</label>
                <input type="number" id="peserta" name="peserta" min="1" value="1" required oninput="hitungTotal()">

                <label for="harga">Harga Paket Perjalanan:</label>
                <input type="text" id="harga_display" readonly>
                <input type="hidden" id="harga_val" name="harga_paket">

                <label for="tagihan">Jumlah Tagihan:</label>
                <input type="text" id="tagihan_display" readonly>
                <input type="hidden" id="tagihan_val" name="total_tagihan">

                <div class="btn-group">
                    <button type="submit" class="btn-save">Simpan</button>
                    <button type="button" class="btn-calc" onclick="hitungTotal()">Hitung</button>
                    <button type="reset" class="btn-reset" onclick="resetForm()">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        [cite_start]// Logika Perhitungan JavaScript [cite: 51, 64]
        function hitungTotal() {
            let waktu = parseInt(document.getElementById('waktu').value) || 0;
            let peserta = parseInt(document.getElementById('peserta').value) || 0;
            
            let hargaInap = document.getElementById('inap').checked ? 1000000 : 0;
            let hargaTrans = document.getElementById('transport').checked ? 1200000 : 0;
            let hargaMakan = document.getElementById('makan').checked ? 500000 : 0;

            // Rumus: Harga Paket = Total layanan yang dipilih [cite: 57]
            let hargaPaket = hargaInap + hargaTrans + hargaMakan;

            // Rumus: Tagihan = Waktu x Peserta x Harga Paket [cite: 58]
            let totalTagihan = waktu * peserta * hargaPaket;

            // Tampilkan ke user (format Rupiah)
            document.getElementById('harga_display').value = "Rp " + hargaPaket.toLocaleString('id-ID');
            document.getElementById('tagihan_display').value = "Rp " + totalTagihan.toLocaleString('id-ID');

            // Simpan nilai asli ke input hidden untuk dikirim ke PHP
            document.getElementById('harga_val').value = hargaPaket;
            document.getElementById('tagihan_val').value = totalTagihan;
        }

        // Validasi Form [cite: 48, 50]
        function validateForm() {
            let nama = document.getElementById('nama').value;
            let hp = document.getElementById('hp').value;
            let tagihan = document.getElementById('tagihan_val').value;

            if (nama == "" || hp == "") {
                alert("Data form pemesanan harus terisi lengkap!");
                return false;
            }
            if (tagihan == "" || tagihan == "0") {
                alert("Silakan pilih layanan paket wisata terlebih dahulu.");
                return false;
            }
            return true;
        }

        function resetForm() {
            document.getElementById('harga_display').value = "";
            document.getElementById('tagihan_display').value = "";
        }
    </script>
</body>
</html>