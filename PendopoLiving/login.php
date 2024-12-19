<?php
session_start();
include 'koneksi.php';

$error = ""; // Inisialisasi variabel error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_type = $_POST['user_type'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Login Admin
    if ($user_type === 'admin') {
        $query = "SELECT * FROM admin WHERE Email = ?";
        $stmt = $koneksi->prepare($query);
        
        if (!$stmt) {
            die("Error dalam menyiapkan query: " . $koneksi->error);
        }
        
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $user['Password'])) {
                $_SESSION['user_type'] = 'admin';
                $_SESSION['email'] = $email;
                $_SESSION['idAdmin'] = $user['idAdmin'];
                $_SESSION['namaAdmin'] = $user['namaAdmin'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Login Admin gagal! Periksa password.";
            }
        } else {
            $error = "Login Admin Gagal! Email tidak ditemukan.";
        }
    }

    // Proses login untuk penyewa
    elseif ($user_type === 'penyewa') {
        $query = "SELECT * FROM penyewa WHERE email = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_type'] = 'penyewa';
                $_SESSION['email'] = $email;
                $_SESSION['idPenyewa'] = $user['idPenyewa']; // Asumsikan $data adalah array hasil login
                $_SESSION['namaPenyewa'] = $user['namaPenyewa'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Login Penyewa gagal! Password salah.";
            }
        } else {
            $error = "Login Penyewa gagal! Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Custom styling for back button */
        #backButton {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            display: none;
        }
        
        #backButton:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div id="roleWrapper">
    <!-- Tombol Kembali -->
    <button id="backButton" onclick="goBackToRoleSelection()" 
        style="position: absolute; top: 10px; left: 10px; padding: 10px 15px; border: none; 
               background-color: #007bff; color: white; font-size: 14px; font-weight: bold; 
               border-radius: 20px; cursor: pointer; <?php echo !empty($error) ? 'display: inline;' : 'display: none;'; ?>">
    Kembali
    </button>


    <div id="roleContainer">
        <h2 id="roleTitle" style="<?php echo !empty($error) ? 'display: none;' : ''; ?>">Lanjutkan Aktivitas sebagai</h2>

        <div id="roleButtons" <?php echo !empty($error) ? 'style="display: none;"' : ''; ?>>
            <div class="role-button-container">
                <div class="role-content">
                    <img src="https://static-asset.papikost.com/images/general/pencari-kost.svg" alt="Pencari Kost" width="100">
                    <div class="role-info">
                        <h3>Pencari Kost</h3>
                        <p>Cari tempat kost yang nyaman dan sesuai dengan kebutuhan Anda.</p>
                        <button onclick="selectUserType('penyewa')">Pencari Kost</button>
                    </div>
                </div>
            </div>
            <div class="role-button-container">
                <div class="role-content">
                    <img src="https://static-asset.papikost.com/images/general/pemilik-kost.svg" alt="Pemilik Kost" width="100">
                    <div class="role-info">
                        <h3>Pemilik Kost</h3>
                        <p>Kelola kost Anda dan temukan penyewa yang sesuai.</p>
                        <button onclick="selectUserType('admin')">Pemilik Kost</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Form, will remain visible if error -->
    <form id="loginForm" method="post" action="" style="<?php echo !empty($error) ? 'display: block;' : 'display: none;'; ?>">
        <input type="hidden" name="user_type" id="userType" value="<?php echo isset($user_type) ? htmlspecialchars($user_type) : ''; ?>">
        
        <div class="input-container">
            <label>Email:</label>
            <input type="text" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>
        
        <div class="input-container">
            <label>Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <div class="show-password-container">
            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()"> <label for="showPassword">Tampilkan Password</label>
        </div>
        
        <button type="submit">Login</button>

        <div class="links">
            <a href="register.php" style="<?php echo isset($user_type) && $user_type === 'penyewa' ? 'display: block;' : 'display: none;'; ?>" id="daftarLink">Daftar</a>
            <a href="lupa_password.php">Lupa Password?</a>
        </div>
    </form>
    
    <?php
    if (!empty($error)) {
        echo "<p id='errorMessage' style='color:red; text-align:center; margin-top:10px;'>$error</p>";
    }
    ?>
</div>

<script>
function selectUserType(type) {
    document.getElementById('userType').value = type;

    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('roleButtons').style.display = 'none';
    document.getElementById('roleTitle').style.display = 'none';
    document.getElementById('backButton').style.display = 'inline';

    if (type === 'penyewa') {
        document.getElementById('daftarLink').style.display = 'block'; 
    } else {
        document.getElementById('daftarLink').style.display = 'none'; 
    }
}

function togglePasswordVisibility() {
    var passwordField = document.getElementById('password');
    var showPasswordCheckbox = document.getElementById('showPassword');
    passwordField.type = showPasswordCheckbox.checked ? 'text' : 'password';
}

function goBackToRoleSelection() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('roleButtons').style.display = 'flex';
    document.getElementById('roleTitle').style.display = 'block';
    document.getElementById('backButton').style.display = 'none';

    var errorMessage = document.getElementById('errorMessage');
    if (errorMessage) {
        errorMessage.style.display = 'none';
    }
}

</script>

</body>
</html>