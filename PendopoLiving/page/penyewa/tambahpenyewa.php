<?php
include"koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penyewa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf2f8;
            color: #4a4a4a;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #6b21a8;
        }
        form label {
            display: block;
            margin-top: 15px;
            color: #4a4a4a;
        }
        form input, form textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        form input[type="submit"] {
            width: 100%;
            background-color: #9333ea;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        form input[type="submit"]:hover {
            background-color: #7e22ce;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Penyewa Baru</h2>
        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
            <label>ID Kamar:</label>
            <input type="text" name="idKamar" required>

            <label>Nama Penyewa:</label>
            <input type="text" name="namaPenyewa" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Nomor Telepon:</label>
            <input type="text" name="noTelepon" required>

            <label>Foto Jaminan:</label>
            <input type="file" name="fotoJaminan" accept="image/*" required>

            <input type="submit" value="Simpan">
        </form>
    </div>
</body>
</html>
