<?php
  $idFasilitas = $_GET['idFasilitas'];

  $koneksi->query("DELETE FROM fasilitas where idFasilitas='$idFasilitas'");
?>

<script type="text/javascript">
    window.location.href="?page=fasilitas";
</script>