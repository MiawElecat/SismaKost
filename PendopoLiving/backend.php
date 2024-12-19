<?php
// Baca file CSV
$data = array_map('str_getcsv', file('data_pemesanan_cleaned.csv'));

// Hitung penyewa per bulan
$penyewa_per_bulan = [];
foreach ($data as $row) {
    $date = strtotime($row[1]); // Asumsi pemesanan_kamar di kolom kedua
    $month = date('M', $date);
    if (!isset($penyewa_per_bulan[$month])) {
        $penyewa_per_bulan[$month] = 0;
    }
    $penyewa_per_bulan[$month]++;
}

// Siapkan data untuk JavaScript
$labels = array_keys($penyewa_per_bulan);
$values = array_values($penyewa_per_bulan);

// Kirim ke frontend
$penyewa_per_bulan = ['labels' => $labels, 'values' => $values];
?>
