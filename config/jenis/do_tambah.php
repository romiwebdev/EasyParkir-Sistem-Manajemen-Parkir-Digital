<?php
include "../config.php";

$jenisK = $_POST['jenis_kendaraan'];
$harga = $_POST['harga'];
$kapasitas = $_POST['kapasitas'] ?? 0;

$query = mysqli_query($koneksi, "INSERT INTO jenisKendaraan (jenis_kendaraan, harga, kapasitas_slot)
                                 VALUES ('$jenisK', '$harga', '$kapasitas')");

if($query){
    header("location:../../admin/jenis_kendaraan.php?success=1");
} else {
    header("location:../../admin/jenis_kendaraan.php?error=1");
}
?>
