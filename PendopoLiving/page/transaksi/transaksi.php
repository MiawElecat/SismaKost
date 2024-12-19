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
                <i class="fa fa-money"></i> Data Transaksi
            </div>
            <div class="panel-body">
                <!-- Form Pencarian -->
                <form method="GET" action="" class="mb-4">
                    <div class="input-group" style="max-width: 300px; float: left;">
                        <input type="hidden" name="page" value="transaksi">
                        <input type="text" name="cari" class="form-control" placeholder="Cari Transaksi..." value="<?php echo htmlspecialchars($keyword); ?>">
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
                                <th>ID Transaksi</th>
                                <th>ID Penyewa</th>
                                <th>ID Pemesanan</th>
                                <th>Jenis Transaksi</th>
                                <th>Jumlah Transaksi</th>
                                <th>Tanggal Transaksi</th>
                                <th>Metode Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;
                                // Query dengan filter pencarian
                                $sql = $koneksi->query("SELECT * FROM transaksi 
                                                        WHERE (id_transaksi LIKE '%$keyword%' 
                                                        OR jenis_transaksi LIKE '%$keyword%' 
                                                        OR id_penyewa LIKE '%$keyword%' 
                                                        OR id_pemesanan LIKE '%$keyword%' 
                                                        OR metode_bayar LIKE '%$keyword%')");
                                
                                if ($sql === false) {
                                    echo "<tr><td colspan='9'>Error: " . $koneksi->error . "</td></tr>";
                                } else {
                                    $dataFound = false; // Set flag to false initially
                                    while ($data = $sql->fetch_assoc()) {
                                        $dataFound = true; // Set flag to true if data is found
                                        
                                        // Assign badge class based on the value of jenis_transaksi
                                        $jenisTransaksi = htmlspecialchars($data['jenis_transaksi']);
                                        if ($jenisTransaksi === 'Bayar Penuh') {
                                            $badgeClassJenis = 'badge-success';  // Green for full payment
                                        } elseif ($jenisTransaksi === 'DP 30%') {
                                            $badgeClassJenis = 'badge-warning';  // Yellow for down payment
                                        } elseif ($jenisTransaksi === 'Sisa Pembayaran') {
                                            $badgeClassJenis = 'badge-info';  // Blue for remaining payment
                                        } else {
                                            $badgeClassJenis = 'badge-secondary'; // Default
                                        }
                            ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($data['id_transaksi']); ?></td>
                                            <td><?php echo htmlspecialchars($data['id_penyewa']); ?></td>
                                            <td><?php echo htmlspecialchars($data['id_pemesanan']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $badgeClassJenis; ?>">
                                                    <?php echo $jenisTransaksi; ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatRupiah($data['jumlah_transaksi']); ?></td>
                                            <td><?php echo htmlspecialchars($data['tanggal_transaksi']); ?></td>
                                            <td><?php echo htmlspecialchars($data['metode_bayar']); ?></td>
                                        </tr>
                            <?php 
                                    }
                                    if (!$dataFound) {
                                        echo "<tr><td colspan='9' class='text-center'>Tidak ada data yang ditemukan</td></tr>";
                                    }
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Tambah Data -->
                <a href="?page=transaksi&aksi=tambah" class="btn btn-primary" style="margin-bottom: 8px;">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            </div>
        </div>
        <!-- End Advanced Tables -->
    </div>
</div>
