<?php
function getpendopo_living() {
    $koneksi = new mysqli("localhost", "root", "", "pendopo_living");

    if ($koneksi->connect_error) {
        die("Koneksi gagal: " . $koneksi->connect_error);
    }

    $sql = $koneksi->query("SELECT * FROM pendopo_living");

    if ($sql === false) {
        die("Query gagal: " . $koneksi->error);
    }

    $lowongan = [];
    while ($row = $sql->fetch_assoc()) {
        $lowongan[] = $row;
    }

    $koneksi->close();

    return $lowongan;
}
?>
