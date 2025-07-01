<?php

include "../config.php";
$id = $_POST['id'];
$jenisK = $_POST['jenis_kendaraan'];
$harga = $_POST['harga'];

$query = mysqli_query($koneksi,"UPDATE jenisKendaraan SET jenis_kendaraan='$jenisK',harga='$harga' WHERE id_jenisKendaraan='$id'");

if($query){
    header("location:../../admin/jenis_kendaraan.php");

}else{
    header("location:../../admin/jenis_kendaraan.php");
}
?>