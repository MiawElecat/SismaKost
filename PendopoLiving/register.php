<?php
session_start();
require 'koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaPenyewa = mysqli_real_escape_string($koneksi, $_POST['namaPenyewa']);
    $noTelepon = mysqli_real_escape_string($koneksi, $_POST['noTelepon']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Cek apakah password dan konfirmasi password sama
    if ($password !== $confirm_password) {
        $message = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Masukkan data ke dalam database tanpa fotoJaminan
        $sql = "INSERT INTO penyewa (namaPenyewa, noTelepon, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssss", $namaPenyewa, $noTelepon, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $message = "Pendaftaran berhasil. Silakan login.";
        } else {
            $message = "Pendaftaran gagal: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // JavaScript untuk otomatis menambahkan '08' pada input nomor telepon
        function autoFillPhone() {
            var phoneInput = document.getElementById('noTelepon');
            var phoneValue = phoneInput.value.replace(/\D/g, ''); // Hapus semua karakter non-digit
            
            // Jika nomor tidak dimulai dengan 08, tambahkan 08
            if (!phoneValue.startsWith('08')) {
                phoneValue = '08' + phoneValue.substring(2);
            }
            
            phoneInput.value = phoneValue; // Atur nilai input dengan nomor yang valid
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <form method="post">
            <h1>Daftar</h1>

            <div class="input-box">
                <input type="text" name="namaPenyewa" placeholder="Nama Lengkap" required>
            </div>

            <div class="input-box">
                <input type="tel" id="noTelepon" name="noTelepon" placeholder="Nomor Telepon (08xxxx)" required 
                       oninput="autoFillPhone()">
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>

            <button type="submit" class="btn">Daftar</button>

            <?php if ($message): ?>
                <p style="color: green;"><?php echo $message; ?></p>
            <?php endif; ?>
        </form>

        <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</body>
</html>
