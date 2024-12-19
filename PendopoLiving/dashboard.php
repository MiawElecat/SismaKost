<?php
session_start();
include 'koneksi.php';

$namaAdmin = 'Pengguna'; // Default value if session idAdmin is not found

if (isset($_SESSION['idAdmin'])) {
    $idAdmin = $_SESSION['idAdmin'];

    $query = "SELECT namaAdmin FROM admin WHERE idAdmin = ?";
    $stmt = $koneksi->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $idAdmin);
        if ($stmt->execute()) {
            $stmt->bind_result($namaAdmin);
            $stmt->fetch();
            $stmt->close();
        } else {
            echo "Error: Failed to execute query.";
        }
    }
}

// Cek status pemesanan yang masih "Menunggu Dikonfirmasi"
$query = "SELECT COUNT(*) AS total FROM pemesanan WHERE status IN ('Menunggu Dikonfirmasi', 'Perpanjangan')";
$result = $koneksi->query($query);
$row = $result->fetch_row();
$pendingOrders = $row[0]; // Jumlah pemesanan yang belum dikonfirmasi

// Jika ada pemesanan yang belum dikonfirmasi, tampilkan indikator
$showBellIndicator = $pendingOrders > 0 ? 'block' : 'none';


// Hitung jumlah pesan baru berdasarkan kriteria waktu (contoh: pesan dalam 24 jam terakhir)
// Query untuk menghitung jumlah pesan yang belum dibaca
$status = isset($_GET['status']) ? $_GET['status'] : 'Belum Dibaca'; // Default ke 'Belum Dibaca'

// Query untuk menghitung jumlah pesan sesuai status
$queryCount = "SELECT COUNT(*) FROM pesan WHERE `read` = '$status'";
$result = $koneksi->query($queryCount);
$row = $result->fetch_row();
$newMessages = $row[0];  // Mengambil hasil COUNT(*) untuk jumlah pesan sesuai status

// Query untuk mengambil data pesan berdasarkan status
$query = "SELECT idPesan, idPenyewa, subject, message, created_at FROM pesan WHERE `read` = '$status' ORDER BY created_at DESC";
$result = $koneksi->query($query);
$messages = [];

// Menyimpan data pesan dalam array $messages
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elisa Kost</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">

    <style>
        /* Styling untuk profile container */
        .profile {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-radius: 20px;
            margin: auto;
        }

       /* Styling untuk ikon bell */
        .right-section .bx-bell {
            position: relative; /* Posisi relatif untuk penempatan badge */
            font-size: 16px; /* Ukuran ikon bel */
            margin-left: 20px;
        }

        .badge-indicator {
            position: absolute;
            top: -5px; /* Atur sedikit di atas */
            right: -5px; /* Atur sedikit di kanan */
            background-color: red; /* Warna latar belakang badge */
            color: white; /* Warna teks badge */
            border-radius: 50%; /* Bentuk bulat */
            width: 15px; /* Lebar badge */
            height: 15px; /* Tinggi badge */
            display: none; /* Tersembunyi secara default */
            text-align: center; /* Pusatkan angka di dalam badge */
            font-size: 12px; /* Ukuran teks angka */
            font-weight: bold; /* Menebalkan teks */
            line-height: 15px; /* Menjaga angka berada di tengah secara vertikal */
        }

        /* Menampilkan indikator jika ada notifikasi */
        #bell-indicator {
            display: block; /* Tampilkan indikator jika ada pemesanan yang menunggu konfirmasi */
        }

        .right-section .bx-envelope {
            position: relative; /* Posisi relatif untuk penempatan badge */
            font-size: 16px; /* Ukuran ikon amplop */
        }

        #envelope-indicator {
            position: absolute; /* Untuk mengatur posisi relatif terhadap ikon */
            top: -5px; /* Atur sedikit di atas */
            right: -5px; /* Atur sedikit di kanan */
            background-color: red; /* Warna latar belakang badge */
            color: white; /* Warna teks badge */
            border-radius: 50%; /* Membentuk lingkaran */
            width: 15px; /* Lebar indikator */
            height: 15px; /* Tinggi indikator */
            display: none; /* Tersembunyi secara default */
            text-align: center; /* Pusatkan teks di dalam lingkaran */
            font-size: 12px; /* Ukuran teks di dalam indikator */
            font-weight: bold; /* Teks tebal */
            line-height: 15px; /* Vertikal tengah untuk teks */
        }

        /* Tampilkan indikator jika ada pesan baru */
        #envelope-indicator[data-show="true"] {
            display: block; /* Tampilkan indikator */
        }



        .profile .info a {
            font-weight: bold;
            color: white; /* Nama pengguna berwarna putih */
            text-decoration: none;
            font-size: 16px;
        }

        .profile .info p {
            margin: 0;
            font-size: 12px;
            color: #ccc;
        }

        .profile .dropdown-trigger {
            cursor: pointer;
            color: white; /* Warna panah dropdown putih */
            font-size: 18px;
            margin-top: 5px; /* Jarak antara panah dan teks */
        }

        /* Styling untuk dropdown menu */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 120%;
            left: 100%;
            transform: translateX(-50%);
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 150px;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .profile.active .dropdown-menu {
            display: block;
        }

        .table th, .table td {
         vertical-align: middle; /* Teks di tengah secara vertikal */
        }

        .table img {
            max-width: 100px; /* Ukuran maksimum untuk gambar */
            height: auto;
            border-radius: 5px; /* Tambahkan border radius jika diperlukan */
            object-fit: cover;
        }

            /* Mengatur modal agar sesuai dengan lebar kolom */
        .custom-modal .modal-dialog {
            max-width: 100%; /* Memaksimalkan lebar hingga ukuran kontainer */
            width: 80%; /* Pastikan ukurannya proporsional */
            margin: auto; /* Pusatkan modal */
        }

        .custom-modal .modal-content {
            overflow-x: auto; /* Tambahkan scroll horizontal jika tabel lebih lebar */
        }

        /* Tambahkan padding untuk membuat tampilan lebih rapi */
        .custom-modal .modal-body {
            padding: 20px;
        }

    </style>


    <script>
 document.addEventListener('DOMContentLoaded', () => {
    // Tampilkan modal dropdown pada profile
    const profile = document.querySelector('.profile');
    const dropdownTrigger = profile.querySelector('.dropdown-trigger');

    dropdownTrigger.addEventListener('click', () => {
        profile.classList.toggle('active');
    });

    document.addEventListener('click', (event) => {
        if (!profile.contains(event.target)) {
            profile.classList.remove('active');
        }
    });

    // Tampilkan modal bell notification
    const bellIcon = document.querySelector('.bx-bell');
    const bellIndicator = document.getElementById('bell-indicator');
    <?php echo "bellIndicator.style.display = '$showBellIndicator';"; ?>

    bellIcon.addEventListener('click', () => {
        fetchPendingOrders();
        $('#pendingOrdersModal').modal('show'); // Tampilkan modal pemesanan
    });

     // Tampilkan modal pesan
    const envelopeIcon = document.querySelector('.bx-envelope');
    const envelopeIndicator = document.getElementById('envelope-indicator');

    envelopeIcon.addEventListener('click', () => {
        $('#messageModal').modal('show'); // Tampilkan modal pesan
    });

    // Input pencarian dalam tabel pemesanan
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('pendingOrdersTableBody');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        let visibleRows = 0;

        Array.from(tableBody.rows).forEach(row => {
            const cells = Array.from(row.cells);
            const rowText = cells.map(cell => cell.textContent.toLowerCase()).join(' ');

            if (rowText.includes(query)) {
                row.style.display = ''; // Tampilkan baris
                visibleRows++;
            } else {
                row.style.display = 'none'; // Sembunyikan baris
            }
        });

        // Tampilkan pesan jika tidak ada data ditemukan
        if (visibleRows === 0) {
            if (!document.getElementById('noDataRow')) {
                const noDataRow = document.createElement('tr');
                noDataRow.id = 'noDataRow';
                noDataRow.innerHTML = `<td colspan="8">Tidak ada data ditemukan</td>`;
                tableBody.appendChild(noDataRow);
            }
        } else {
            const noDataRow = document.getElementById('noDataRow');
            if (noDataRow) {
                noDataRow.remove();
            }
        }
    });

    // Event listener untuk gambar bukti transfer
    tableBody.addEventListener('click', (event) => {
        if (event.target.tagName === 'IMG') {
            const imgSrc = event.target.getAttribute('src');
            const modalImage = document.getElementById('modalImage');
            modalImage.setAttribute('src', imgSrc);
            $('#imageModal').modal('show'); // Tampilkan modal gambar
        }
    });
});

// Fungsi untuk mengambil data pemesanan yang menunggu konfirmasi
function fetchPendingOrders() {
    fetch('fetch_pending_orders.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('pendingOrdersTableBody');
            tableBody.innerHTML = ''; // Bersihkan data sebelumnya

            if (data.length > 0) {
                data.forEach(order => {
                    const row = `
                       <tr>
                            <td>${order.id_pemesanan}</td>
                            <td>${order.pemesanan_kamar}</td>
                            <td>${order.id_penyewa}</td>
                            <td>${order.idKamar}</td>
                            <td>${order.uang_muka}</td>
                            <td>${order.status_uang_muka}</td>
                            <td>
                                <img src="uploads/${order.bukti_transfer}" alt="Bukti Transfer" class="img-thumbnail" style="cursor: pointer;">
                            </td>
                            <td>${order.status}</td>
                            <td>
                                <a href="generate_invoice.php?id_pemesanan=${order.id_pemesanan}&id_kamar=${order.idKamar}&id_penyewa=${order.id_penyewa}" class="btn btn-success">Konfirmasi</a>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8">Tidak ada data ditemukan</td>
                    </tr>
                `;
            }
        })
        .catch(error => console.error('Error fetching data:', error));
}
    </script>
</head>
<body>
    

    <!-- Navbar -->
    <div class="top-container">
        <div class="nav">
            <!-- Logo -->
            <div class="logo">
                <i class='bx bxl-codepen'></i>
                <a href="index.php?page=dashboard">Kost Elisa</a>
            </div>

            <!-- Links / Navigation -->
            <div class="nav-links">
                <a href="dashboard.php?page=dashboard">Beranda</a>
                <a href="dashboard.php?page=fasilitas">Fasilitas</a>
                <a href="dashboard.php?page=kamar">Kamar</a>
                <a href="dashboard.php?page=penyewa">Penyewa</a>
                <a href="dashboard.php?page=pemesanan">Pemesanan</a>
                <a href="dashboard.php?page=pembayaran">Pembayaran</a>
                <a href="dashboard.php?page=transaksi">Transaksi</a>

            </div>

            <!-- Right Section (Notification, Search, Profile) -->
            <div class="right-section">
                <i class='bx bx-bell'>
                    <span id="bell-indicator" class="badge-indicator" style="display: <?php echo $showBellIndicator; ?>;">
                        <!-- <?php echo $pendingOrders > 0 ? $pendingOrders : ''; ?> -->
                    </span> <!-- Indikator notifikasi dengan angka -->
                </i>
                <i class='bx bx-envelope'>
                    <span id="envelope-indicator" class="badge-indicator" style="display: <?php echo $newMessages > 0 ? 'block' : 'none'; ?>;">
                        <!-- <?php echo $newMessages > 0 ? $newMessages : ''; ?> -->
                    </span>
                </i>
                <!-- <i class='bx bx-search'></i> -->

                <!-- Profile Section -->
                <div class="profile">
                    <div class="info">
                        <div>
                            <a href="#"><?php echo htmlspecialchars($namaAdmin); ?></a>
                            <p>Admin</p>
                        </div>
                        <i class='bx bx-chevron-down dropdown-trigger'></i> <!-- Tambahkan kelas "dropdown-trigger" -->
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu">
                        <a href="logout.php">Logout</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

        <!-- Modal for Pending Orders -->
        <div class="modal fade custom-modal" id="pendingOrdersModal" tabindex="-1" aria-labelledby="pendingOrdersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pendingOrdersModalLabel">Daftar Pemesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Input Pencarian -->
                    <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari">
                        </div>
                        <table class="table table-bordered table-striped table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID Pemesanan</th>
                                    <th>Pemesanan Kamar</th>
                                    <th>ID Penyewa</th>
                                    <th>ID Kamar</th>
                                    <th>Uang Muka</th>
                                    <th>Status Uang Muka</th>
                                    <th>Bukti Transfer</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pendingOrdersTableBody">
                                <!-- Data akan diinject secara dinamis via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk melihat bukti transfer -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Bukti Transfer" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

<!-- Modal Pesan -->
<!-- Modal untuk Pesan -->
<div class="modal fade custom-modal" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Daftar Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tombol untuk beralih antara Belum Dibaca dan Sudah Dibaca -->
                <div class="mb-3">
                    <a href="?status=Belum Dibaca" class="btn btn-primary">Belum Dibaca</a>
                    <a href="?status=Sudah Dibaca" class="btn btn-secondary">Sudah Dibaca</a>
                </div>

                <!-- Tabel untuk menampilkan pesan -->
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Pesan</th>
                            <th>ID Penyewa</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <?php if ($status == 'Belum Dibaca'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada pesan <?php echo htmlspecialchars($status); ?>.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($message['idPesan']); ?></td>
                                    <td><?php echo htmlspecialchars($message['idPenyewa']); ?></td>
                                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($message['message']); ?></td>
                                    <?php if ($status == 'Belum Dibaca'): ?>
                                        <td>
                                            <form method="POST" action="proses_keluar.php">
                                                <input type="hidden" name="idPesan" value="<?php echo $message['idPesan']; ?>">
                                                <input type="hidden" name="idPenyewa" value="<?php echo $message['idPenyewa']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Proses</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




    <!-- Content -->
    <div class="container mt-5">
        <?php
        // Check if the 'page' parameter is set, if not, default to 'dashboard'
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
        $aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

        // Switch content based on 'page' and 'aksi' parameters
        switch ($page) {
            case "dashboard":
                include "page/dashboard/adminhome.php";
                break;

            case "fasilitas":
                if ($aksi == "") {
                    include "page/fasilitas/fasilitas.php";
                } elseif ($aksi == "tambah") {
                    include "page/fasilitas/tambah.php";
                } elseif ($aksi == "edit") {
                    include "page/fasilitas/edit.php";
                } elseif ($aksi == "hapus") {
                    include "page/fasilitas/hapus.php";
                }
                break;

            case "kamar":
                if ($aksi == "") {
                    include "page/kamar/kamar.php";
                } elseif ($aksi == "tambah") {
                    include "page/kamar/tambahkamar.php";
                } elseif ($aksi == "edit") {
                    include "page/kamar/edit.php";
                } elseif ($aksi == "hapus") {
                    include "page/kamar/hapus.php";
                }
                break;

            case "penyewa":
                if ($aksi == "") {
                    include "page/penyewa/datapenyewa.php";
                } elseif ($aksi == "tambah") {
                    include "page/penyewa/tambahpenyewa.php";
                } elseif ($aksi == "edit") {
                    include "page/penyewa/edit.php";
                } elseif ($aksi == "hapus") {
                    include "page/penyewa/hapus.php";
                }
                break;

            case "pemesanan":
                if ($aksi == "") {
                    include "page/pemesanan/pemesanan.php";
                } elseif ($aksi == "tambah") {
                    include "page/pemesanan/tambah.php";
                } elseif ($aksi == "edit") {
                    include "page/pemesanan/edit.php";
                } elseif ($aksi == "hapus") {
                    include "page/pemesanan/hapus.php";
                }
                break;

            case "pembayaran":
                if ($aksi == "") {
                    include "page/pembayaran/pembayaran.php";
                } elseif ($aksi == "tambah") {
                    include "page/pembayaran/tambah.php";
                } elseif ($aksi == "edit") {
                    include "page/pembayaran/edit.php";
                } elseif ($aksi == "hapus") {
                    include "page/pembayaran/hapus.php";
                }
                break;
            
            case "transaksi":
                    if ($aksi == "") {
                        include "page/transaksi/transaksi.php";
                    }
                    break;

            default:
                echo "<h1>404 - Page Not Found</h1>";
                break;
        }
        ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>