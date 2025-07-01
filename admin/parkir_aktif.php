<?php
date_default_timezone_set("Asia/Jakarta");
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

$filter = '';
$jenis_filter = isset($_GET['jenis']) ? $_GET['jenis'] : '';
if (!empty($jenis_filter)) {
    $filter = "WHERE jk.id_jenisKendaraan = '$jenis_filter'";
}

$total_query = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM kendaraan_masuk km 
    JOIN jenisKendaraan jk ON km.id_jenisKendaraan = jk.id_jenisKendaraan 
    $filter
");
$total = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total / $per_page);

$data = mysqli_query($koneksi, "
    SELECT km.*, jk.jenis_kendaraan, u.username as petugas 
    FROM kendaraan_masuk km 
    JOIN jenisKendaraan jk ON km.id_jenisKendaraan = jk.id_jenisKendaraan
    LEFT JOIN user u ON km.id_user = u.id
    $filter
    ORDER BY km.waktu_masuk DESC
    LIMIT $start, $per_page
");

$jenis_kendaraan = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Daftar Kendaraan Terparkir</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <?php require_once('template/css.php'); ?>
    <style>
        .nav-tabs .nav-link {
            border: 1px solid #dee2e6;
            border-bottom: none;
            margin-right: 5px;
            color: #495057;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            background-color: #f8f9fa;
            border-color: #dee2e6 #dee2e6 #f8f9fa;
            color: #0d6efd;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-car-front-fill me-2"></i>Daftar Kendaraan Parkir</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Kendaraan Parkir</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="mb-4">
                            <ul class="nav nav-tabs" id="jenisTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link <?= empty($jenis_filter) ? 'active' : '' ?>" 
                                       href="parkir_aktif.php" 
                                       aria-selected="<?= empty($jenis_filter) ? 'true' : 'false' ?>">
                                        Semua Jenis
                                    </a>
                                </li>
                                <?php while ($jk = mysqli_fetch_assoc($jenis_kendaraan)): ?>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link <?= $jenis_filter == $jk['id_jenisKendaraan'] ? 'active' : '' ?>" 
                                       href="parkir_aktif.php?jenis=<?= $jk['id_jenisKendaraan'] ?>" 
                                       aria-selected="<?= $jenis_filter == $jk['id_jenisKendaraan'] ? 'true' : 'false' ?>">
                                        <?= $jk['jenis_kendaraan'] ?>
                                    </a>
                                </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode Unik</th>
                                        <th>Nama Kendaraan</th>
                                        <th>Jenis</th>
                                        <th>Petugas</th>
                                        <th>Waktu Masuk</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = $start + 1;
                                    while ($row = mysqli_fetch_assoc($data)): 
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><span class="badge bg-primary-light text-primary"><?= $row['kode_unik'] ?></span></td>
                                        <td><?= $row['nama_kendaraan'] ?></td>
                                        <td><?= $row['jenis_kendaraan'] ?></td>
                                        <td><?= $row['petugas'] ?? 'System' ?></td>
                                        <td><?= date("d/m/Y H:i", strtotime($row['waktu_masuk'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= $row['nama_kendaraan'] ?>"
                                                    data-jenis="<?= $row['id_jenisKendaraan'] ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="hapus_kendaraan.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Yakin hapus data ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <?php if ($pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="parkir_aktif.php?jenis=<?= $jenis_filter ?>&page=<?= $page-1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    
                                    <?php for($i = 1; $i <= $pages; $i++): ?>
                                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                        <a class="page-link" href="parkir_aktif.php?jenis=<?= $jenis_filter ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?= $page == $pages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="parkir_aktif.php?jenis=<?= $jenis_filter ?>&page=<?= $page+1 ?>" aria-label="Next">
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
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="update_kendaraan.php">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kendaraan</label>
                        <input type="text" name="nama_kendaraan" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <select name="jenis_kendaraan" id="editJenis" class="form-select" required>
                            <?php 
                            mysqli_data_seek($jenis_kendaraan, 0); // Reset pointer
                            while ($j = mysqli_fetch_assoc($jenis_kendaraan)): ?>
                                <option value="<?= $j['id_jenisKendaraan'] ?>"><?= $j['jenis_kendaraan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('template/js.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editButtons = document.querySelectorAll('.edit-btn');
    
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var nama = this.getAttribute('data-nama');
            var jenis = this.getAttribute('data-jenis');
            
            document.getElementById('editId').value = id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editJenis').value = jenis;
        });
    });
});
</script>
</body>
</html>