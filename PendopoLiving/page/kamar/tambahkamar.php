<?php
// Memeriksa apakah formulir telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['simpan'])) {
        // Mengambil data dari form
        $namaKamar = $_POST['namaKamar'];
        $nomorKamar = $_POST['nomorKamar'];
        $harga = $_POST['harga'];
        $status = $_POST['status'];
        $idBlok = $_POST['blokKamar'];  // Ambil data idBlok dari form
        
        // Mengelola unggahan file foto
        $foto = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_size = $_FILES['foto']['size'];
        $foto_ext = pathinfo($foto, PATHINFO_EXTENSION);

        // Validasi ekstensi dan ukuran file foto
        $allowed_ext = array("jpg", "jpeg", "png", "gif");
        if (in_array($foto_ext, $allowed_ext)) {
            if ($foto_size <= 5000000) {  // Maksimal ukuran 5MB
                $target = "uploads/" . basename($foto);
                
                // Memindahkan file ke folder tujuan
                if (move_uploaded_file($foto_tmp, $target)) {
                    // Menyiapkan query untuk menambahkan data kamar
                    $sql = $koneksi->prepare("INSERT INTO kamar (namaKamar, nomorKamar, harga, status, foto, idBlok) VALUES (?, ?, ?, ?, ?, ?)");
                    $sql->bind_param("ssisss", $namaKamar, $nomorKamar, $harga, $status, $foto, $idBlok);
                    
                    if ($sql->execute()) {
                        // Mendapatkan idKamar yang baru saja dimasukkan
                        $idKamar = $koneksi->insert_id;
                        
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
                            alert("Data berhasil ditambahkan");
                            window.location.href="?page=kamar";
                        </script>';
                    } else {
                        echo '<script>alert("Data gagal ditambahkan: ' . $koneksi->error . '");</script>';
                    }
                } else {
                    echo '<script>alert("Gagal mengunggah foto. Pastikan file dalam format yang valid.");</script>';
                }
            } else {
                echo '<script>alert("Ukuran foto terlalu besar. Maksimal 5MB.");</script>';
            }
        } else {
            echo '<script>alert("Ekstensi file foto tidak valid. Hanya JPG, JPEG, PNG, dan GIF yang diizinkan.");</script>';
        }
    }
}
?>

<!-- Formulir HTML untuk menambahkan data kamar baru -->
<div class="panel panel-default">
    <div class="panel-heading">
        Tambah Data Kamar
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Kamar</label>
                        <input class="form-control" name="namaKamar" required/>
                    </div>

                    <div class="form-group">
                        <label>Nomor Kamar</label>
                        <input class="form-control" name="nomorKamar" required/>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input class="form-control" name="harga" type="number" required/>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Kosong">Kosong</option>
                            <option value="Booking">Booking</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Blok Kamar</label>
                        <select class="form-control" name="blokKamar" required>
                            <?php
                            // Menampilkan pilihan blok dari tabel blok
                            $sqlBlok = $koneksi->query("SELECT * FROM blok");
                            while ($blok = $sqlBlok->fetch_assoc()) {
                                echo '<option value="' . $blok['idBlok'] . '">' . $blok['namaBlok'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" class="form-control" name="foto" required/>
                    </div>

                    <div class="form-group">
                        <label>Fasilitas</label><br>
                        <?php
                        // Menampilkan fasilitas dari database
                        $sqlFasilitas = $koneksi->query("SELECT * FROM fasilitas");
                        while ($fasilitas = $sqlFasilitas->fetch_assoc()) {
                            echo '<label><input type="checkbox" name="fasilitas[]" value="' . $fasilitas['idFasilitas'] . '"> ' . $fasilitas['namaFasilitas'] . '</label><br>';
                        }
                        ?>
                    </div>

                    <div>
                        <input type="submit" name="simpan" value="Tambah" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
