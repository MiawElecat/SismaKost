
<div class="panel panel-default">
    <div class="panel-heading">
        Tambah Data Fasilitas
    </div>

<div class="panel-body">
    <div class="row">
         <div class="col-md-12">
            <!-- <h3>Basic Form Examples</h3> -->
             <form method="POST">
                <div class="form-group">
                    <label>Nama Fasilitas</label>
                    <input class="form-control" name="namaFasilitas"/>
                </div>

                <div class="form-group">
                    <label>Biaya Tambahan</label>
                    <input type="number" class="form-control" name="biayaTambahan"/>
                </div>




                <div>
                <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
                </div>
              


                </div>
            </form>
            </div>
</div>
</div>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['simpan'])) {

        $namaFasilitas = $_POST['namaFasilitas'];
        $biayaTambahan = $_POST['biayaTambahan'];

        $sql = $koneksi->query("INSERT INTO fasilitas (namaFasilitas, biayaTambahan) 
        VALUES ('$namaFasilitas', '$biayaTambahan')");

        if ($sql) {
            echo '
            <script type="text/javascript">
                alert("Data berhasil disimpan");
                window.location.href="?page=fasilitas";
            </script>';
        }
    }
}
?>

               