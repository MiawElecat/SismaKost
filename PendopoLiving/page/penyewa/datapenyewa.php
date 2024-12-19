<?php
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Menangkap nilai pencarian
$keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
?>

<div class="row">
    <div class="col-md-12">
        <!-- Advanced Tables -->
        <div class="panel panel-default">
            <div class="panel-heading text-center" style="font-size: 24px; font-weight: bold; background-color: #f8f9fa;">
                <i class="fa fa-users"></i> Data Penyewa
            </div>
            <div class="panel-body">
                <!-- Form Pencarian -->
                <form method="GET" action="" class="mb-4">
                    <div class="input-group" style="max-width: 300px; float: left;">
                        <input type="hidden" name="page" value="penyewa">
                        <input type="text" name="cari" class="form-control" placeholder="Cari Penyewa..." value="<?php echo htmlspecialchars($keyword); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-center" id="dataTables-example">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Penyewa</th>
                                <th>No Telepon</th>
                                <th>Email</th>
                                <th>Foto Jaminan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;

                                // Query dengan filter pencarian
                                $sql = $koneksi->query("
                                    SELECT penyewa.*, kamar.namaKamar 
                                    FROM penyewa 
                                    LEFT JOIN kamar ON penyewa.idKamar = kamar.idKamar
                                    WHERE penyewa.namaPenyewa LIKE '%$keyword%' 
                                       OR penyewa.noTelepon LIKE '%$keyword%' 
                                       OR penyewa.email LIKE '%$keyword%'
                                ");

                                if ($sql === false) {
                                    echo "<tr><td colspan='6'>Error: " . $koneksi->error . "</td></tr>";
                                } else {
                                    if ($sql->num_rows > 0) {
                                        while ($data = $sql->fetch_assoc()) {
                                            $namaKamar = $data['namaKamar'] ?? 'Belum Terpilih';
                                            $fotoJaminan = $data['fotoJaminan'];
                            ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($data['namaPenyewa']); ?></td>
                                                <td><?php echo htmlspecialchars($data['noTelepon']); ?></td>
                                                <td><?php echo htmlspecialchars($data['email']); ?></td>
                                                <td>
                                                    <?php if ($fotoJaminan): ?>
                                                        <img src="uploads/<?php echo htmlspecialchars($fotoJaminan); ?>" width="50" height="50" class="img-thumbnail" 
                                                             alt="Foto Jaminan" data-toggle="modal" data-target="#jaminanModal<?php echo $data['idPenyewa']; ?>">
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada foto</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?page=penyewa&aksi=edit&idPenyewa=<?php echo $data['idPenyewa'];?>" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    <a onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" 
                                                       href="?page=penyewa&aksi=hapus&idPenyewa=<?php echo $data['idPenyewa'];?>" 
                                                       class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Modal untuk melihat gambar jaminan -->
                                            <div class="modal fade" id="jaminanModal<?php echo $data['idPenyewa']; ?>" tabindex="-1" role="dialog" aria-labelledby="jaminanModalLabel<?php echo $data['idPenyewa']; ?>" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="jaminanModalLabel<?php echo $data['idPenyewa']; ?>">Foto Jaminan</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="uploads/<?php echo htmlspecialchars($fotoJaminan); ?>" alt="Foto Jaminan" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                            <?php 
                                        }
                                    } else {
                                        // Tampilkan pesan jika tidak ada data
                                        echo "<tr><td colspan='6' class='text-center text-muted'>Tidak ada data yang ditemukan.</td></tr>";
                                    }
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol untuk menambah data -->
                <a href="?page=penyewa&aksi=tambah" class="btn btn-primary" style="margin-bottom: 8px;">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            </div>
        </div>
        <!-- End Advanced Tables -->
    </div>
</div>
