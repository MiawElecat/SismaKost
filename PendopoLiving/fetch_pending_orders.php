<?php
include 'koneksi.php';

// Ambil parameter pencarian jika ada
$searchTerm = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : null;

// Query untuk data awal (tanpa pencarian, hanya 5 data)
if ($searchTerm === null) {
    $query = "SELECT id_pemesanan, id_penyewa, idKamar, pemesanan_kamar, uang_muka, status_uang_muka, bukti_transfer, status
              FROM pemesanan
              WHERE status IN ('Menunggu Dikonfirmasi', 'Perpanjangan')
              LIMIT 5";
    $stmt = $koneksi->prepare($query);
} else {
    // Query untuk pencarian (cari di semua data)
    $query = "SELECT id_pemesanan, id_penyewa, idKamar, pemesanan_kamar, uang_muka, status_uang_muka, bukti_transfer, status
              FROM pemesanan
              WHERE status IN ('Menunggu Dikonfirmasi', 'Perpanjangan')
              AND (id_pemesanan LIKE ? OR id_penyewa LIKE ? OR idKamar LIKE ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

// Masukkan data ke dalam array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
