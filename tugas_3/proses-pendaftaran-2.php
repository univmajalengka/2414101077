<?php

include("koneksi.php");

// cek apakah tombol daftar sudah diklik atau belum?
if(isset($_POST['daftar'])){

    // ambil data dari formulir
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $jk = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $sekolah = $_POST['sekolah_asal']; // PERBAIKAN: Menambahkan tanda $

    // IMPLEMENTASI BEST PRACTICE: Prepared Statement
    // Persiapkan template query
    $sql = "INSERT INTO calon_siswa (nama, alamat, jenis_kelamin, agama, sekolah_asal) VALUES (?, ?, ?, ?, ?)";
    
    // Inisialisasi statement
    $stmt = mysqli_stmt_init($db);

    // Cek kesiapan statement
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind parameter ke statement
        // "sssss" berarti 5 parameter bertipe String (String, String, String, String, String)
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $alamat, $jk, $agama, $sekolah);

        // Eksekusi query yang sudah aman
        $execute = mysqli_stmt_execute($stmt);

        // Cek keberhasilan
        if( $execute ) {
            // kalau berhasil alihkan ke halaman index.php dengan status=sukses
            header('Location: index.php?status=sukses');
        } else {
            // kalau gagal alihkan ke halaman index.php dengan status=gagal
            header('Location: index.php?status=gagal');
        }
        
        // Tutup statement
        mysqli_stmt_close($stmt);

    } else {
        // Jika statement gagal disiapkan
        die("Query Error: " . mysqli_error($db));
    }

} else {
    die("Akses dilarang...");
}

?>