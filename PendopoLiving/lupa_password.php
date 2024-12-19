<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'koneksi.php';

date_default_timezone_set('Asia/Jakarta'); 

$message = ''; // Variabel untuk menyimpan pesan

// Step 1: Kirim Token Reset Password
if (isset($_POST['submit_email'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    // Mengecek apakah email milik admin atau penyewa
    $isAdmin = false;
    $sql = $koneksi->query("SELECT * FROM admin WHERE Email='$email'");
    $data = $sql->fetch_assoc();

    if (!$data) {
        $sql = $koneksi->query("SELECT * FROM penyewa WHERE email='$email'");
        $data = $sql->fetch_assoc();
    } else {
        $isAdmin = true;
    }

    if ($data) {
        $token = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Update token pada tabel yang sesuai
        if ($isAdmin) {
            $koneksi->query("UPDATE admin SET reset_token='$token', token_expiry='$expiry' WHERE Email='$email'");
        } else {
            $koneksi->query("UPDATE penyewa SET reset_token='$token', token_expiry='$expiry' WHERE email='$email'");
        }

        // Mengirim email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kakapatria22@gmail.com';
            $mail->Password = 'ofse zhdu vtle dapq'; // Ganti dengan password aplikasi yang benar
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('youremail@example.com', 'Pendopo Living');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Kode Reset Password Anda';

            $mail->Body = "Berikut adalah kode untuk mereset password Anda: <strong>$token</strong>. Kode ini berlaku selama 15 menit.";

            $mail->send();
            $message = "Email berisi kode reset password telah dikirim. Silakan cek email Anda.";
            $_SESSION['isTokenSent'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['isAdmin'] = $isAdmin;
        } catch (Exception $e) {
            $message = "Gagal mengirim email. Error: " . $mail->ErrorInfo;
        }
    } else {
        $message = "Email tidak ditemukan.";
    }
}

// Step 2: Verifikasi Token
elseif (isset($_POST['verify_token'])) {
    $token = mysqli_real_escape_string($koneksi, $_POST['token']);
    $isAdmin = $_SESSION['isAdmin'] ?? false;
    
    // Cek token di tabel yang sesuai
    if ($isAdmin) {
        $sql = $koneksi->query("SELECT * FROM admin WHERE reset_token='$token' AND token_expiry > NOW()");
    } else {
        $sql = $koneksi->query("SELECT * FROM penyewa WHERE reset_token='$token' AND token_expiry > NOW()");
    }

    $data = $sql->fetch_assoc();

    if ($data) {
        $_SESSION['isTokenVerified'] = true;
        $_SESSION['reset_token'] = $token;
        $message = "Token valid. Silakan masukkan password baru Anda di bawah.";
    } else {
        $message = "Token tidak valid atau telah kedaluwarsa.";
    }
}

// Step 3: Reset Password Baru
elseif (isset($_POST['reset_password']) && isset($_SESSION['isTokenVerified']) && $_SESSION['isTokenVerified']) {
    $password = mysqli_real_escape_string($koneksi, $_POST['new_password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $isAdmin = $_SESSION['isAdmin'] ?? false;

    // Update password pada tabel yang sesuai
    if ($isAdmin) {
        $koneksi->query("UPDATE admin SET Password='$hashed_password', reset_token=NULL, token_expiry=NULL WHERE reset_token='{$_SESSION['reset_token']}'");
    } else {
        $koneksi->query("UPDATE penyewa SET password='$hashed_password', reset_token=NULL, token_expiry=NULL WHERE reset_token='{$_SESSION['reset_token']}'");
    }
    
    $_SESSION['message'] = "Kata sandi berhasil direset. Silakan login dengan password baru Anda.";
    session_destroy();

    echo "<script>
        alert('Password berhasil direset. Silakan login dengan password baru.');
        window.location.href = 'login.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <?php if (!isset($_SESSION['isTokenSent']) && !isset($_SESSION['isTokenVerified'])): ?>
            <!-- Form untuk Memasukkan Email -->
            <form method="post">
                <h1>Lupa Password</h1>
                <div class="input-box">
                    <input type="email" placeholder="Email" name="email" required>
                </div>
                <button type="submit" name="submit_email" class="btn">Kirim Kode Reset</button>
                <?php if ($message): ?>
                    <p style="color: red;"><?php echo $message; ?></p>
                <?php endif; ?>
            </form>

        <?php elseif (isset($_SESSION['isTokenSent']) && !isset($_SESSION['isTokenVerified'])): ?>
            <!-- Form untuk Memasukkan Token -->
            <form method="post">
                <h1>Verifikasi Token</h1>
                <div class="input-box">
                    <input type="text" placeholder="Masukkan Kode" name="token" required>
                </div>
                <button type="submit" name="verify_token" class="btn">Verifikasi Kode</button>
                <?php if ($message): ?>
                    <p style="color: green;"><?php echo $message; ?></p>
                <?php endif; ?>
            </form>

        <?php elseif (isset($_SESSION['isTokenVerified'])): ?>
            <!-- Form untuk Mengatur Ulang Password Baru -->
            <form method="post">
                <h1>Reset Password</h1>
                <div class="input-box">
                    <input type="password" placeholder="Password Baru" name="new_password" required>
                </div>
                <button type="submit" name="reset_password" class="btn">Reset Password</button>
                <?php if ($message): ?>
                    <p style="color: green;"><?php echo $message; ?></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
