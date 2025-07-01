<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kendaraan']);
    $jenis = $_POST['jenis_kendaraan'];

    $query = mysqli_query($koneksi, "UPDATE kendaraan_masuk SET nama_kendaraan='$nama', id_jenisKendaraan='$jenis' WHERE id='$id'");
    
    if ($query) {
        echo "<script>alert('Data berhasil diubah'); window.location='parkir_aktif.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data: " . mysqli_error($koneksi) . "'); window.location='parkir_aktif.php';</script>";
    }
    exit;
}

header("location:parkir_aktif.php");
?>