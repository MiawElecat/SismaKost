<?php 
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

$keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
?>

<div class="row">
    <div class="col-md-12">
        <!-- Advanced Tables -->
        <div class="panel panel-default">
        <div class="panel-heading text-center" style="font-size: 24px; font-weight: bold; background-color: #f8f9fa;">
                <i class="fa fa-users"></i> Data Kamar
            </div>
            <div class="panel-body">
                <form method="GET" action="" class="mb-4">
                    <div class="input-group" style="max-width: 300px; float: left;">
                        <input type="hidden" name="page" value="kamar">
                        <input type="text" name="cari" class="form-control" placeholder="Cari Kamar..." value="<?php echo htmlspecialchars($keyword); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Kamar</th>
                                <th>Nomor Kamar</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Foto</th>
                                <th>Fasilitas</th>
                                <th>Blok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;
                                // Modify the SQL query to include the search keyword in the WHERE clause
                                $sql = $koneksi->query("
                                    SELECT kamar.*, 
                                           GROUP_CONCAT(fasilitas.namaFasilitas ORDER BY fasilitas.namaFasilitas ASC) AS fasilitasList,
                                           blok.namaBlok
                                    FROM kamar
                                    LEFT JOIN kamar_fasilitas ON kamar.idKamar = kamar_fasilitas.idKamar
                                    LEFT JOIN fasilitas ON kamar_fasilitas.idFasilitas = fasilitas.idFasilitas
                                    LEFT JOIN blok ON kamar.idBlok = blok.idBlok
                                    WHERE kamar.namaKamar LIKE '%$keyword%' 
                                       OR kamar.nomorKamar LIKE '%$keyword%' 
                                       OR kamar.harga LIKE '%$keyword%' 
                                       OR blok.namaBlok LIKE '%$keyword%' 
                                    GROUP BY kamar.idKamar
                                ");

                                if ($sql === false) {
                                    echo "<tr><td colspan='9'>Error: " . $koneksi->error . "</td></tr>";
                                } else {
                                    $resultsFound = false; // Flag to check if results are found
                                    while ($data = $sql->fetch_assoc()) {
                                        $resultsFound = true;
                            ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($data['namaKamar']); ?></td>
                                            <td><?php echo htmlspecialchars($data['nomorKamar']); ?></td>
                                            <td><?php echo formatRupiah($data['harga']); ?></td>
                                            <td><?php echo htmlspecialchars($data['status']); ?></td>
                                            <td>
                                                <!-- Thumbnail gambar -->
                                                <img src="uploads/<?php echo $data['foto']; ?>" alt="Foto Kamar" width="100" 
                                                     data-toggle="modal" data-target="#fotoModal<?php echo $data['idKamar']; ?>" 
                                                     style="cursor: pointer;">
                                            </td>
                                            <td><?php echo htmlspecialchars($data['fasilitasList']); ?></td>
                                            <td><?php echo htmlspecialchars($data['namaBlok']); ?></td>
                                            <td>
                                                <a href="?page=kamar&aksi=edit&idKamar=<?php echo $data['idKamar']; ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <a onclick="return confirm('Apakah Anda Yakin Akan Menghapus Data ini..?')" 
                                                   href="?page=kamar&aksi=hapus&idKamar=<?php echo $data['idKamar']; ?>" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Modal untuk foto besar -->
                                        <div class="modal fade" id="fotoModal<?php echo $data['idKamar']; ?>" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel<?php echo $data['idKamar']; ?>" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h5 class="modal-title" id="fotoModalLabel<?php echo $data['idKamar']; ?>">Foto Kamar</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img src="uploads/<?php echo $data['foto']; ?>" alt="Foto Kamar" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            <?php 
                                    }
                                    if (!$resultsFound) {
                                        echo "<tr><td colspan='12' class='text-center'>Tidak ada data yang ditemukan</td></tr>";
                                    }
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Tambah -->
                <a href="?page=kamar&aksi=tambah" class="btn btn-success btn-sm" style="margin-bottom: 8px;">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </div>
</div>
