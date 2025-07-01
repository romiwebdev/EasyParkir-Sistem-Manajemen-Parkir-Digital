<?php

session_start();

if(!isset($_SESSION['login'])){
    header("location:../login.php");
    exit;
}

include "../config.php";
$id = $_GET['id'];

$query = mysqli_query($koneksi,"DELETE FROM jenisKendaraan WHERE id_jenisKendaraan='$id'");

if($query){
    header("location:../../admin/jenis_kendaraan.php");

}else{
    header("location:../../admin/jenis_kendaraan.php");
}
?>