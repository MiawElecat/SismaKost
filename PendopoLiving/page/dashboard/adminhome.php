<?php
$tahunDipilih = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Query untuk mengambil data transaksi berdasarkan tahun yang dipilih
$sql = "SELECT tanggal_transaksi AS tanggal, jumlah_transaksi AS harga_sewa 
        FROM transaksi 
        WHERE YEAR(tanggal_transaksi) = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $tahunDipilih);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Hitung penyewa per bulan dan pendapatan per bulan
$penyewa_per_bulan = [];
$pendapatan_per_bulan = [];
foreach ($data as $row) {
    $date = strtotime($row['tanggal']);
    $month = date('M', $date); // Bulan dalam format tiga huruf, seperti 'Jan', 'Feb', dll.
    $harga_sewa = floatval($row['harga_sewa']);

    if (!isset($penyewa_per_bulan[$month])) {
        $penyewa_per_bulan[$month] = 0;
        $pendapatan_per_bulan[$month] = 0;
    }

    // Hitung penyewa per bulan
    $penyewa_per_bulan[$month]++;

    // Hitung pendapatan per bulan
    $pendapatan_per_bulan[$month] += $harga_sewa;
}

$labels = array_keys($penyewa_per_bulan);
$values_penyewa = array_values($penyewa_per_bulan);
$values_pendapatan = array_values($pendapatan_per_bulan);

// Rata-rata pendapatan per bulan
$total_pendapatan = array_sum($pendapatan_per_bulan);
$rata_rata_per_bulan = count($pendapatan_per_bulan) > 0 
    ? $total_pendapatan / count($pendapatan_per_bulan) 
    : 0;

$penyewa_per_bulan = [
    'labels' => $labels,
    'values_penyewa' => $values_penyewa,
    'values_pendapatan' => $values_pendapatan,
    'rata_rata_per_bulan' => $rata_rata_per_bulan
];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home - Kost Elisa</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style2.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Kontainer Atas -->
    <div class="top-container">
        <div class="status">
            <div class="header">
                <h4 id="big">Data Analisis Power BI</h4>
            </div>
            <div class="item">
            <iframe title="kostelisa" width="1256" height="486" src="https://app.powerbi.com/view?r=eyJrIjoiZTIzZDM3OGUtMWQ2NS00Y2U2LThkNzgtODAyNmRmMGNjNDYxIiwidCI6IjUyNjNjYzgxLTU5MTItNDJjNC1hYmMxLWQwZjFiNjY4YjUzMCIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div>

    <!-- Kontainer Bawah -->
    <div class="bottom-container">
        <div class="prog-status">
            <div class="header">
                <h5>Pendapatan Tahunan</h5>
                <div class="tabs">
                    <a href="?tahun=2024" class="<?= $tahunDipilih == 2024 ? 'active' : '' ?>">2024</a>
                    <a href="?tahun=2023" class="<?= $tahunDipilih == 2023 ? 'active' : '' ?>">2023</a>
                    <a href="?tahun=2022" class="<?= $tahunDipilih == 2022 ? 'active' : '' ?>">2022</a>
                    <a href="?tahun=2021" class="<?= $tahunDipilih == 2021 ? 'active' : '' ?>">2021</a>
                </div>
            </div>
            <div class="details">
                <div class="item">
                    <h2>Rp. <?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                    <p>Total Pendapatan Tahun <?= $tahunDipilih ?></p>
                </div>
                <div class="separator"></div>
                <div class="item">
                    <h2>Rp. <?= number_format($rata_rata_per_bulan, 0, ',', '.') ?></h2>
                    <p>Rata-Rata per Bulan (<?= $tahunDipilih ?>)</p>
                </div>
            </div>

            <!-- Grafik Pendapatan -->
            <canvas id="prog-chart"></canvas>
        </div>

        <!-- Kontainer Penyewa per Bulan -->
        <div class="prog-status">
            <div class="header">
                <h5>Penyewa per Bulan</h5>
            </div>
            <div class="details">
                <!-- Grafik Penyewa per Bulan -->
                <canvas id="activity-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Skrip JS -->
    <script>
        const penyewaData = <?= json_encode($penyewa_per_bulan); ?>;

        // Grafik untuk Aktivitas Bulanan (Penyewa per Bulan)
        const ctxActivity = document.getElementById('activity-chart').getContext('2d');
        new Chart(ctxActivity, {
            type: 'bar',
            data: {
                labels: penyewaData.labels, // ['Jan', 'Feb', ...]
                datasets: [{
                    label: 'Penyewa per Bulan',
                    data: penyewaData.values_penyewa, // [10, 20, ...]
                    backgroundColor: '#60a5fa',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        border: { display: true },
                        grid: { display: true, color: '#1e293b' }
                    },
                    y: {
                        ticks: { display: true }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });

        // Grafik Pendapatan per Bulan
        const ctxPendapatan = document.getElementById('prog-chart').getContext('2d');
        new Chart(ctxPendapatan, {
            type: 'bar',
            data: {
                labels: penyewaData.labels, 
                datasets: [{
                    label: 'Pendapatan per Bulan (Rp)',
                    data: penyewaData.values_pendapatan,
                    backgroundColor: '#1e293b',
                    borderWidth: 3,
                    borderRadius: 6,
                    hoverBackgroundColor: '#60a5fa'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: { color: '#1e293b' }
                    },
                    y: {
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString(); }
                        }
                    }
                },
                plugins: { legend: { display: false } },
                animation: { duration: 1000 }
            }
        });
    </script>

</body>
</html>
