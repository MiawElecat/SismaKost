<?php
include('koneksi.php');
session_start(); // Mulai session

// Periksa apakah ID Pemesanan dan ID Penyewa telah tersedia
$idPemesanan = isset($_GET['idPemesanan']) ? $_GET['idPemesanan'] : '';
$idPenyewa = isset($_GET['idPenyewa']) ? $_GET['idPenyewa'] : '';

if (empty($idPemesanan) || empty($idPenyewa)) {
    die('ID Pemesanan atau ID Penyewa tidak valid.');
}

// Ambil data pembayaran dan pemesanan dari database
$sql = "SELECT p.id_pemesanan, p.status AS statusPemesanan, p.status_uang_muka, p.sisa_pembayaran, pm.tanggalPembayaran, pm.durasiSewa, pm.StatusPembayaran, pm.jatuh_tempo, k.namaKamar
        FROM pemesanan p
        JOIN pembayaran pm ON p.id_pemesanan = pm.id_pemesanan
        JOIN kamar k ON p.idKamar = k.idKamar
        WHERE p.id_pemesanan = ? AND pm.idPenyewa = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $idPemesanan, $idPenyewa);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pembayaran = mysqli_fetch_assoc($result);

if (!$pembayaran) {
    die('Data pembayaran tidak ditemukan.');
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 10px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #4CAF50;
        }
        p {
            font-size: 1rem;
            margin: 10px 0;
        }
        .status {
            font-weight: bold;
        }
        .back-button {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-button a {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-button a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Status Pembayaran</h2>
        <p><strong>Kamar:</strong> <?php echo htmlspecialchars($pembayaran['namaKamar']); ?></p>
        <p><strong>Tanggal Pembayaran:</strong> <?php echo $pembayaran['tanggalPembayaran']; ?></p>
        <p><strong>Durasi Sewa:</strong> <?php echo $pembayaran['durasiSewa']; ?> bulan</p>
        <p><strong>Status Pembayaran:</strong> <span class="status"><?php echo $pembayaran['StatusPembayaran']; ?></span></p>
        <p><strong>Jatuh Tempo:</strong> <?php echo $pembayaran['jatuh_tempo']; ?></p>
        <p><strong>Status Pemesanan:</strong> <span class="status"><?php echo $pembayaran['statusPemesanan']; ?></span></p>

        <?php if ($pembayaran['status_uang_muka'] == 'DP 30%' && $pembayaran['sisa_pembayaran'] > 0): ?>
            <p><strong>Sisa Pembayaran:</strong> Rp. <?php echo number_format($pembayaran['sisa_pembayaran'], 2, ',', '.'); ?></p>
        <?php endif; ?>

        <div class="back-button">
            <a href="index.php">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
