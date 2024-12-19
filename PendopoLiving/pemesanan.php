<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['idPenyewa'])) {
    echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit;
}

include('koneksi.php');

// Ambil parameter dari URL
$idKamar = isset($_GET['idKamar']) ? $_GET['idKamar'] : '';
$fasilitas = isset($_GET['fasilitas']) ? $_GET['fasilitas'] : '';
$blok = isset($_GET['blok']) ? $_GET['blok'] : '';
$bringElectronics = isset($_POST['bringElectronics']) ? $_POST['bringElectronics'] : 0;
$paymentOption = isset($_POST['paymentOption']) ? $_POST['paymentOption'] : 'dp';
$durasiSewa = isset($_POST['durasiSewa']) ? $_POST['durasiSewa'] : 1; // Default durasi sewa 1 bulan

$returnUrl = isset($_GET['returnUrl']) ? $_GET['returnUrl'] : 'daftar_kamar.php';
echo "<a href='" . $returnUrl . "' id='backButton'>Kembali</a>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['durasiSewa'] = $durasiSewa;
    
    if ($bringElectronics < 0) {
        die("Jumlah barang elektronik tidak valid.");
    }

    $startDate = $_POST['startDate'];
    if (strtotime($startDate) < strtotime(date('Y-m-d'))) {
        die("Tanggal mulai menempati tidak valid.");
    }
}

$idPenyewa = isset($_SESSION['idPenyewa']) ? $_SESSION['idPenyewa'] : null;

// Cek apakah penyewa sudah memiliki pemesanan aktif
$sqlCekPemesanan = "SELECT * FROM pemesanan WHERE id_penyewa = '$idPenyewa' AND status IN ('Menunggu Pembayaran', 'Menunggu Dikonfirmasi', 'Dikonfirmasi')";
$resultCekPemesanan = mysqli_query($koneksi, $sqlCekPemesanan);

if (mysqli_num_rows($resultCekPemesanan) > 0) {
    echo "<script>alert('Anda sudah memiliki pemesanan aktif. Anda tidak dapat melakukan pemesanan lagi.');</script>";
    echo "<script>window.location.href='index.php';</script>";
    exit;
}


// Query untuk mendapatkan informasi kamar
$sql = "SELECT k.idKamar, k.namaKamar, k.harga, b.namaBlok, f.namaFasilitas, f.biayaTambahan, k.status
        FROM kamar k
        JOIN kamar_fasilitas kf ON k.idKamar = kf.idKamar
        JOIN fasilitas f ON kf.idFasilitas = f.idFasilitas
        JOIN blok b ON k.idBlok = b.idBlok
        WHERE k.idKamar = '$idKamar'";
$result = mysqli_query($koneksi, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die('Kamar tidak ditemukan.');
}

$hargaKamar = $row['harga'];
$namaKamar = $row['namaKamar'];
$namaBlok = $row['namaBlok'];
$namaFasilitas = $row['namaFasilitas'];
$biayaTambahan = $row['biayaTambahan'];
$statusKamar = $row['status'];

// Cek apakah kamar sudah dalam status "Booking"
if ($statusKamar == 'Booking') {
    echo "<script>alert('Kamar ini sudah dipesan oleh orang lain. Silakan pilih kamar lain yang masih tersedia.'); window.location.href='index.php';</script>";
    exit;
} elseif ($statusKamar == 'Kosong') {
    echo "<script>alert('Kamar ini sedang tidak tersedia untuk pemesanan. Mohon pilih kamar lain.'); window.location.href='index.php';</script>";
    exit;
}


// Biaya tambahan untuk listrik
$biayaListrik = 15000 * $bringElectronics;

// Total biaya
$totalBiaya = $hargaKamar * $durasiSewa + $biayaTambahan * $durasiSewa + $biayaListrik;
$dpBiaya = $totalBiaya * 0.30; // 30% dari total biaya
$sisaBiaya = ($paymentOption == 'dp') ? $totalBiaya - $dpBiaya : 0;

$_SESSION['totalBiaya'] = $totalBiaya;
$_SESSION['sisaBiaya'] = $sisaBiaya; // Menyimpan sisa biaya dalam sesi jika perlu untuk pembayaran berikutnya

// Sesuaikan dengan metode pembayaran
if ($paymentOption == 'full') {
    $finalBiaya = $totalBiaya; // Bayar Penuh
} else {
    $finalBiaya = $dpBiaya; // DP 30%
}

// Proses form submission untuk pemesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan kamar tersedia sebelum melakukan pemesanan
    if ($statusKamar != 'Tersedia') {
        echo "<script>alert('Kamar sudah tidak tersedia.'); window.location.href='index.php';</script>";
        exit;
    }
    
    // Menentukan status uang muka dan status pemesanan awal
    $statusUangMuka = ($paymentOption === 'full') ? 'Bayar Penuh' : 'DP 30%';
    $statusPemesanan = 'Menunggu Pembayaran';

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $tenggatWaktu = date('Y-m-d H:i:s', strtotime('+2 days'));

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);
    
    try {
        // Ubah status kamar menjadi "Booking"
        $updateStatusKamar = "UPDATE kamar SET status = 'Booking' WHERE idKamar = '$idKamar'";
        mysqli_query($koneksi, $updateStatusKamar);

        // Query untuk pemesanan
        $sqlPemesanan = "INSERT INTO pemesanan (pemesanan_kamar, uang_muka, status_uang_muka, tenggat_uang_muka, mulai_menempati_kos, batas_menempati_kos, status, sisa_pembayaran, id_penyewa, idKamar)
                VALUES (NOW(), '$finalBiaya', '$statusUangMuka', '$tenggatWaktu', '$startDate', '$endDate', '$statusPemesanan', '$sisaBiaya', '$idPenyewa', '$idKamar')";

        if (mysqli_query($koneksi, $sqlPemesanan)) {
            $idPemesanan = mysqli_insert_id($koneksi);
            mysqli_commit($koneksi);
            echo "<script>alert('Pemesanan berhasil! Silakan cek pemesanan Anda.'); window.location.href='pesananku.php';</script>";
        } else {
            throw new Exception("Error in pemesanan query: " . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Kamar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styling untuk form pemesanan */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        .order-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .order-summary {
            margin: 20px 0;
            font-size: 18px;
        }

        .order-summary p {
            margin: 10px 0;
        }

        .order-summary .price {
            color: #FF5722;
            font-weight: bold;
        }

        .order-form input[type="number"],
        .order-form input[type="date"],
        .order-form select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        .order-form input[type="number"]:focus,
        .order-form input[type="date"]:focus,
        .order-form select:focus {
            border-color: #4CAF50;
            background-color: #fff;
            outline: none;
        }

        .order-form label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        .order-form button {
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .order-form button:hover {
            background-color: #45a049;
        }

        #backButton {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="order-container">
        <h2>Pemesanan Kamar</h2>
        <form method="post" class="order-form" action="">
            <div class="order-summary">
                <p><strong>Kamar:</strong> <?= $namaKamar ?> di Blok <?= $namaBlok ?></p>
                <p><strong>Fasilitas:</strong> <?= $namaFasilitas ?></p>
                <p><strong>Harga Kamar (per bulan):</strong> <span class="price">Rp. <?= number_format($hargaKamar, 0, ',', '.') ?></span></p>
                <p><strong>Biaya Fasilitas Tambahan :</strong> <span class="price">Rp. <?= number_format($biayaTambahan, 0, ',', '.') ?></span></p>
                <p><strong>Biaya Listrik:</strong> <span id="biayaListrik">Rp. 0</span></p>
                <p><strong>Total Pembayaran:</strong> <span id="totalBiaya">Rp. 0</span></p>
                <p><strong>Sisa Pembayaran:</strong> <span id="sisaBiaya">Rp. 0</span></p>
            </div>
            
            <label for="startDate">Tanggal Mulai Menempati:</label>
            <input type="date" id="startDate" name="startDate" onchange="updateBiayaListrik()" required>

            <label for="endDate">Tanggal Selesai Menempati:</label>
            <input type="date" id="endDate" name="endDate" readonly>

            <label for="durasiSewa">Durasi Sewa (bulan):</label>
            <input type="number" id="durasiSewa" name="durasiSewa" min="1" value="<?= $durasiSewa ?>" oninput="updateBiayaListrik()" required>

            <label for="bringElectronics">Jumlah Barang Elektronik:</label>
            <input type="number" id="bringElectronics" name="bringElectronics" min="0" value="<?= $bringElectronics ?>" oninput="updateBiayaListrik()" required>

            <label for="paymentOption">Opsi Pembayaran:</label>
            <select id="paymentOption" name="paymentOption" onchange="updateBiayaListrik()" required>
                <option value="dp">DP 30%</option>
                <option value="full">Bayar Penuh</option>
            </select>

            <button type="submit">Pesan Sekarang</button>
        </form>
    </div>

    <script>
        function updateBiayaListrik() {
            var barangElektronik = document.getElementById("bringElectronics").value;
            var biayaListrik = 15000 * barangElektronik;
            var hargaKamar = <?= $hargaKamar ?>;
            var biayaTambahan = <?= $biayaTambahan ?>;
            var durasiSewa = document.getElementById("durasiSewa").value;
            var totalBiaya = (hargaKamar + biayaTambahan) * durasiSewa + biayaListrik;

            var paymentOption = document.getElementById("paymentOption").value;
            var finalBiaya, sisaBiaya;

            if (paymentOption == "full") {
                finalBiaya = totalBiaya; // Pembayaran penuh
                sisaBiaya = 0;
            } else {
                finalBiaya = totalBiaya * 0.30; // DP 30%
                sisaBiaya = totalBiaya - finalBiaya;
            }

            document.getElementById("biayaListrik").innerText = "Rp. " + biayaListrik.toLocaleString();
            document.getElementById("totalBiaya").innerText = "Rp. " + finalBiaya.toLocaleString();
            document.getElementById("sisaBiaya").innerText = "Rp. " + sisaBiaya.toLocaleString();

            // Update tanggal akhir berdasarkan durasi sewa
            var startDate = document.getElementById("startDate").value;
            if (startDate) {
                var start = new Date(startDate);
                start.setMonth(start.getMonth() + parseInt(durasiSewa)); // Menambahkan bulan sesuai durasi sewa
                var endDate = start.toISOString().split('T')[0]; // Format YYYY-MM-DD
                document.getElementById("endDate").value = endDate;
            }
        }
    </script>
</body>
</html>
