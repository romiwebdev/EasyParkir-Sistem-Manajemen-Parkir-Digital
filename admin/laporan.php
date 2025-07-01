<?php
date_default_timezone_set("Asia/Jakarta");
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

if (isset($_POST['reset'])) {
    mysqli_query($koneksi, "DELETE FROM riwayat_keluar");
    echo "<script>alert('Laporan berhasil direset.'); window.location='laporan.php';</script>";
    exit;
}

$per_page = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); 
$offset = ($page - 1) * $per_page;

$filter = '';
$label = 'Semua Data';
$data = [];
$where_clause = '';

if (isset($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $where_clause = "WHERE DATE(waktu_keluar) = '$tanggal'";
    $label = "Tanggal: " . date("d M Y", strtotime($tanggal));
} elseif (isset($_GET['bulan'])) {
    $bulan = $_GET['bulan'];
    $where_clause = "WHERE MONTH(waktu_keluar) = '$bulan'";
    $label = "Bulan: " . date("F", mktime(0, 0, 0, $bulan, 1));
}

$query = mysqli_query($koneksi, "
    SELECT rk.*, jk.jenis_kendaraan, u.username 
    FROM riwayat_keluar rk 
    JOIN jenisKendaraan jk ON rk.id_jenisKendaraan = jk.id_jenisKendaraan
    LEFT JOIN user u ON rk.id_user = u.id
    $where_clause
    ORDER BY waktu_keluar DESC
    LIMIT $offset, $per_page
");

$total_query = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM riwayat_keluar rk
    $where_clause
");
$total_row = mysqli_fetch_assoc($total_query);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $per_page);

$total_motor = 0;
$total_mobil = 0;
$pendapatan = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
    $pendapatan += $row['biaya'];
    if (strtolower($row['jenis_kendaraan']) === 'motor') {
        $total_motor++;
    } else {
        $total_mobil++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laporan Parkir</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <?php require_once('template/css.php'); ?>
</head>
<body>
<div class="main-wrapper">
    <?php require_once('template/sidebar.php'); ?>

    <div class="main-content">
        <?php require_once('template/nav.php'); ?>

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-semibold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Laporan Parkir</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="fw-semibold mb-3">Filter Laporan</h5>
                                <form method="GET" class="row g-3">
                                    <input type="hidden" name="page" value="1">
                                    <div class="col-md-5">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control" value="<?= $_GET['tanggal'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Bulan (1-12)</label>
                                        <input type="number" name="bulan" min="1" max="12" class="form-control" 
                                               placeholder="1-12" value="<?= $_GET['bulan'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-funnel me-2"></i>Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <h5 class="fw-semibold mb-3">Aksi</h5>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-success" href="laporan_excel.php<?= isset($_GET['tanggal']) ? '?tanggal='.$_GET['tanggal'] : (isset($_GET['bulan']) ? '?bulan='.$_GET['bulan'] : '') ?>">
                                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus semua laporan?');">
                                        <button type="submit" name="reset" class="btn btn-danger">
                                            <i class="bi bi-trash me-2"></i>Reset Laporan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-primary bg-primary-light border-0 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= $label ?></strong> | 
                                    <span class="me-3"><i class="bi bi-car-front me-2"></i>Mobil: <strong><?= $total_mobil ?></strong></span>
                                    <span><i class="bi bi-bicycle me-2"></i>Motor: <strong><?= $total_motor ?></strong></span>
                                </div>
                                <div>
                                    <span class="fw-bold">Total Pendapatan: Rp <?= rupiah($pendapatan) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Keluar</th>
                                        <th>Kode Unik</th>
                                        <th>Nama Kendaraan</th>
                                        <th>Jenis</th>
                                        <th>Durasi</th>
                                        <th>Biaya</th>
                                        <th>Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $d): ?>
                                    <tr>
                                        <td><?= date("d/m/Y H:i", strtotime($d['waktu_keluar'])) ?></td>
                                        <td><span class="badge bg-primary-light text-primary"><?= $d['kode_unik'] ?></span></td>
                                        <td><?= $d['nama_kendaraan'] ?></td>
                                        <td><?= $d['jenis_kendaraan'] ?></td>
                                        <td><?= $d['durasi_hari'] ?> hari</td>
                                        <td class="fw-semibold">Rp <?= rupiah($d['biaya']) ?></td>
                                        <td><?= $d['username'] ?? '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($data)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Tidak ada data laporan</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php 
                                $query_params = $_GET;
                                unset($query_params['page']);
                                $query_string = http_build_query($query_params);
                                ?>
                                
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?<?= $query_string ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <?php 
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?'.$query_string.'&page=1">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item '.$active.'"><a class="page-link" href="?'.$query_string.'&page='.$i.'">'.$i.'</a></li>';
                                }
                                
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?'.$query_string.'&page='.$total_pages.'">'.$total_pages.'</a></li>';
                                }
                                ?>
                                
                                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?<?= $query_string ?>&page=<?= $page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<?php require_once('template/js.php'); ?>
</body>
</html>