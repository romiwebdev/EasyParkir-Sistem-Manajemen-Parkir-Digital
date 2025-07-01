<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses ditolak! Hanya untuk admin.'); window.location='../index.php';</script>";
    exit;
}

include "../config/config.php";
$data = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jenis Kendaraan</title>
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-truck me-2"></i>Jenis Kendaraan</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Jenis Kendaraan</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="card-title fw-semibold">Daftar Jenis Kendaraan</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                                <i class="bi bi-plus-lg me-2"></i>Tambah Jenis
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Kendaraan</th>
                                        <th>Harga</th>
                                        <th>Kapasitas Slot</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($kendaraan = mysqli_fetch_assoc($data)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $kendaraan['jenis_kendaraan'] ?></td>
                                        <td>Rp <?= rupiah($kendaraan['harga']) ?></td>
                                        <td><?= $kendaraan['kapasitas_slot'] ?> slot</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    data-id="<?= $kendaraan['id_jenisKendaraan'] ?>"
                                                    data-jenis="<?= $kendaraan['jenis_kendaraan'] ?>"
                                                    data-harga="<?= $kendaraan['harga'] ?>"
                                                    data-kapasitas="<?= $kendaraan['kapasitas_slot'] ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="../config/jenis/do_hapus.php?id=<?= $kendaraan['id_jenisKendaraan'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Yakin hapus jenis kendaraan ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahModalLabel">Tambah Jenis Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../config/jenis/do_tambah.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <input type="text" class="form-control" name="jenis_kendaraan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas Slot</label>
                        <input type="number" class="form-control" name="kapasitas_slot" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Jenis Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../config/jenis/do_edit.php" method="post">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <input type="text" class="form-control" name="jenis_kendaraan" id="edit_jenis" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" id="edit_harga" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas Slot</label>
                        <input type="number" class="form-control" name="kapasitas_slot" id="edit_kapasitas" required>
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
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var jenis = button.getAttribute('data-jenis');
        var harga = button.getAttribute('data-harga');
        var kapasitas = button.getAttribute('data-kapasitas');
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_jenis').value = jenis;
        document.getElementById('edit_harga').value = harga;
        document.getElementById('edit_kapasitas').value = kapasitas;
    });
});
</script>
</body>
</html>