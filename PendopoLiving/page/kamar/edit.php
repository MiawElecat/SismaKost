<?php
$idKamar = $_GET['idKamar'];

// Mengambil data kamar yang akan diedit
$sqlKamar = $koneksi->prepare("SELECT * FROM kamar WHERE idKamar = ?");
$sqlKamar->bind_param("i", $idKamar);
$sqlKamar->execute();
$resultKamar = $sqlKamar->get_result();
$dataKamar = $resultKamar->fetch_assoc();

// Mengambil fasilitas yang sudah dimiliki oleh kamar ini
$sqlFasilitasKamar = $koneksi->prepare("SELECT idFasilitas FROM kamar_fasilitas WHERE idKamar = ?");
$sqlFasilitasKamar->bind_param("i", $idKamar);
$sqlFasilitasKamar->execute();
$resultFasilitasKamar = $sqlFasilitasKamar->get_result();
$fasilitasKamar = [];
while ($row = $resultFasilitasKamar->fetch_assoc()) {
    $fasilitasKamar[] = $row['idFasilitas'];
}

// Menyimpan perubahan data kamar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['simpan'])) {
        $namaKamar = $_POST['namaKamar'];
        $nomorKamar = $_POST['nomorKamar'];
        $harga = $_POST['harga'];
        $status = $_POST['status'];
        $idBlok = $_POST['blokKamar'];

        // Update data kamar
        $sqlUpdateKamar = $koneksi->prepare("UPDATE kamar SET namaKamar = ?, nomorKamar = ?, harga = ?, status = ?, idBlok = ? WHERE idKamar = ?");
        $sqlUpdateKamar->bind_param("ssissi", $namaKamar, $nomorKamar, $harga, $status, $idBlok, $idKamar);
        
        if ($sqlUpdateKamar->execute()) {
            // Hapus fasilitas lama dari kamar ini
            $koneksi->query("DELETE FROM kamar_fasilitas WHERE idKamar = $idKamar");
            
            // Menyimpan fasilitas yang dipilih
            if (isset($_POST['fasilitas'])) {
                foreach ($_POST['fasilitas'] as $idFasilitas) {
                    $sqlFasilitas = $koneksi->prepare("INSERT INTO kamar_fasilitas (idKamar, idFasilitas) VALUES (?, ?)");
                    $sqlFasilitas->bind_param("ii", $idKamar, $idFasilitas);
                    $sqlFasilitas->execute();
                }
            }
            
            echo '
            <script type="text/javascript">
                alert("Data berhasil diperbarui");
                window.location.href="?page=kamar";
            </script>';
        } else {
            echo '<script>alert("Data gagal diperbarui: ' . $koneksi->error . '");</script>';
        }
    }
}
?>

<!-- Formulir HTML untuk mengedit data kamar -->
<div class="panel panel-default">
    <div class="panel-heading">
        Edit Data Kamar
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Kamar</label>
                        <input class="form-control" name="namaKamar" value="<?php echo $dataKamar['namaKamar']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label>Nomor Kamar</label>
                        <input class="form-control" name="nomorKamar" value="<?php echo $dataKamar['nomorKamar']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input class="form-control" name="harga" type="number" value="<?php echo $dataKamar['harga']; ?>" required/>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="Tersedia" <?php if ($dataKamar['status'] == 'Tersedia') echo 'selected'; ?>>Tersedia</option>
                            <option value="Kosong" <?php if ($dataKamar['status'] == 'Kosong') echo 'selected'; ?>>Kosong</option>
                            <option value="Booking" <?php if ($dataKamar['status'] == 'Booking') echo 'selected'; ?>>Booking</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Blok Kamar</label>
                        <select class="form-control" name="blokKamar" required>
                            <?php
                            $sqlBlok = $koneksi->query("SELECT * FROM blok");
                            while ($blok = $sqlBlok->fetch_assoc()) {
                                $selected = $blok['idBlok'] == $dataKamar['idBlok'] ? 'selected' : '';
                                echo '<option value="' . $blok['idBlok'] . '" ' . $selected . '>' . $blok['namaBlok'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fasilitas</label><br>
                        <?php
                        $sqlFasilitas = $koneksi->query("SELECT * FROM fasilitas");
                        while ($fasilitas = $sqlFasilitas->fetch_assoc()) {
                            $checked = in_array($fasilitas['idFasilitas'], $fasilitasKamar) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="fasilitas[]" value="' . $fasilitas['idFasilitas'] . '" ' . $checked . '> ' . $fasilitas['namaFasilitas'] . '</label><br>';
                        }
                        ?>
                    </div>

                    <div>
                        <input type="submit" name="simpan" value="Perbarui" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
