<?php
include('koneksi.php');
session_start(); // Pastikan session sudah dimulai

// Ambil ID Pemesanan dan Penyewa dengan validasi input
$idPemesanan = isset($_GET['idPemesanan']) ? $_GET['idPemesanan'] : '';
$idPenyewa = isset($_GET['idPenyewa']) ? $_GET['idPenyewa'] : '';

// Pastikan ID Pemesanan dan Penyewa valid
if (empty($idPemesanan) || empty($idPenyewa)) {
    throw new Exception("ID Pemesanan, ID Penyewa tidak ditemukan.");
}

// Ambil data pemesanan dari database dengan prepared statements
$sql = "SELECT p.id_pemesanan, p.uang_muka, p.status_uang_muka, p.mulai_menempati_kos, p.batas_menempati_kos, p.tenggat_uang_muka, p.status, 
               k.namaKamar, k.harga, k.idKamar, k.status AS statusKamar, p.sisa_pembayaran
        FROM pemesanan p
        JOIN kamar k ON p.idKamar = k.idKamar
        WHERE p.id_pemesanan = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, 's', $idPemesanan); // 's' untuk string
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pemesanan = mysqli_fetch_assoc($result);

if (!$pemesanan) {
    die('Pemesanan tidak ditemukan.');
}

$durasiSewa = isset($_SESSION['durasiSewa']) ? $_SESSION['durasiSewa'] : 1;

// Cek jika pemesanan telah dibatalkan
if ($pemesanan['status'] === 'Dibatalkan') {
    echo "<script>
            alert('Pemesanan telah dibatalkan. Tidak dapat melanjutkan pembayaran.');
            window.location.href='index.php';
          </script>";
    exit;
}

// Periksa tenggat waktu pembayaran untuk DP 30%
date_default_timezone_set('Asia/Jakarta');  // Atur zona waktu sesuai lokasi Anda
$tanggalSekarang = time();  // Debugging: periksa tanggal sekarang
// echo "Tanggal Sekarang: " . date('Y-m-d H:i:s', $tanggalSekarang) . "<br>"; 

$tenggatUangMuka = strtotime($pemesanan['tenggat_uang_muka']); // Tenggat waktu uang muka
$tanggalMulaiKos = strtotime($pemesanan['mulai_menempati_kos']);
$tanggalBatasKos = strtotime($pemesanan['batas_menempati_kos']);

if (in_array($pemesanan['status'], ['Menunggu Dikonfirmasi', 'Dikonfirmasi'])) {
    // Izinkan pembayaran hanya jika sudah mencapai tanggal `batas_menempati_kos`
    if ($tanggalSekarang < $tanggalBatasKos) {
        echo "<script>
                alert('Tidak dapat melakukan pembayaran. Pemesanan masih dalam status {$pemesanan['status']} dan belum mencapai batas waktu menempati kos.');
                window.location.href='index.php';
              </script>";
        exit;
    } else {
        // Tampilkan modal untuk perpanjangan kos
        echo "<script>
                $(document).ready(function() {
                    $('#modalPerpanjanganKos').modal('show');
                });
              </script>";
    }
}

// // Cek apakah status sudah diperbarui sebelumnya di session
// if (!isset($_SESSION['status_diperbarui'])) {

//     // Cek apakah sudah melewati batas waktu dan status pembayaran atau pemesanan perlu diupdate
//     if ($tanggalSekarang > $tanggalBatasKos) {
//         // Mulai transaksi
//         mysqli_begin_transaction($koneksi);

//         try {
//             // Update status pembayaran menjadi 'Belum Lunas'
//             $sqlUpdateStatusPembayaran = "UPDATE pembayaran SET StatusPembayaran = 'Belum Lunas' WHERE id_pemesanan = ?";
//             $stmtPembayaran = mysqli_prepare($koneksi, $sqlUpdateStatusPembayaran);
//             mysqli_stmt_bind_param($stmtPembayaran, 's', $idPemesanan);
//             mysqli_stmt_execute($stmtPembayaran);

//             // Update status pemesanan menjadi 'Menunggu Pembayaran'
//             $sqlUpdateStatusPemesanan = "UPDATE pemesanan SET status = 'Menunggu Pembayaran' WHERE id_pemesanan = ?";
//             $stmtPemesanan = mysqli_prepare($koneksi, $sqlUpdateStatusPemesanan);
//             mysqli_stmt_bind_param($stmtPemesanan, 's', $idPemesanan);
//             mysqli_stmt_execute($stmtPemesanan);

//             // Update tenggat_uang_muka ke periode baru
//             $tenggatBaru = strtotime("+3 days", $tanggalSekarang);
//             $tenggatBaruFormatted = date('Y-m-d', $tenggatBaru);

//             $updateTenggat = "UPDATE pemesanan SET tenggat_uang_muka = ? WHERE id_pemesanan = ?";
//             $stmtTenggat = mysqli_prepare($koneksi, $updateTenggat);
//             mysqli_stmt_bind_param($stmtTenggat, 'ss', $tenggatBaruFormatted, $idPemesanan);
//             mysqli_stmt_execute($stmtTenggat);

//             // Commit perubahan
//             mysqli_commit($koneksi);

//             // Set session flag agar tidak melakukan update lagi
//             $_SESSION['status_diperbarui'] = true;

//             // Tampilkan pesan perubahan hanya jika status diperbarui
//             echo "<script>alert('Status pembayaran, pemesanan, dan tenggat uang muka telah diperbarui.');</script>";

//         } catch (Exception $e) {
//             // Rollback jika terjadi kesalahan
//             mysqli_rollback($koneksi);
//             echo "<script>alert('Terjadi kesalahan saat memperbarui status: " . $e->getMessage() . "');</script>";
//         }
//     }

// } else {
//     // Jika sudah ada flag di session, status sudah diperbarui, tidak tampilkan pesan
//     // Anda bisa menambahkan kode lain di sini jika diperlukan
// }


// // Validasi apakah tenggat waktu uang muka tersedia dan sudah terlewati
// if (isset($pemesanan['tenggat_uang_muka'])) {
//     // Cek jika status 'Menunggu Pembayaran' dan tenggat waktu baru sudah terlewati
//     if ($pemesanan['status'] === 'Menunggu Pembayaran' && strtotime($pemesanan['tenggat_uang_muka']) < $tanggalSekarang) {
//         // Pembatalan pemesanan
//         $updateStatusPemesanan = "UPDATE pemesanan SET status = 'Dibatalkan' WHERE id_pemesanan = ? AND status = 'Menunggu Pembayaran'";
//         $stmtUpdate = mysqli_prepare($koneksi, $updateStatusPemesanan);
//         mysqli_stmt_bind_param($stmtUpdate, 's', $idPemesanan);
        
//         // Cek apakah pembatalan berhasil
//         if (mysqli_stmt_execute($stmtUpdate)) {
//             // Ubah status kamar menjadi 'Tersedia'
//             $updateStatusKamar = "UPDATE kamar SET status = 'Tersedia' WHERE idKamar = ? AND status = 'Booking'";
//             $stmtKamar = mysqli_prepare($koneksi, $updateStatusKamar);
//             mysqli_stmt_bind_param($stmtKamar, 's', $pemesanan['idKamar']);
            
//             // Cek apakah pengubahan status kamar berhasil
//             if (mysqli_stmt_execute($stmtKamar)) {
//                 echo "<script>
//                     alert('Tenggat waktu pembayaran terlewati. Pemesanan dibatalkan dan status kamar telah diubah menjadi Tersedia.');
//                     window.location.href='index.php';
//                 </script>";
//                 exit;
//             } else {
//                 echo "Error mengubah status kamar: " . mysqli_error($koneksi);
//                 exit;
//             }
//         } else {
//             echo "Error membatalkan pemesanan: " . mysqli_error($koneksi);
//             exit;
//         }
//     }
// }



// Ambil sisa pembayaran langsung dari database, bukan dari session
$sisaBiaya = $pemesanan['sisa_pembayaran']; // Ganti dengan data dari database

// Periksa sisa biaya (sisaPembayaran)
// Jika metode pembayaran dipilih
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pilih_metode'])) {
    $metodePembayaran = $_POST['pembayaran'];
    $_SESSION['metodePembayaran'] = $metodePembayaran;

    // Tampilkan instruksi pembayaran
    if ($metodePembayaran === 'QRIS') {
        echo "<h3>Silakan scan QRIS di bawah ini untuk melakukan pembayaran</h3>";
        echo "<img src='qris_code.png' alt='QRIS Code' style='width:200px;'>";
    } elseif ($metodePembayaran === 'Bank Transfer') {
        echo "<h3>Silakan transfer ke rekening berikut:</h3><p>Bank ABC<br>Nomor Rekening: 1234567890<br>Atas Nama: Kos XYZ</p>";
    }

    echo "<form action='' method='post' enctype='multipart/form-data'>
            <label for='bukti_transfer'>Unggah Bukti Transfer:</label>
            <input type='file' name='bukti_transfer' id='bukti_transfer' required>
            <button type='submit' name='unggah_bukti'>Unggah Bukti</button>
          </form>";
    exit;
}

    $pembayaran = isset($_POST['pembayaran']) ? $_POST['pembayaran'] : '';
    $tanggalPembayaran = date('Y-m-d H:i:s');
    $batasPembayaran = $pemesanan['batas_menempati_kos'];
    $sisaBiaya = isset($_POST['sisaBiaya']) ? $_POST['sisaBiaya'] : $sisaBiaya;
    $batasMenempatiKos = $pemesanan['batas_menempati_kos'];


    // Tentukan durasi sewa
    $durasiSewa = $_SESSION['durasiSewa'] ?? 1;
    // Menghitung jatuh tempo (misalnya 3 hari setelah batas_menempati_kos)
    $jatuhTempo = null;
    if (strtotime($tanggalPembayaran) > strtotime($batasPembayaran)) {
        $selisihHari = (strtotime($tanggalPembayaran) - strtotime($batasPembayaran)) / (60 * 60 * 24);
        $jatuhTempo = (int)$selisihHari; // Konversi ke integer
    }

    if ($pemesanan['status_uang_muka'] === 'Bayar Penuh') {
        $statusPembayaran = 'Lunas';  // Pembayaran penuh langsung lunas
    } else {
        // Cek jika status uang muka adalah DP 30% dan masih ada sisa pembayaran
        if ($pemesanan['status_uang_muka'] === 'DP 30%' && $pemesanan['sisa_pembayaran'] > 0) {
            $statusPembayaran = 'Belum Lunas';  // Jika masih ada sisa pembayaran, statusnya belum lunas
        } else {
            $statusPembayaran = 'Lunas';  // Jika tidak ada sisa pembayaran atau status lainnya, maka lunas
        }
    }
    

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

            $metodePembayaran = isset($_POST['pembayaran']) ? $_POST['pembayaran'] : '';

            // Validasi apakah metode pembayaran sudah dipilih
            if (empty($metodePembayaran)) {
                echo "<script>alert('Silakan pilih metode pembayaran terlebih dahulu.');</script>";
                exit;
            }

            
    
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
            
                    $metodePembayaran = isset($_POST['pembayaran']) ? $_POST['pembayaran'] : '';
            
                    // Validasi apakah metode pembayaran sudah dipilih
                    if (empty($metodePembayaran)) {
                        echo "<script>alert('Silakan pilih metode pembayaran terlebih dahulu.');</script>";
                        exit;
                    }
            
                    // Proses unggah file
                    if (move_uploaded_file($_FILES["bukti_transfer"]["tmp_name"], $targetFilePath)) {
                        mysqli_begin_transaction($koneksi); // Memulai transaksi
                        try {
                            // Update data pemesanan dengan bukti transfer
                            $sqlUpdatePemesanan = "UPDATE pemesanan SET bukti_transfer = ? WHERE id_pemesanan = ?";
                            $stmtUpdatePemesanan = mysqli_prepare($koneksi, $sqlUpdatePemesanan);
                            mysqli_stmt_bind_param($stmtUpdatePemesanan, 'ss', $fileName, $idPemesanan);
                            if (!mysqli_stmt_execute($stmtUpdatePemesanan)) {
                                throw new Exception("Gagal memperbarui data pemesanan.");
                            }
            
                            // Insert data ke tabel pembayaran
                            $sqlInsertPembayaran = "INSERT INTO pembayaran (tanggalPembayaran, batasPembayaran, durasiSewa, StatusPembayaran, idPenyewa, jatuh_tempo, id_pemesanan) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmtInsert = mysqli_prepare($koneksi, $sqlInsertPembayaran);
                            $batasPembayaran = date('Y-m-d', strtotime($tanggalPembayaran . ' +30 day'));
                            mysqli_stmt_bind_param($stmtInsert, 'sssssss', $tanggalPembayaran, $batasPembayaran, $durasiSewa, $statusPembayaran, $idPenyewa, $jatuhTempo, $idPemesanan);
                            if (!mysqli_stmt_execute($stmtInsert)) {
                                throw new Exception("Gagal menyimpan data pembayaran.");
                            }
            
                            // Ambil ID pembayaran yang baru saja diinsert
                            $idPembayaran = mysqli_insert_id($koneksi);
            
                            // Insert data ke tabel transaksi
                            $sqlGetStatusUangMuka = "SELECT status_uang_muka, uang_muka FROM pemesanan WHERE id_pemesanan = ?";
                            $stmtGetStatusUangMuka = mysqli_prepare($koneksi, $sqlGetStatusUangMuka);
                            mysqli_stmt_bind_param($stmtGetStatusUangMuka, 'i', $idPemesanan); // Gunakan tipe 'i' untuk integer
                            if (!mysqli_stmt_execute($stmtGetStatusUangMuka)) {
                                throw new Exception("Gagal mengambil data status uang muka.");
                            }
                            mysqli_stmt_bind_result($stmtGetStatusUangMuka, $statusUangMuka, $uangMuka);
                            mysqli_stmt_fetch($stmtGetStatusUangMuka);
                            mysqli_stmt_close($stmtGetStatusUangMuka);

                            // Tentukan jenis transaksi berdasarkan status uang muka
                            // $jenisTransaksi = ($statusUangMuka == 'DP 30%') ? 'DP 30%' : 'Bayar Penuh';
                            $tanggalTransaksi = date('Y-m-d H:i:s');
            
                            $sqlInsertTransaksi = "INSERT INTO transaksi (id_penyewa, id_pemesanan, id_pembayaran, jenis_transaksi, jumlah_transaksi, tanggal_transaksi, metode_bayar) 
                                                   VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmtInsertTransaksi = mysqli_prepare($koneksi, $sqlInsertTransaksi);
                            mysqli_stmt_bind_param($stmtInsertTransaksi, 'iisssss', $idPenyewa, $idPemesanan, $idPembayaran, $statusUangMuka, $uangMuka, $tanggalTransaksi, $metodePembayaran);
                            if (!mysqli_stmt_execute($stmtInsertTransaksi)) {
                                throw new Exception("Gagal menyimpan data transaksi.");
                            }
            
                            // Update status pemesanan menjadi 'Menunggu Dikonfirmasi'
                            $sqlUpdatePemesananStatus = "UPDATE pemesanan SET status = 'Menunggu Dikonfirmasi' WHERE id_pemesanan = ?";
                            $stmtUpdatePemesananStatus = mysqli_prepare($koneksi, $sqlUpdatePemesananStatus);
                            mysqli_stmt_bind_param($stmtUpdatePemesananStatus, 's', $idPemesanan);
                            if (!mysqli_stmt_execute($stmtUpdatePemesananStatus)) {
                                throw new Exception("Gagal memperbarui status pemesanan.");
                            }
            
                            // Commit transaksi jika semua sukses
                            mysqli_commit($koneksi);
                            echo "<script>
                                alert('Pembayaran berhasil diproses. Status pemesanan kini Menunggu Konfirmasi.');
                                window.location.href = 'status_pembayaran.php?idPemesanan={$idPemesanan}&idPenyewa={$idPenyewa}';
                            </script>";
                        } catch (Exception $e) {
                            // Rollback jika terjadi kesalahan
                            mysqli_rollback($koneksi);
                            echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "');</script>";
                        }
                    } else {
                        echo "<script>alert('Gagal mengunggah file.');</script>";
                    }
                }
            }
        }
    }            
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran</title>
    <!-- Tambahkan Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .payment-container {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #4CAF50;
            font-size: 1.8rem;
        }
        .btn-custom {
            background-color: #4CAF50;
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="payment-container">
    <button onclick="goBack()" class="btn btn-primary btn-rounded">Kembali</button>
        <h2>Konfirmasi Pembayaran</h2>
        <div class="payment-summary mb-4">
            <p><strong>Kamar:</strong> <?= htmlspecialchars($pemesanan['namaKamar']) ?></p>
            <p><strong>Harga:</strong> Rp<?= number_format($pemesanan['harga'], 0, ',', '.') ?></p>
            <p><strong>Uang Muka:</strong> Rp<?= number_format($pemesanan['uang_muka'], 0, ',', '.') ?></p>
            <p><strong>Durasi Sewa:</strong> <?= $durasiSewa ?> bulan</p>
            <p><strong>Status Pembayaran:</strong> <?= $pemesanan['statusPembayaran'] ?? 'Belum Lunas' ?></p>
            <?php if ($pemesanan['sisa_pembayaran'] > 0): ?>
                <p><strong>Sisa Pembayaran:</strong> Rp<?= number_format($pemesanan['sisa_pembayaran'], 0, ',', '.') ?></p>
            <?php endif; ?>
        </div>
        <button class="btn btn-custom w-100" id="openModalBtn" data-bs-toggle="modal" data-bs-target="#paymentModal">
            Pilih Metode Pembayaran
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Pilih Metode Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="pembayaran.php?idPemesanan=<?= $idPemesanan ?>&idPenyewa=<?= $idPenyewa ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="pembayaran" class="form-label">Metode Pembayaran</label>
                            <select name="pembayaran" id="pembayaran" class="form-select" required>
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div id="paymentInstructions" class="mb-3">
                            <p><strong>Silakan pilih metode pembayaran untuk melihat instruksi.</strong></p>
                        </div>
                        <div id="fileUploadSection" class="mb-3" style="display: none;">
                            <label for="bukti_transfer" class="form-label">Unggah Bukti Transfer</label>
                            <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control" required>
                        </div>
                        <button type="submit" name="unggah_bukti" class="btn btn-custom w-100">Unggah Bukti</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('pembayaran').addEventListener('change', function () {
            const method = this.value;
            const instructionsDiv = document.getElementById('paymentInstructions');
            const fileUploadSection = document.getElementById('fileUploadSection');

            if (method === 'QRIS') {
                instructionsDiv.innerHTML = `
                    <h5>Scan QRIS berikut untuk pembayaran:</h5>
                    <img src="img/qris.jpeg" alt="QRIS Code" class="img-fluid">
                `;
                fileUploadSection.style.display = 'block';
            } else if (method === 'Bank Transfer') {
                instructionsDiv.innerHTML = `
                    <h5>Transfer ke rekening berikut:</h5>
                    <p>Bank ABC<br>Nomor Rekening: 1234567890<br>Atas Nama: Kos XYZ</p>
                `;
                fileUploadSection.style.display = 'block';
            } else {
                instructionsDiv.innerHTML = '<p><strong>Silakan pilih metode pembayaran untuk melihat instruksi.</strong></p>';
                fileUploadSection.style.display = 'none';
            }
        });
        function goBack() {
            window.history.back();
        }   
    </script>
</body>
</html>
