<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPesan = $_POST['idPesan'];
    $idPenyewa = $_POST['idPenyewa'];

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Cari kamar terkait dari tabel pemesanan
        $queryPemesanan = "SELECT idKamar FROM pemesanan WHERE id_penyewa = ? AND status IN ('Menunggu Pembayaran','Menunggu Dikonfirmasi', 'Dikonfirmasi', 'Perpanjangan')";
        $stmt = $koneksi->prepare($queryPemesanan);
        $stmt->bind_param("i", $idPenyewa);
        $stmt->execute();
        $resultPemesanan = $stmt->get_result();

        if ($resultPemesanan->num_rows > 0) {
            $pemesanan = $resultPemesanan->fetch_assoc();
            $idKamar = $pemesanan['idKamar'];

            // Update status pemesanan
            $queryUpdatePemesanan = "UPDATE pemesanan SET status = 'Keluar' WHERE id_penyewa = ?";
            $stmtUpdatePemesanan = $koneksi->prepare($queryUpdatePemesanan);
            $stmtUpdatePemesanan->bind_param("i", $idPenyewa);
            $stmtUpdatePemesanan->execute();

            // Update status kamar
            $queryUpdateKamar = "UPDATE kamar SET status = 'Tersedia' WHERE idKamar = ?";
            $stmtUpdateKamar = $koneksi->prepare($queryUpdateKamar);
            $stmtUpdateKamar->bind_param("i", $idKamar);
            $stmtUpdateKamar->execute();

            // Tandai pesan sebagai telah diproses
            $queryUpdatePesan = "UPDATE pesan SET `read` = 'Sudah Dibaca' WHERE idPesan = ?";
            $stmtUpdatePesan = $koneksi->prepare($queryUpdatePesan);
            $stmtUpdatePesan->bind_param("i", $idPesan);
            $stmtUpdatePesan->execute();

            // Ambil nama penyewa dan nomor telepon penyewa
            $queryPenyewa = "SELECT namaPenyewa, noTelepon FROM penyewa WHERE idPenyewa = ?";
            $stmtPenyewa = $koneksi->prepare($queryPenyewa);
            $stmtPenyewa->bind_param("i", $idPenyewa);
            $stmtPenyewa->execute();
            $resultPenyewa = $stmtPenyewa->get_result();

            if ($resultPenyewa->num_rows > 0) {
                $penyewa = $resultPenyewa->fetch_assoc();
                $namaPenyewa = $penyewa['namaPenyewa'];
                $noTelepon = $penyewa['noTelepon'];

                // Ubah nomor telepon ke format Indonesia (contoh: 08123456789 menjadi +628123456789)
                if (substr($noTelepon, 0, 1) == '0') {
                    $noTelepon = '62' . substr($noTelepon, 1);
                }

                // Pesan WhatsApp untuk konfirmasi pengajuan keluar dan pengembalian kunci
                $message = "Hallo, $namaPenyewa,\n\n"
                         . "Pengajuan keluar Anda telah diproses dan kamar telah tersedia.\n\n"
                         . "Harap segera mengembalikan kunci kamar dan memastikan kamar dalam kondisi baik.\n\n"
                         . "Jika ada pertanyaan atau kendala terkait pengembalian kunci, silakan hubungi kami untuk bantuan lebih lanjut.\n\n"
                         . "Terima kasih telah menggunakan layanan kami.";

                // URL WhatsApp untuk mengirim pesan
                $whatsappUrl = "https://api.whatsapp.com/send/?phone=$noTelepon&text=" . urlencode($message);

                // Commit transaksi
                $koneksi->commit();

                // Redirect ke WhatsApp
                echo "<script>
                        alert('Proses berhasil. Mengarahkan ke WhatsApp untuk konfirmasi.');
                        window.location.href = '$whatsappUrl';
                      </script>";
            } else {
                throw new Exception('Data penyewa tidak ditemukan.');
            }
        } else {
            throw new Exception('Tidak ditemukan pemesanan aktif untuk penyewa ini.');
        }
    } catch (Exception $e) {
        // Jika terjadi error, rollback transaksi
        $koneksi->rollback();

        // Menampilkan error ke pengguna
        echo "<script>
                alert('Terjadi kesalahan: " . $e->getMessage() . "');
                window.location.href = 'dashboard.php';
              </script>";
    }
}
?>
