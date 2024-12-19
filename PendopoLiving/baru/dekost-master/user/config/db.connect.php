<?php

$mysql_server = "localhost";
$username = "root"; // root
$password = ""; // ""
$database = "dekost";

try {
    $conn = mysqli_connect($mysql_server, $username, $password, $database);
} catch (Exception $e) {
    echo ("Terjadi error: $e");
}