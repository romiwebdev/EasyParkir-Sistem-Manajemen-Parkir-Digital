<?php
$koneksi = mysqli_connect(
    "sql12.freesqldatabase.com",
    "sql12787385",
    "nhx4uTBHKu",
    "sql12787385",
    3306
);

if (mysqli_connect_errno()){
    die("Koneksi database gagal: " . mysqli_connect_error());
}

function rupiah($angka)
{
    return number_format($angka, 0, ',', '.');
}
