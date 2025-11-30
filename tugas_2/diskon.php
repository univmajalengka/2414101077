<?php

function hitungDiskon($totalBelanja) {
    
    $diskon = 0;

    if ($totalBelanja >= 100000) {
        $diskon = 0.10 * $totalBelanja;
    } 

    elseif ($totalBelanja >= 50000 && $totalBelanja < 100000) {
        $diskon = 0.05 * $totalBelanja;
    } 

    else {
        $diskon = 0;
    }

    return $diskon;
}

$totalBelanja = 120000;

$diskon = hitungDiskon($totalBelanja);
 
$totalBayar = $totalBelanja - $diskon;

echo "=== Rincian Pembayaran ===\n";
echo "Total Belanja : Rp " . number_format($totalBelanja, 0, ',', '.') . "\n";
echo "Diskon        : Rp " . number_format($diskon, 0, ',', '.') . "\n";
echo "Total Bayar   : Rp " . number_format($totalBayar, 0, ',', '.') . "\n";

?>
