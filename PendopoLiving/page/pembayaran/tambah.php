<?php
// Memeriksa apakah formulir telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['simpan'])) {
        $tanggalPembayaran = $_POST['tanggalPembayaran'];
        $batasPembayaran = $_POST['batasPembayaran'];
        $durasiSewa = $_POST['durasiSewa'];
        $statusPembayaran = $_POST['statusPembayaran'];
        $idPenyewa = $_POST['idPenyewa'];
        $jatuhTempo = $_POST['jatuhTempo'];

        // Menyimpan data ke tabel 'pembayaran'
        $sql = $koneksi->query("INSERT INTO pembayaran (tanggalPembayaran, batasPembayaran, durasiSewa, StatusPembayaran, idPenyewa, jatuh_tempo) VALUES (
            '$tanggalPembayaran',
            '$batasPembayaran',
            '$durasiSewa',
            '$statusPembayaran',
            '$idPenyewa',
            '$jatuhTempo'
        )");

        if ($sql) {
            echo '
            <script type="text/javascript">
                alert("Data berhasil ditambahkan");
                window.location.href="?page=pembayaran";
            </script>';
        } else {
            echo '<script>alert("Data gagal ditambahkan");</script>';
        }
    }
}
?>

<!-- Formulir HTML untuk menambahkan data pembayaran baru -->
<div class="panel panel-default">
    <div class="panel-heading">
        Tambah Data Pembayaran
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <form method="POST">
                    <div class="form-group">
                        <label>Tanggal Pembayaran</label>
                        <input type="date" class="form-control" name="tanggalPembayaran" required/>
                    </div>

                    <div class="form-group">
                        <label>Batas Pembayaran</label>
                        <input type="date" class="form-control" name="batasPembayaran" required/>
                    </div>

                    <div class="form-group">
                        <label>Durasi Sewa</label>
                        <input class="form-control" name="durasiSewa" required/>
                    </div>

                    <div class="form-group">
                        <label>Status Pembayaran</label>
                        <select class="form-control" name="statusPembayaran">
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>ID Penyewa</label>
                        <input class="form-control" name="idPenyewa" required/>
                    </div>

                    <div class="form-group">
                        <label>Jatuh Tempo</label>
                        <input type="date" class="form-control" name="jatuhTempo" required/>
                    </div>

                    <div>
                        <input type="submit" name="simpan" value="Tambah" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
