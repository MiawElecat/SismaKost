<?php
require('fpdf/fpdf.php');
include 'koneksi.php';

// Ambil data pemesanan berdasarkan id_pemesanan yang dikirimkan via GET
$id_pemesanan = $_GET['id_pemesanan'];
$id_kamar = isset($_GET['id_kamar']) ? $_GET['id_kamar'] : null; // Jika tidak ada, set ke null
$id_penyewa = isset($_GET['id_penyewa']) ? $_GET['id_penyewa'] : null;

// Query untuk mengambil data pemesanan
$queryPemesanan = "SELECT * FROM pemesanan WHERE id_pemesanan = ?";
$stmt = $koneksi->prepare($queryPemesanan);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Query untuk mengambil data penyewa
$queryPenyewa = "SELECT * FROM penyewa WHERE idPenyewa = ?";
$stmt = $koneksi->prepare($queryPenyewa);
$stmt->bind_param("i", $order['id_penyewa']);
$stmt->execute();
$penyewa = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Buat file PDF (Kuitansi)
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Judul
$pdf->Cell(190, 10, 'Kuitansi Pembayaran Pemesanan', 0, 1, 'C');
$pdf->Ln(5);

// Nama Kos dan Tanggal/Jam Cetak
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 10, 'ElisaKost', 0, 1, 'C'); // Nama Kos
$pdf->Cell(190, 10, 'Tanggal : ' . date('d-m-Y H:i:s'), 0, 1, 'C'); // Jam cetak
$pdf->Ln(10);

// Informasi Pemesanan
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Nama Penyewa:', 0, 0);
$pdf->Cell(0, 10, $penyewa['namaPenyewa'], 0, 1);
$pdf->Cell(40, 10, 'ID Pemesanan:', 0, 0);
$pdf->Cell(0, 10, $order['id_pemesanan'], 0, 1);
$pdf->Cell(40, 10, 'Tanggal Pemesanan:', 0, 0);
$pdf->Cell(0, 10, $order['pemesanan_kamar'], 0, 1);
$pdf->Cell(40, 10, 'Jumlah Uang Muka:', 0, 0);
$pdf->Cell(0, 10, "Rp " . number_format($order['uang_muka'], 0, ',', '.'), 0, 1);
$pdf->Cell(40, 10, 'Status Pembayaran:', 0, 0);
$pdf->Cell(0, 10, $order['status_uang_muka'], 0, 1);

// Tambahkan sedikit jarak dan footer
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Terima kasih atas pesanan Anda!', 0, 1, 'C');

// Cek dan buat folder untuk menyimpan file PDF jika belum ada
$folderPath = 'uploads/kuintasi/';
if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true); // Membuat folder dan memberikan izin akses
}

// Tentukan nama file PDF
$pdfFilePath = $folderPath . 'kuitansi_' . $order['id_pemesanan'] . '.pdf';

// Simpan PDF ke dalam file
$pdf->Output('F', $pdfFilePath);

// Redirect ke konfirmasi.php dengan path PDF
if (isset($id_pemesanan, $id_kamar, $id_penyewa, $pdfFilePath)) {
    header("Location: konfirmasi.php?id_pemesanan=$id_pemesanan&id_kamar=$id_kamar&id_penyewa=$id_penyewa&pdf_path=" . urlencode($pdfFilePath));
} else {
    echo "Terjadi Kesalahan.";
    exit;
}
?>
