<?php
$idKamar = $_GET['idKamar'];

// Menghapus data di tabel kamar_fasilitas yang bergantung pada idKamar
$koneksi->query("DELETE FROM kamar_fasilitas WHERE idKamar='$idKamar'");

// Menghapus data kamar setelah data terkait dihapus
$koneksi->query("DELETE FROM kamar WHERE idKamar='$idKamar'");

?>

<script type="text/javascript">
    window.location.href="?page=kamar";
</script>
