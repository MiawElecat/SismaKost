<?php
include 'koneksi.php';

// Ambil data dari URL atau POST
$id_pemesanan = $_GET['id_pemesanan']; // ID pemesanan yang dikonfirmasi
$id_kamar = $_GET['id_kamar']; // ID kamar yang terkait
$id_penyewa = $_GET['id_penyewa']; // ID penyewa yang melakukan pemesanan
$pdf_path = $_GET['pdf_path']; // Path PDF kuitansi yang telah dibuat

// Ambil status pemesanan sebelumnya untuk referensi
$queryPemesanan = "SELECT status FROM pemesanan WHERE id_pemesanan = ?";
$stmt = $koneksi->prepare($queryPemesanan);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$stmt->bind_result($statusPemesanan);
$stmt->fetch();
$stmt->close();

// Update status pemesanan menjadi "Dikonfirmasi"
$queryUpdatePemesanan = "UPDATE pemesanan SET status = 'Dikonfirmasi' WHERE id_pemesanan = ?";
$stmt = $koneksi->prepare($queryUpdatePemesanan);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$stmt->close();

// Update status kamar menjadi "Kosong"
$queryUpdateKamar = "UPDATE kamar SET status = 'Kosong' WHERE idKamar = ?";
$stmt = $koneksi->prepare($queryUpdateKamar);
$stmt->bind_param("i", $id_kamar);
$stmt->execute();
$stmt->close();

// Ambil nama penyewa dan nomor telepon
$queryPenyewa = "SELECT namaPenyewa, noTelepon FROM penyewa WHERE idPenyewa = ?";
$stmt = $koneksi->prepare($queryPenyewa);
$stmt->bind_param("i", $id_penyewa);
$stmt->execute();
$stmt->bind_result($namaPenyewa, $noTelepon);
$stmt->fetch();
$stmt->close();

if (!$noTelepon || !$namaPenyewa) {
    echo "Nomor telepon atau nama penyewa tidak ditemukan untuk ID penyewa $id_penyewa.";
    exit;
}

// Ubah nomor telepon yang dimulai dengan '08' menjadi '62' (format internasional)
if (substr($noTelepon, 0, 2) == '08') {
    $noTelepon = '62' . substr($noTelepon, 1);
}

// Tentukan pesan berdasarkan status pemesanan sebelumnya
if ($statusPemesanan == 'Menunggu Dikonfirmasi') {
    $kuitansi = "Halo $namaPenyewa, terima kasih banyak atas pemesanan Anda! 🎉 Kami senang memberitahukan bahwa pemesanan Anda telah dikonfirmasi. Kamar Anda sekarang telah berhasil dipesan. Kami sangat menantikan kedatangan Anda dan siap membantu Anda untuk mengatur waktu bertemu dan mengambil kunci. Jangan ragu untuk menghubungi kami jika ada yang perlu ditanyakan. 😊";
} else if ($statusPemesanan == 'Perpanjangan') {
    $kuitansi = "Halo $namaPenyewa, terima kasih atas kepercayaan Anda untuk memperpanjang masa sewa! 😊 Kami senang dapat melanjutkan kerjasama dengan Anda. Kamar Anda telah berhasil diperpanjang, dan kami siap membantu Anda dengan segala kebutuhan. Silakan hubungi kami jika ada yang perlu dibicarakan lebih lanjut. Kami selalu siap melayani Anda dengan senang hati! 🌟";
} else {
    $kuitansi = "Halo $namaPenyewa, terima kasih banyak atas pemesanan Anda! 🙏 Kami sangat menghargai kepercayaan Anda. Tim kami akan segera menghubungi Anda untuk memberikan informasi lebih lanjut. Kami berharap dapat memberikan pengalaman terbaik untuk Anda. Jangan ragu untuk menghubungi kami kapan saja jika ada pertanyaan. Semoga hari Anda menyenankan! 🌸";
}


// Mengarahkan ke WhatsApp dengan nomor telepon dan pesan
$whatsappURL = "https://api.whatsapp.com/send/?phone=$noTelepon&text=" . urlencode($kuitansi);

// Redirect ke WhatsApp
header("Location: $whatsappURL");
exit;
?>
