<?php
include 'koneksi.php'; // Menghubungkan ke database

// Mengecek apakah idPembayaran ada di URL
if (isset($_GET['id'])) {
    $idPembayaran = $_GET['id'];

    // Mengambil data pembayaran berdasarkan idPembayaran
    $sql = $koneksi->query("SELECT * FROM pembayaran WHERE idPembayaran = '$idPembayaran'");
    if ($sql->num_rows == 0) {
        echo "Data tidak ditemukan!";
        exit;
    }
    $data = $sql->fetch_assoc();
} else {
    echo "ID Pembayaran tidak ditemukan!";
    exit;
}

// Proses update jika form disubmit
if (isset($_POST['update'])) {
    $tanggalPembayaran = $_POST['tanggalPembayaran'];
    $batasPembayaran = $_POST['batasPembayaran'];
    $durasiSewa = $_POST['durasiSewa'];
    $statusPembayaran = $_POST['statusPembayaran'];
    $jatuhTempo = $_POST['jatuhTempo'];

    // Melakukan update ke database
    $updateSql = $koneksi->query("UPDATE pembayaran 
                                  SET tanggalPembayaran = '$tanggalPembayaran', 
                                      batasPembayaran = '$batasPembayaran', 
                                      durasiSewa = '$durasiSewa', 
                                      StatusPembayaran = '$statusPembayaran', 
                                      jatuh_tempo = '$jatuhTempo'
                                  WHERE idPembayaran = '$idPembayaran'");

    if ($updateSql) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='?page=pembayaran';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pembayaran</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Data Pembayaran</h2>
        <form method="post">
            <!-- <div class="form-group">
                <label for="tanggalPembayaran">Tanggal Pembayaran</label>
                <input type="date" class="form-control" id="tanggalPembayaran" name="tanggalPembayaran" value="<?php echo htmlspecialchars($data['tanggalPembayaran']); ?>" required>
            </div> -->
            <div class="form-group">
                <label for="batasPembayaran">Batas Pembayaran</label>
                <input type="date" class="form-control" id="batasPembayaran" name="batasPembayaran" value="<?php echo htmlspecialchars($data['batasPembayaran']); ?>" required>
            </div>
            <div class="form-group">
                <label for="durasiSewa">Durasi Sewa (bulan)</label>
                <input type="number" class="form-control" id="durasiSewa" name="durasiSewa" value="<?php echo htmlspecialchars($data['durasiSewa']); ?>" required>
            </div>
            <div class="form-group">
                <label for="statusPembayaran">Status Pembayaran</label>
                <select class="form-control" id="statusPembayaran" name="statusPembayaran" required>
                    <option value="Lunas" <?php echo ($data['StatusPembayaran'] === 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                    <option value="Belum Lunas" <?php echo ($data['StatusPembayaran'] === 'Belum Lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                </select>
            </div>
            <div class="form-group">
                <label for="jatuhTempo">Jatuh Tempo</label>
                <input type="date" class="form-control" id="jatuhTempo" name="jatuhTempo" value="<?php echo htmlspecialchars($data['jatuh_tempo']); ?>">
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="?page=pembayaran" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="path/to/bootstrap.bundle.min.js"></script>
</body>
</html>
