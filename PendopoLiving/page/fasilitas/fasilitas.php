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
                <i class="fa fa-users"></i> Data Fasilitas
            </div>
            <div class="panel-body">
            <form method="GET" action="" class="mb-4">
                    <div class="input-group" style="max-width: 300px; float: left;">
                        <input type="hidden" name="page" value="fasilitas">
                        <input type="text" name="cari" class="form-control" placeholder="Cari Fasilitas..." value="<?php echo htmlspecialchars($keyword); ?>">
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
                                <th>Nama Fasilitas</th>
                                <th>Biaya Tambahan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;
                                // Modify the query to include the search functionality
                                $sql = $koneksi->query("SELECT * FROM fasilitas WHERE namaFasilitas LIKE '%$keyword%'");

                                if ($sql === false) {
                                    echo "<tr><td colspan='4'>Error: " . $koneksi->error . "</td></tr>";
                                } else {
                                    $resultsFound = false; // Flag to check if results are found
                                    while ($data = $sql->fetch_assoc()) {
                                        $resultsFound = true;
                            ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($data['namaFasilitas']); ?></td>
                                            <td><?php echo formatRupiah($data['biayaTambahan']); ?></td>
                                            <td>
                                                <a href="?page=fasilitas&aksi=edit&idFasilitas=<?php echo $data['idFasilitas']; ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <a onclick="return confirm('Apakah Anda Yakin Akan Menghapus Data ini..?')" 
                                                   href="?page=fasilitas&aksi=hapus&idFasilitas=<?php echo $data['idFasilitas']; ?>" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                            <?php 
                                    }
                                    if (!$resultsFound) {
                                        echo "<tr><td colspan='4'>Tidak ada hasil yang ditemukan untuk pencarian '<strong>" . htmlspecialchars($keyword) . "</strong>'</td></tr>";
                                    }
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Buttons for Adding and Exporting -->
                <a href="?page=fasilitas&aksi=tambah" class="btn btn-success btn-sm" style="margin-bottom: 8px;">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </div>
</div>
