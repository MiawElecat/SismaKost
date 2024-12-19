    <?php
    // Koneksi database
    include('koneksi.php');

    // Ambil parameter dari URL
    $fasilitas = isset($_GET['fasilitas']) ? $_GET['fasilitas'] : '';
    $blok = isset($_GET['blok']) ? $_GET['blok'] : '';

    // Tentukan URL kembali berdasarkan kondisi
    if (empty($blok)) {
        // Jika parameter blok tidak ada, kembali ke index.php
        $returnUrl = 'index.php';
    } else {
        // Jika parameter blok ada, kembali ke daftar_kamar.php dengan fasilitas saja
        $returnUrl = "daftar_kamar.php?fasilitas=" . urlencode($fasilitas);
    }

    // Jika fasilitas belum dipilih, tampilkan pilihan fasilitas
    if (empty($fasilitas)) {
        echo "<h2>Pilih Fasilitas Kamar</h2>";
        echo "<div class='blok-buttons'>";
        echo "<a href='daftar_kamar.php?fasilitas=kamar mandi dalam' class='blok-btn'>Kamar Mandi Dalam</a>";
        echo "<a href='daftar_kamar.php?fasilitas=kamar mandi luar' class='blok-btn'>Kamar Mandi Luar</a>";
        echo "</div>";
    } elseif (empty($blok)) {
        // Jika fasilitas sudah dipilih tetapi blok belum, tampilkan pilihan blok
        echo "<h2>Pilih Blok Kost untuk Fasilitas $fasilitas</h2>";
        echo "<div class='blok-container'>";
        echo "<div class='blok-row'>";
        // echo "<span class='blok-label'>Blok A</span>";
        // echo "<span class='blok-label'>Blok B</span>";
        // echo "<span class='blok-label'>Blok C</span>";
        echo "</div>";
        echo "<div class='blok-buttons'>";
        $blokOptions = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($blokOptions as $blok) {
            echo "<a href='daftar_kamar.php?fasilitas=$fasilitas&blok=$blok' class='blok-btn'>Blok $blok</a>";
        }
        echo "</div>";
        echo "</div>";
        
    } else {
        // Jika fasilitas dan blok sudah dipilih, tampilkan daftar kamar yang sesuai
        $sql = "SELECT k.idKamar, k.namaKamar, f.namaFasilitas, f.biayaTambahan, b.namaBlok, k.harga, k.status, k.foto
                FROM kamar k
                JOIN kamar_fasilitas kf ON k.idKamar = kf.idKamar
                JOIN fasilitas f ON kf.idFasilitas = f.idFasilitas
                JOIN blok b ON k.idBlok = b.idBlok
                WHERE f.namaFasilitas = '$fasilitas'
                AND b.namaBlok = '$blok'
                ORDER BY k.idKamar";
        
        $result = mysqli_query($koneksi, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<h2>Daftar Kamar di Blok $blok dengan Fasilitas $fasilitas</h2>";
            echo "<div class='room-container'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='kamar-item'>";
                echo "<h4>" . $row['namaKamar'] . "</h4>";
                if (!empty($row['foto'])) {
                    echo "<img src='path/to/folder/" . $row['foto'] . "' alt='" . $row['namaKamar'] . "' class='room-photo' />";
                } else {
                    echo "<img src='img/Kamar.jpg' alt='Foto tidak tersedia' class='room-photo' />";
                }
                echo "<p>Harga: Rp. " . number_format($row['harga'], 0, ',', '.') . "</p>";
                echo "<p>Status: " . $row['status'] . "</p>";
                echo "<a href='detail_kamar.php?idKamar=" . $row['idKamar'] . "&fasilitas=" . urlencode($fasilitas) . "&blok=" . urlencode($blok) . "&returnUrl=" . urlencode($_SERVER['REQUEST_URI']) . "' class='btn-detail'><i class='fas fa-info-circle'></i> Detail Kamar</a>";
                echo "<a href='pemesanan.php?idKamar=" . $row['idKamar'] . "&returnUrl=" . urlencode($_SERVER['REQUEST_URI']) . "' class='pemesanan-btn'><i class='fas fa-shopping-cart'></i> Pesan Kamar</a>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "Tidak ada kamar dengan fasilitas $fasilitas di blok $blok.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pilih Blok</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            /* Resetting some default styles */
            body, h2 {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            body {
                background-color: #f4f4f9;
                color: #333;
                line-height: 1.6;
                padding: 20px;
                position: relative;
            }

            h2 {
                text-align: center;
                margin-bottom: 30px;
                color: #4CAF50;
                font-size: 30px;
                font-weight: bold;
            }

            /* Custom styling for back button */
            #backButton {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 7px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            }
            #backButton:hover {
                background-color: #0056b3;
            }

/* Kontainer Utama */
.blok-container {
    display: grid;
    grid-template-rows: auto auto;
    gap: 20px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: auto;
    text-align: center;
}

/* Label Baris Blok */
.blok-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Tiga kolom */
    gap: 10px;
    font-weight: bold;
    color: #333;
    text-transform: uppercase;
}

/* Tombol Blok */
.blok-buttons {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Tiga kolom untuk peta */
    gap: 20px;
}

.blok-btn {
    padding: 15px 25px;
    font-size: 18px;
    text-decoration: none;
    background-color: #4CAF50;
    color: white;
    border-radius: 8px;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 2px solid #4CAF50;
}

.blok-btn:hover {
    background-color: #45a049;
    transform: scale(1.05);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
    border-color: #45a049;
}

/* Label Posisi Blok */
.blok-label {
    text-align: center;
    font-size: 16px;
    color: #555;
    margin-bottom: 5px;
}



            /* Container for room items */
            .room-container {
                display: grid;
                grid-template-columns: repeat(3, 1fr); /* Selalu 3 kolom */
                gap: 20px;
                justify-items: center;
                padding: 10px; /* Mengatur jarak atas, bawah, kanan, dan kiri */
                margin-left: auto; /* Membuat kontainer terpusat */
                margin-right: auto;
                max-width: 1100px; /* Membatasi lebar maksimal agar grid tidak terlalu melebar */
            }

            /* Styling for each room item */
            .kamar-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 15px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 300px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .kamar-item:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }

            .room-name {
                margin: 10px 0;
                color: #4CAF50;
                font-size: 20px;
                font-weight: bold;
            }
            .room-photo {
                width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin-bottom: 10px;
            }

            .price {
                font-size: 18px;
                color: #FF5722;
                font-weight: bold;
                margin: 5px 0;
            }

            .status {
                font-size: 14px;
                color: #888;
                margin: 5px 0;
            }

            /* Styling for the reservasi button */
            .btn-reservasi {
                display: inline-block;
                background-color: #FF5722;
                color: white;
                padding: 10px 20px;
                border-radius: 8px;
                text-decoration: none;
                margin-top: 10px;
                transition: background-color 0.3s ease;
            }

            .btn-reservasi:hover {
                background-color: #FF3D00;
            }
        </style>
        <script>
            // Show the back button only if the referrer exists and is not empty
            document.addEventListener("DOMContentLoaded", function() {
                const backButton = document.getElementById("backButton");
                if (document.referrer) {
                    backButton.style.display = "block";
                }
            });
        </script>
    </head>
    <body>
    <a href="<?php echo $returnUrl; ?>" id="backButton">Kembali</a>
    </body>
    </html>
