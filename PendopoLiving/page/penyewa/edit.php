<?php
// Ambil ID penyewa dari parameter GET
$idPenyewa = isset($_GET['idPenyewa']) ? $_GET['idPenyewa'] : null;
if (!$idPenyewa) {
    die("ID Penyewa tidak ditemukan.");
}

// Query untuk mendapatkan data penyewa berdasarkan ID
$sql = $koneksi->query("SELECT * FROM penyewa WHERE idPenyewa = '$idPenyewa'");
$data = $sql->fetch_assoc();

if (!$data) {
    die("Data penyewa tidak ditemukan.");
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPenyewa = $_POST['namaPenyewa'];
    $noTelepon = $_POST['noTelepon'];
    $email = $_POST['email'];

    // Validasi sederhana
    if (empty($namaPenyewa) || empty($noTelepon) || empty($email)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        // Query untuk update data penyewa
        $update = $koneksi->query("
            UPDATE penyewa 
            SET namaPenyewa = '$namaPenyewa', noTelepon = '$noTelepon', email = '$email' 
            WHERE idPenyewa = '$idPenyewa'
        ");

        if ($update) {
            echo "
                <script>
                    alert('Data berhasil diperbarui.');
                    window.location.href = '?page=penyewa';
                </script>
            ";
        } else {
            $error = "Gagal memperbarui data: " . $koneksi->error;
        }
    }
}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        Edit Data Penyewa
    </div>
    <div class="panel-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Penyewa</label>
                <input type="text" name="namaPenyewa" class="form-control" value="<?php echo htmlspecialchars($data['namaPenyewa']); ?>" required>
            </div>
            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="noTelepon" class="form-control" value="<?php echo htmlspecialchars($data['noTelepon']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="?page=penyewa" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
