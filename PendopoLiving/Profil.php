<?php
// Mulai sesi dan cek apakah pengguna sudah login
session_start();
require 'koneksi.php';

$idPenyewa = $_SESSION['idPenyewa'];

// Ambil data penyewa dari database
$query = "SELECT * FROM penyewa WHERE idPenyewa = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $idPenyewa);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    // Jika tidak ada data ditemukan, tampilkan pesan kesalahan
    echo "Data penyewa tidak ditemukan.";
    exit();
}

// Inisialisasi variabel untuk perubahan data
$updated = false;

// Proses upload foto jaminan jika ada
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cek apakah ada perubahan pada nama, noTelepon, atau email
    if (isset($_POST['namaPenyewa']) && $_POST['namaPenyewa'] !== $data['namaPenyewa']) {
        $namaPenyewa = $_POST['namaPenyewa'];
        $query = "UPDATE penyewa SET namaPenyewa = ? WHERE idPenyewa = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("si", $namaPenyewa, $idPenyewa);
        $stmt->execute();
        $updated = true;
    }

    if (isset($_POST['noTelepon']) && $_POST['noTelepon'] !== $data['noTelepon']) {
        $noTelepon = $_POST['noTelepon'];
        $query = "UPDATE penyewa SET noTelepon = ? WHERE idPenyewa = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("si", $noTelepon, $idPenyewa);
        $stmt->execute();
        $updated = true;
    }

    if (isset($_POST['email']) && $_POST['email'] !== $data['email']) {
        $email = $_POST['email'];
        $query = "UPDATE penyewa SET email = ? WHERE idPenyewa = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("si", $email, $idPenyewa);
        $stmt->execute();
        $updated = true;
    }

    // Proses foto jaminan
    if (isset($_FILES['fotoJaminan']) && $_FILES['fotoJaminan']['error'] == 0) {
        $fotoJaminan = $_FILES['fotoJaminan'];
        $targetDir = "uploads/foto_jaminan/"; // Pastikan folder 'uploads/foto_jaminan' ada
        $targetFile = $targetDir . basename($fotoJaminan["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($fotoJaminan["tmp_name"], $targetFile)) {
                // Update foto jaminan di database
                $query = "UPDATE penyewa SET fotoJaminan = ? WHERE idPenyewa = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("si", $targetFile, $idPenyewa);
                $stmt->execute();
                $updated = true;
            } else {
                echo "Terjadi kesalahan saat mengunggah foto jaminan.";
                exit();
            }
        } else {
            echo "Format file tidak diperbolehkan. Harap unggah file dengan format JPG, JPEG, PNG, atau GIF.";
            exit();
        }
    }

    // Jika ada perubahan, refresh halaman
    if ($updated) {
        $_SESSION['update_success'] = "Profil Anda telah berhasil diperbarui!";
        header("Location: profil.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Penyewa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .profil-wrapper {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            display: flex;
            max-width: 1000px;
            width: 100%;
            flex-wrap: wrap;
            background: linear-gradient(145deg, #f0f4f7, #e4e8f1); /* Gradien latar belakang */
        }

        .image-container {
            width: 150px;
            height: 150px;
            background-color: #4CAF50; /* Ganti warna latar belakang gambar */
            border-radius: 50%;
            margin-right: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 50px;
            color: white;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .image-container i {
            font-size: 50px;
        }

        .form-container {
            flex: 1;
            min-width: 250px;
            max-width: 650px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 1px;
            font-size: 16px;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fff;
        }

        input[type="file"] {
            height: 120px;
            background-color: #f0f0f0;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); /* Tambahkan bayangan */
        }

        input[type="file"]::before {
            content: '+';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 40px;
            color: #777;
            pointer-events: none;
        }

        input[type="file"]:hover {
            background-color: #e6e6e6;
            border-color: #4CAF50; /* Perubahan warna border saat hover */
        }

        button {
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            max-width: 200px;
            align-self: flex-start;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background-color: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .modal-content button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .modal-content button:hover {
            background-color: #45a049;
        }

        @media screen and (max-width: 768px) {
            .profil-wrapper {
                flex-direction: column;
                align-items: center;
            }

            .image-container {
                margin-bottom: 20px;
            }

            .form-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profil-wrapper">
            <!-- Gambar Profil -->
            <div class="image-container">
                <i class="fas fa-user"></i>
            </div>

            <!-- Form Profil -->
            <div class="form-container">
                <h2>Profil Penyewa</h2>

                <!-- Form untuk edit data penyewa -->
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <label for="namaPenyewa">Nama Penyewa:</label>
                    <input type="text" name="namaPenyewa" value="<?php echo htmlspecialchars($data['namaPenyewa']); ?>" required>

                    <label for="noTelepon">Nomor Telepon:</label>
                    <input type="text" name="noTelepon" value="<?php echo htmlspecialchars($data['noTelepon']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>

                    <label for="fotoJaminan">Foto Jaminan:</label>
                    <p style="font-size: 14px; color: #777; margin-bottom: -10px;">* Catatan: Ukuran foto maksimal 3MB</p>
                    <input type="file" name="fotoJaminan" accept="image/*">

                    <button type="submit">Update Profil</button>
                </form>
            </div>
        </div>
    </div>

      <!-- Modal Pop-Up -->
      <div id="successModal" class="modal">
        <div class="modal-content">
            <p>Profil Anda telah berhasil diperbarui!</p>
            <button id="closeModal">Tutup</button>
        </div>
    </div>

    <script>
        // JavaScript untuk menampilkan modal jika berhasil
        <?php if (isset($_SESSION['update_success'])): ?>
            // Tampilkan modal pop-up
            document.getElementById('successModal').style.display = 'block';

            // Hapus pesan keberhasilan setelah pop-up ditampilkan
            <?php unset($_SESSION['update_success']); ?>
        <?php endif; ?>

        // Tutup modal ketika tombol "Tutup" diklik
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('successModal').style.display = 'none';
        });

        // Tutup modal jika area luar modal diklik
        window.onclick = function(event) {
            if (event.target == document.getElementById('successModal')) {
                document.getElementById('successModal').style.display = 'none';
            }
        };
    </script>
</body>
</html>
