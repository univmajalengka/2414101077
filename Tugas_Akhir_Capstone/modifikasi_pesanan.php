<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan - Wisata Majalengka</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Daftar Pesanan Wisata</h1>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="pemesanan.php">Tambah Pesanan</a>
        </nav>
    </header>

    <div class="container">
        <table border="1" cellpadding="10" cellspacing="0" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Phone</th>
                    <th>Peserta</th>
                    <th>Hari</th>
                    <th>Akomodasi</th>
                    <th>Transport</th>
                    <th>Makan</th>
                    <th>Harga Paket</th>
                    <th>Total Tagihan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM pesanan ORDER BY id DESC";
                $result = mysqli_query($conn, $sql);
                $no = 1;

                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $row['nama_pemesan'] . "</td>";
                    echo "<td>" . $row['nomor_hp'] . "</td>";
                    echo "<td>" . $row['jumlah_peserta'] . "</td>";
                    echo "<td>" . $row['waktu_perjalanan'] . "</td>";
                    // Konversi 1/0 ke Y/N [cite: 29]
                    echo "<td>" . ($row['layanan_penginapan'] ? 'Y' : 'N') . "</td>";
                    echo "<td>" . ($row['layanan_transportasi'] ? 'Y' : 'N') . "</td>";
                    echo "<td>" . ($row['layanan_makanan'] ? 'Y' : 'N') . "</td>";
                    echo "<td>Rp " . number_format($row['harga_paket'],0,',','.') . "</td>";
                    echo "<td>Rp " . number_format($row['total_tagihan'],0,',','.') . "</td>";
                    echo "<td>
                            <a href='edit_pesanan.php?id=".$row['id']."' class='btn-edit'>Edit</a> 
                            <a href='hapus_pesanan.php?id=".$row['id']."' class='btn-delete' onclick=\"return confirm('Anda yakin akan menghapus data ini?')\">Delete</a>
                          </td>"; // Pop-up konfirmasi hapus [cite: 62]
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>