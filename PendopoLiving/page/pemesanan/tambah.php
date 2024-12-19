<?php
// Include database connection
include('database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pemesananKamar = $_POST['pemesanan_kamar'];
    $uangMuka = $_POST['uang_muka'];
    $statusUangMuka = $_POST['status_uang_muka'];
    $tenggatUangMuka = $_POST['tenggat_uang_muka'];
    $mulaiMenempatiKos = $_POST['mulai_menempati_kos'];
    $batasMenempatiKos = $_POST['batas_menempati_kos'];
    $idPenyewa = $_POST['id_penyewa'];
    $idKamar = $_POST['id_kamar'];
    $status = $_POST['status'];

    // Insert data into the pemesanan table
    $sql = "INSERT INTO pemesanan (pemesanan_kamar, uang_muka, status_uang_muka, tenggat_uang_muka, mulai_menempati_kos, batas_menempati_kos, id_penyewa, idKamar, status)
            VALUES ('$pemesananKamar', '$uangMuka', '$statusUangMuka', '$tenggatUangMuka', '$mulaiMenempatiKos', '$batasMenempatiKos', '$idPenyewa', '$idKamar', '$status')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>alert('Data pemesanan berhasil ditambahkan.'); window.location.href='?page=pemesanan';</script>";
    } else {
        echo "<script>alert('Error: " . $sql . " - " . $koneksi->error . "');</script>";
    }
}
?>

<!-- Add New Pemesanan Form -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Tambah Pemesanan
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="pemesanan_kamar">Pemesanan Kamar</label>
                        <input type="text" name="pemesanan_kamar" id="pemesanan_kamar" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="uang_muka">Uang Muka</label>
                        <input type="number" name="uang_muka" id="uang_muka" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="status_uang_muka">Status Uang Muka</label>
                        <select name="status_uang_muka" id="status_uang_muka" class="form-control">
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tenggat_uang_muka">Tenggat Uang Muka</label>
                        <input type="date" name="tenggat_uang_muka" id="tenggat_uang_muka" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="mulai_menempati_kos">Mulai Menempati Kos</label>
                        <input type="date" name="mulai_menempati_kos" id="mulai_menempati_kos" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="batas_menempati_kos">Batas Menempati Kos</label>
                        <input type="date" name="batas_menempati_kos" id="batas_menempati_kos" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="id_penyewa">ID Penyewa</label>
                        <input type="number" name="id_penyewa" id="id_penyewa" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="id_kamar">ID Kamar</label>
                        <input type="number" name="id_kamar" id="id_kamar" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="?page=pemesanan" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
