<?php
session_start();
include "../config/config.php";

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM kendaraan_masuk WHERE id='$id'");
echo "<script>alert('Data berhasil dihapus'); window.location='parkir_aktif.php';</script>";
