<?php
  $idKamar = $_GET['idPemesanan'];

  $koneksi->query("DELETE FROM pemesanan where id_pemesanan='$id_Pemesanan'");
?>

<script type="text/javascript">
    window.location.href="?page=pemesanan";
</script>