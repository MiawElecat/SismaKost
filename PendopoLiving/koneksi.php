<?php

$host = "localhost";
$user = "root";
$password = "11223344";
$db= "kostkamar";

$koneksi = mysqli_connect( $host,$user,$password,$db );

if (!$koneksi) {
   die("Koneksi Gagal".mysqli_connect_error());
 
}
?>