<?php
include 'koneksi.php';

// Ambil ID pemesanan yang diteruskan melalui URL
$id_pemesanan = $_GET['idPemesanan'] ?? null;

// Jika ID pemesanan tidak ada, arahkan kembali ke halaman pemesanan
if (!$id_pemesanan) {
    echo '<script>alert("ID Pemesanan tidak valid!"); window.location.href="?page=pemesanan";</script>';
    exit;
}

// Ambil data pemesanan berdasarkan ID
$sql = $koneksi->query("SELECT * FROM pemesanan WHERE id_pemesanan = '$id_pemesanan'");
$data = $sql->fetch_assoc();

// Jika data tidak ditemukan, arahkan kembali ke halaman pemesanan
if (!$data) {
    echo '<script>alert("Data pemesanan tidak ditemukan!"); window.location.href="?page=pemesanan";</script>';
    exit;
}

// Proses jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    $pemesanan_kamar = $_POST['pemesanan_kamar'];
    $uang_muka = $_POST['uang_muka'];
    $status_uang_muka = $_POST['status_uang_muka'];
    $tenggat_uang_muka = $_POST['tenggat_uang_muka'];
    $mulai_menempati_kos = $_POST['mulai_menempati_kos'];
    $batas_menempati_kos = $_POST['batas_menempati_kos'];
    $status = $_POST['status'];

    // Update data pemesanan
    $update = $koneksi->query("UPDATE pemesanan SET 
        pemesanan_kamar='$pemesanan_kamar',
        uang_muka='$uang_muka',
        status_uang_muka='$status_uang_muka',
        tenggat_uang_muka='$tenggat_uang_muka',
        mulai_menempati_kos='$mulai_menempati_kos',
        batas_menempati_kos='$batas_menempati_kos',
        status='$status'
        WHERE id_pemesanan='$id_pemesanan'");

    if ($update) {
        echo '<script>alert("Data berhasil diperbarui!"); window.location.href="?page=pemesanan";</script>';
    } else {
        echo '<script>alert("Data gagal diperbarui. Error: ' . $koneksi->error . '");</script>';
    }
}
?>

<!-- Tampilan Form Edit -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-lg" style="width: 50%;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Edit Data Pemesanan</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="pemesanan_kamar" class="form-label">Tanggal Pemesanan</label>
                        <input type="date" id="pemesanan_kamar" name="pemesanan_kamar" class="form-control" 
                               value="<?= htmlspecialchars($data['pemesanan_kamar']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="uang_muka" class="form-label">Uang Muka</label>
                        <input type="number" id="uang_muka" name="uang_muka" class="form-control" 
                               value="<?= htmlspecialchars($data['uang_muka']) ?>" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="status_uang_muka" class="form-label">Status Uang Muka</label>
                        <select id="status_uang_muka" name="status_uang_muka" class="form-select" required>
                            <option value="DP 30%" <?= $data['status_uang_muka'] == 'DP 30%' ? 'selected' : '' ?>>DP 30%</option>
                            <option value="Bayar Penuh" <?= $data['status_uang_muka'] == 'Bayar Penuh' ? 'selected' : '' ?>>Bayar Penuh</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tenggat_uang_muka" class="form-label">Tenggat Uang Muka</label>
                        <input type="date" id="tenggat_uang_muka" name="tenggat_uang_muka" class="form-control" 
                               value="<?= htmlspecialchars($data['tenggat_uang_muka']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mulai_menempati_kos" class="form-label">Mulai Menempati Kos</label>
                        <input type="date" id="mulai_menempati_kos" name="mulai_menempati_kos" class="form-control" 
                               value="<?= htmlspecialchars($data['mulai_menempati_kos']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="batas_menempati_kos" class="form-label">Batas Menempati Kos</label>
                        <input type="date" id="batas_menempati_kos" name="batas_menempati_kos" class="form-control" 
                               value="<?= htmlspecialchars($data['batas_menempati_kos']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Pemesanan</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="Menunggu Pembayaran" <?= $data['status'] == 'Menunggu Pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                            <option value="Menunggu Dikonfirmasi" <?= $data['status'] == 'Menunggu Dikonfirmasi' ? 'selected' : '' ?>>Menunggu Dikonfirmasi</option>
                            <option value="Dikonfirmasi" <?= $data['status'] == 'Dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                            <option value="Dibatalkan" <?= $data['status'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            <option value="Perpanjangan" <?= $data['status'] == 'Perpanjangan' ? 'selected' : '' ?>>Perpanjangan</option>
                        </select>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary w-100 mt-2" onclick="history.back()">Kembali</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
