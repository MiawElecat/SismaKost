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
                <i class="fa fa-users"></i> Data Pembayaran
            </div>
            <div class="panel-body">
                <!-- Form Pencarian -->
                <form method="GET" action="" class="mb-4">
                    <div class="input-group" style="max-width: 300px; float: left;">
                        <input type="hidden" name="page" value="pembayaran">
                        <input type="text" name="cari" class="form-control" placeholder="Cari Pembayaran..." value="<?php echo htmlspecialchars($keyword); ?>">
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
                                <th>ID Pembayaran</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Batas Pembayaran</th>
                                <th>Durasi Sewa</th>
                                <th>Status Pembayaran</th>
                                <th>ID Penyewa</th>
                                <th>Jatuh Tempo</th>
                                <th>ID Pemesanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;

                                // Cek jika ada pencarian atau tidak
                                if ($keyword != '') {
                                    // Jika ada pencarian, filter berdasarkan keyword
                                    $sql = $koneksi->query("SELECT * FROM pembayaran WHERE idPembayaran LIKE '%$keyword%' OR idPenyewa LIKE '%$keyword%'");
                                } else {
                                    // Jika tidak ada pencarian, tampilkan semua data
                                    $sql = $koneksi->query("SELECT * FROM pembayaran");
                                }

                                // Cek apakah query berhasil
                                if ($sql === false) {
                                    echo "<tr><td colspan='10'>Error: " . $koneksi->error . "</td></tr>";
                                } else {
                                    // Cek jika tidak ada data ditemukan
                                    if ($sql->num_rows == 0) {
                                        echo "<tr><td colspan='10'>Tidak ada data yang ditemukan.</td></tr>";
                                    } else {
                                        // Tampilkan data
                                        while ($data = $sql->fetch_assoc()) {
                            ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($data['idPembayaran']); ?></td>
                                                <td><?php echo htmlspecialchars($data['tanggalPembayaran']); ?></td>
                                                <td><?php echo htmlspecialchars($data['batasPembayaran']); ?></td>
                                                <td><?php echo htmlspecialchars($data['durasiSewa']); ?> bulan</td>
                                                <td>
                                                    <?php
                                                        $status = htmlspecialchars($data['StatusPembayaran']);
                                                        $badgeClass = ($status === 'Lunas') ? 'badge-success' : 'badge-danger';
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo $status; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($data['idPenyewa']); ?></td>
                                                <td>
                                                <?php echo htmlspecialchars($data['jatuh_tempo'] ?? 'Belum Ada'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($data['id_pemesanan']); ?></td>
                                                <td>
                                                    <a href="?page=pembayaran&aksi=edit&id=<?php echo $data['idPembayaran']; ?>" 
                                                       class="btn btn-info btn-sm">
                                                        <i class="fa fa-eye"></i> Edit
                                                    </a>
                                                    <!-- <a onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" 
                                                       href="?page=pembayaran&aksi=hapus&id=<?php echo $data['idPembayaran']; ?>" 
                                                       class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </a> -->
                                                </td>
                                            </tr>
                            <?php 
                                        }
                                    }
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Advanced Tables -->
    </div>
</div>
