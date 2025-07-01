<?php
date_default_timezone_set("Asia/Jakarta");
include "../config/config.php";

$filter = '';
$label = 'Semua Data';
if (isset($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $filter = "WHERE DATE(waktu_keluar) = '$tanggal'";
    $label = "Tanggal: $tanggal";
} elseif (isset($_GET['bulan'])) {
    $bulan = $_GET['bulan'];
    $filter = "WHERE MONTH(waktu_keluar) = '$bulan'";
    $label = "Bulan: $bulan";
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_parkir_".date('Ymd_His').".xls");
?>

<h3>Laporan Kendaraan Keluar (<?= $label ?>)</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Unik</th>
            <th>Nama Kendaraan</th>
            <th>Jenis</th>
            <th>Tanggal Keluar</th>
            <th>Durasi (hari)</th>
            <th>Biaya</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $q = mysqli_query($koneksi, "
            SELECT rk.*, jk.jenis_kendaraan 
            FROM riwayat_keluar rk 
            JOIN jenisKendaraan jk ON rk.id_jenisKendaraan = jk.id_jenisKendaraan
            $filter ORDER BY waktu_keluar DESC
        ");
        $no = 1;
        $total = 0;
        while ($d = mysqli_fetch_assoc($q)):
            $total += $d['biaya'];
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $d['kode_unik'] ?></td>
            <td><?= $d['nama_kendaraan'] ?></td>
            <td><?= $d['jenis_kendaraan'] ?></td>
            <td><?= $d['waktu_keluar'] ?></td>
            <td><?= $d['durasi_hari'] ?></td>
            <td><?= $d['biaya'] ?></td>
        </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="6"><strong>Total Pendapatan</strong></td>
            <td><strong><?= $total ?></strong></td>
        </tr>
    </tbody>
</table>
