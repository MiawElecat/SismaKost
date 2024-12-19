<?php
session_start();
include('koneksi.php');

// Pastikan penyewa sudah login
if (!isset($_SESSION['idPenyewa'])) {
    echo "<script>alert('Harap login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit;
}

$idPenyewa = $_SESSION['idPenyewa'];
$tanggalSekarang = time();

if (isset($_GET['idPemesanan'])) {
    $idPemesanan = $_GET['idPemesanan'];
    
    if (isset($_GET['idPembayaran'])) {
        $idPembayaran = $_GET['idPembayaran'];
    }

    if (isset($_GET['idPenyewa'])) {
        $idPenyewa = $_GET['idPenyewa'];
    }

    // Ambil data pemesanan dan pembayaran
    $sql = "SELECT p.id_pemesanan, p.uang_muka, p.bukti_transfer, p.batas_menempati_kos,  
            pb.idPembayaran, pb.tanggalPembayaran, pb.batasPembayaran, pb.durasiSewa, pb.statusPembayaran, pb.jatuh_tempo,
            k.namaKamar
        FROM pemesanan p
        LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
        JOIN penyewa ps ON p.id_penyewa = ps.idPenyewa  
        JOIN kamar k ON p.idKamar = k.idKamar 
        WHERE p.id_pemesanan = '$idPemesanan' AND p.id_penyewa = '$idPenyewa'";

    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data pemesanan tidak ditemukan.'); window.location.href='index.php';</script>";
    }
}

// Gunakan $data untuk mengambil nilai
$BatasMenempatiKos = $data['batas_menempati_kos'];
$uangMukaPerBulan = $data['uang_muka'] / $data['durasiSewa'];
$durasiOld = $data['durasiSewa'];
$uangMukaOld = $data['uang_muka'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unggah_bukti'])) {
    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0) {
        // Tentukan direktori tujuan unggah
        $targetDir = "uploads/";
        $fileName = basename($_FILES['bukti_transfer']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        $validExtensions = array("jpg", "jpeg", "png", "gif", "pdf");
        if (!in_array($fileType, $validExtensions)) {
            echo "<script>alert('Format file tidak valid. Hanya file JPG, PNG, JPEG, GIF, dan PDF yang diperbolehkan.');</script>";
            exit;
        }

        $metodePembayaran = isset($_POST['metodePembayaran']) ? $_POST['metodePembayaran'] : '';

        // Validasi apakah metode pembayaran sudah dipilih
        if (empty($metodePembayaran)) {
            echo "<script>alert('Silakan pilih metode pembayaran terlebih dahulu.');</script>";
            exit;
        }

        // Pindahkan file yang diupload ke direktori tujuan
        if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $targetFilePath)) {
            $durasiSewaBaru = isset($_POST['durasiBaru']) ? intval($_POST['durasiBaru']) : $durasiOld;
            $uangMukaBaru = $uangMukaPerBulan * $durasiSewaBaru;
            $tanggalPembayaran = date('Y-m-d H:i:s', $tanggalSekarang);
            $batasMenempatiTimestamp = strtotime($BatasMenempatiKos);

            if ($durasiSewaBaru > $durasiOld) {
                // Jika durasi bertambah, hitung batas menempati kos baru
                $newBatasMenempatiKos = date('Y-m-d', strtotime("+$durasiSewaBaru months", $batasMenempatiTimestamp));
            } else {
                // Jika durasi berkurang, cukup kurangi waktu
                $newBatasMenempatiKos = date('Y-m-d', strtotime("+$durasiSewaBaru months", $batasMenempatiTimestamp));
            }

            // Mulai transaksi
            mysqli_begin_transaction($koneksi);

            // Update tabel pemesanan dengan bukti transfer baru
            $updatePemesananSql = "UPDATE pemesanan SET 
                                    uang_muka = '$uangMukaBaru', 
                                    bukti_transfer = '$fileName',
                                    batas_menempati_kos = '$newBatasMenempatiKos', 
                                    status = 'Perpanjangan'
                                    WHERE id_pemesanan = '$idPemesanan'";

            // Update tabel pembayaran dengan data yang sudah diperbarui
            $updatePembayaranSql = "UPDATE pembayaran SET 
                                    tanggalPembayaran = '$tanggalPembayaran', 
                                    durasiSewa = '$durasiSewaBaru',
                                    batasPembayaran = '$newBatasMenempatiKos', 
                                    statusPembayaran = 'Lunas'
                                    WHERE idPembayaran = '$idPembayaran'";

            // Eksekusi query
            $resultPemesanan = mysqli_query($koneksi, $updatePemesananSql);
            $resultPembayaran = mysqli_query($koneksi, $updatePembayaranSql);

           // Perbaikan: Insert data ke tabel transaksi menggunakan prepared statement
                $insertTransaksiSql = "INSERT INTO transaksi 
                (id_pemesanan, id_penyewa, id_pembayaran, jenis_transaksi, jumlah_transaksi, tanggal_transaksi, metode_bayar) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

                // Menggunakan prepared statement untuk menghindari SQL injection
                $stmtTransaksi = $koneksi->prepare($insertTransaksiSql);
                $jenisTransaksi = 'Perpanjangan';
                $stmtTransaksi->bind_param('iisssss', $idPemesanan, $idPenyewa, $idPembayaran, $jenisTransaksi, $uangMukaBaru, $tanggalPembayaran, $metodePembayaran);

                // Eksekusi query insert transaksi
                $resultTransaksi = $stmtTransaksi->execute();

            if ($resultPemesanan && $resultPembayaran && $resultTransaksi) {
                // Jika semua query berhasil, commit transaksi
                mysqli_commit($koneksi);
                echo "<script>alert('Perpanjangan Sewa berhasil!'); window.location.href='pesananku.php';</script>";
            } else {
                // Jika ada kesalahan, rollback transaksi
                mysqli_rollback($koneksi);
                echo "<script>alert('Gagal memperpanjang sewa. Silakan coba lagi.');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah bukti transfer. Silakan coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('File bukti transfer tidak ditemukan atau terjadi kesalahan saat mengunggah.');</script>";
    }
    $stmtTransaksi->close();
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpanjang Sewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .highlight { color: #f00; font-weight: bold; }
        .info-box { padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Perpanjangan Sewa Kamar</h2>
    <div class="info-box mb-4">
        <h5>Informasi Pemesanan</h5>
        <p>Kamar: <strong><?= htmlspecialchars($data['namaKamar'] ?? '-') ?></strong></p>
        <p>Batas Sewa Saat Ini: <span class="highlight"><?= htmlspecialchars(date('d-m-Y', strtotime($BatasMenempatiKos))) ?></span></p>
        <p>Durasi Sebelumnya: <strong><?= htmlspecialchars($durasiOld) ?> bulan</strong></p>
        <p>Uang Muka Sebelumnya: <strong>Rp <?= number_format($uangMukaOld, 0, ',', '.') ?></strong></p>
        <p>Status Pembayaran: <strong><?= htmlspecialchars($data['statusPembayaran'] ?? 'Belum Lunas') ?></strong></p>
    </div>
    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#paymentModal">Lanjutkan Perpanjangan</button>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Formulir Perpanjangan Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <h6 class="mb-3">Pilih Metode Pembayaran</h6>
                        <div class="mb-3">
                            <select class="form-select" id="metodePembayaran" name="metodePembayaran" required>
                                <option value="" disabled selected>Pilih metode pembayaran</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div id="paymentInstructions" class="mt-3 mb-3"></div>
                        <hr>
                        <h6 class="mb-3">Detail Perpanjangan</h6>
                        <div class="mb-3">
                            <label for="durasiBaru" class="form-label">Durasi Perpanjangan (Bulan)</label>
                            <input type="number" id="durasiBaru" name="durasiBaru" class="form-control" value="<?= htmlspecialchars($durasiOld) ?>" min="1" required>
                        </div>
                        <p>Estimasi Total Uang Muka: <span id="uangMukaBaru">Rp <?= number_format($uangMukaOld, 0, ',', '.') ?></span></p>
                        <div id="fileUploadSection" class="mb-3" style="display: none;">
                            <label for="bukti_transfer" class="form-label">Unggah Bukti Pembayaran</label>
                            <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="unggah_bukti" class="btn btn-primary">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function hitungUangMukaBaru() {
        const uangMukaPerBulan = <?= $uangMukaPerBulan ?>;
        const durasiBaru = parseInt(document.getElementById('durasiBaru').value);

        if (!isNaN(durasiBaru)) {
            const uangMukaBaru = uangMukaPerBulan * durasiBaru;
            document.getElementById('uangMukaBaru').innerText = 'Rp ' + uangMukaBaru.toLocaleString();
        }
    }

    document.getElementById("durasiBaru").addEventListener("input", hitungUangMukaBaru);

    const metodeSelect = document.getElementById("metodePembayaran");
    const instructionsDiv = document.getElementById("paymentInstructions");
    const fileUploadSection = document.getElementById('fileUploadSection');

    metodeSelect.addEventListener("change", function () {
        const method = metodeSelect.value;

        if (method === "QRIS") {
            instructionsDiv.innerHTML = `<h6>Scan QRIS berikut:</h6><img src="img/qris.jpeg" alt="QRIS Code" class="img-fluid" style="max-width: 200px;">`;
            fileUploadSection.style.display = 'block';
        } else if (method === "Bank Transfer") {
            instructionsDiv.innerHTML = `<h6>Transfer ke Rekening:</h6><p>Bank ABC<br>1234567890<br>Atas Nama: Kos Elisa</p>`;
            fileUploadSection.style.display = 'block';
        } else {
            instructionsDiv.innerHTML = '<p><strong>Pilih metode pembayaran.</strong></p>';
            fileUploadSection.style.display = 'none';
        }
    });

    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'true'): ?>
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
    <?php endif; ?>
</script>
</body>
</html>
