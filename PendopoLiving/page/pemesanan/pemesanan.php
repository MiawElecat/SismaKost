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
                <i class="fa fa-users"></i> Data Pemesanan
            </div>
            <div class="panel-body">
                 <!-- Form Pencarian -->
                 <form method="GET" action="" class="mb-4">
                    <div class="input-group" style="max-width: 300px; float: left;">
                        <input type="hidden" name="page" value="pemesanan">
                        <input type="text" name="cari" class="form-control" placeholder="Cari Pemesanan..." value="<?php echo htmlspecialchars($keyword); ?>">
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
                                <th>ID Pemesanan</th>
                                <th>Pemesanan Kamar</th>
                                <th>Uang Muka</th>
                                <th>Status Uang Muka</th>
                                <th>Tenggat Uang Muka</th>
                                <th>Mulai Menempati Kos</th>
                                <th>Batas Menempati Kos</th>
                                <th>ID Penyewa</th>
                                <th>ID Kamar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;
                                // Query dengan filter pencarian
                                $sql = $koneksi->query("SELECT * FROM pemesanan 
                                                        WHERE (id_pemesanan LIKE '%$keyword%' 
                                                        OR pemesanan_kamar LIKE '%$keyword%' 
                                                        OR id_penyewa LIKE '%$keyword%' 
                                                        OR idKamar LIKE '%$keyword%' 
                                                        OR status LIKE '%$keyword%')");
                                
                                if ($sql === false) {
                                    echo "<tr><td colspan='10'>Error: " . $koneksi->error . "</td></tr>";
                                } else {
                                    $dataFound = false; // Set flag to false initially
                                    while ($data = $sql->fetch_assoc()) {
                                        $dataFound = true; // Set flag to true if data is found
                                        $statusUangMuka = htmlspecialchars($data['status_uang_muka']);
                                        
                                        // Assign badge class based on the value of status_uang_muka
                                        if ($statusUangMuka === 'Bayar Penuh') {
                                            $badgeClassUM = 'badge-success';  // Green for full payment
                                        } elseif ($statusUangMuka === 'DP 30%') {
                                            $badgeClassUM = 'badge-warning';  // Yellow for down payment
                                        } else {
                                            $badgeClassUM = 'badge-secondary';  // Default (if any other value)
                                        }
                                    
                                        $statusPemesanan = htmlspecialchars($data['status']); 
                                        // Menentukan kelas badge berdasarkan status pemesanan
                                        if ($statusPemesanan === 'Menunggu Pembayaran') {
                                            $badgeClassStatus = 'badge-warning';  // Kuning untuk 'Menunggu Pembayaran'
                                        } elseif ($statusPemesanan === 'Menunggu Dikonfirmasi') {
                                            $badgeClassStatus = 'badge-primary';  // Biru untuk 'Menunggu Dikonfirmasi'
                                        } elseif ($statusPemesanan === 'Dikonfirmasi') {
                                            $badgeClassStatus = 'badge-success';  // Hijau untuk 'Dikonfirmasi'
                                        } elseif ($statusPemesanan === 'Perpanjangan') {
                                            $badgeClassStatus = 'badge-secondary';  // Abu-abu untuk 'Perpanjangan'
                                        } else {
                                            // Cek jika status kosong atau tidak sesuai
                                            $badgeClassStatus = 'badge-dark';  // Gelap untuk status yang tidak dikenali
                                        }
                            ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($data['id_pemesanan']); ?></td>
                                            <td><?php echo htmlspecialchars($data['pemesanan_kamar']); ?></td>
                                            <td><?php echo formatRupiah($data['uang_muka']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $badgeClassUM; ?>">
                                                    <?php echo $statusUangMuka; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['tenggat_uang_muka']); ?></td>
                                            <td><?php echo htmlspecialchars($data['mulai_menempati_kos']); ?></td>
                                            <td><?php echo htmlspecialchars($data['batas_menempati_kos']); ?></td>
                                            <td><?php echo htmlspecialchars($data['id_penyewa']); ?></td>
                                            <td><?php echo htmlspecialchars($data['idKamar']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $badgeClassStatus; ?>">
                                                    <?php echo $statusPemesanan; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?page=pemesanan&aksi=edit&idPemesanan=<?php echo $data['id_pemesanan'];?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <!-- <a onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" 
                                                   href="?page=pemesanan&aksi=hapus&idPemesanan=<?php echo $data['id_pemesanan'];?>" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </a> -->
                                            </td>
                                        </tr>
                            <?php 
                                    }
                                    if (!$dataFound) {
                                        echo "<tr><td colspan='12' class='text-center'>Tidak ada data yang ditemukan</td></tr>";
                                    }
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Tambah Data -->
                <a href="?page=pemesanan&aksi=tambah" class="btn btn-primary" style="margin-bottom: 8px;">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            </div>
        </div>
        <!-- End Advanced Tables -->
    </div>
</div>
