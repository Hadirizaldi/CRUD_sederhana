<?php 
// koneksi ke database
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'phpdasar';

$conn = mysqli_connect($hostname, $username, $password, $database);

if(!$conn) {
    die("Gagal melakukan koneksi" + mysqli_connect_error());
}

?>