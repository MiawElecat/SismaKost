<?php
  $id = $_GET['id'];

  $koneksi->query("DELETE FROM datapenyewa where id='$id'");
?>

<script type="text/javascript">
    window.location.href="?page=datapenyewa";
</script>