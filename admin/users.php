<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses hanya untuk admin'); window.location='index.php';</script>";
    exit;
}

include "../config/config.php";

// Tambah User
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', '$role')");
    if ($query) {
        echo "<script>alert('User berhasil ditambahkan'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan user: " . mysqli_error($koneksi) . "');</script>";
    }
    exit;
}

if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    
    if ($id != $_SESSION['id_user']) {
        $query = mysqli_query($koneksi, "DELETE FROM user WHERE id = '$id'");
        if ($query) {
            echo "<script>alert('User berhasil dihapus'); window.location='users.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus user: " . mysqli_error($koneksi) . "');</script>";
        }
    } else {
        echo "<script>alert('Tidak boleh menghapus akun sendiri!');</script>";
    }
    exit;
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = $_POST['role'];
    $updatePassword = "";

    if (!empty($_POST['password'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updatePassword = ", password = '$hashed'";
    }

    $query = mysqli_query($koneksi, "UPDATE user SET username='$username', role='$role' $updatePassword WHERE id='$id'");
    if ($query) {
        echo "<script>alert('User berhasil diupdate'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate user: " . mysqli_error($koneksi) . "');</script>";
    }
    exit;
}

$jumlah_per_halaman = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$mulai_dari = ($page > 1) ? ($page * $jumlah_per_halaman) - $jumlah_per_halaman : 0;

$total_result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user");
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_halaman = ceil($total_data / $jumlah_per_halaman);

$query_users = "SELECT * FROM user ORDER BY role ASC LIMIT $mulai_dari, $jumlah_per_halaman";
$users = mysqli_query($koneksi, $query_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manajemen User</title>
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-people-fill me-2"></i>Manajemen User</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-semibold mb-0">Daftar User</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                                <i class="bi bi-plus-lg me-2"></i>Tambah User
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = $mulai_dari + 1; 
                                    while ($u = mysqli_fetch_assoc($users)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $u['username'] ?></td>
                                        <td>
                                            <span class="badge <?= $u['role'] == 'admin' ? 'bg-primary' : 'bg-secondary' ?> rounded-pill">
                                                <?= ucfirst($u['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($u['id'] != $_SESSION['id_user']): ?>
                                                <button class="btn btn-sm btn-outline-primary edit-user" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal"
                                                        data-id="<?= $u['id'] ?>"
                                                        data-username="<?= $u['username'] ?>"
                                                        data-role="<?= $u['role'] ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus user ini?')">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" name="hapus" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-success-light text-success">Login Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    
                                    <?php 
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_halaman, $page + 2);
                                    
                                    if ($start_page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                        if ($start_page > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        $active = $i == $page ? 'active' : '';
                                        echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.'">'.$i.'</a></li>';
                                    }
                                    
                                    if ($end_page < $total_halaman) {
                                        if ($end_page < $total_halaman - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="?page='.$total_halaman.'">'.$total_halaman.'</a></li>';
                                    }
                                    ?>
                                    
                                    <li class="page-item <?= $page >= $total_halaman ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<div class="modal fade" id="tambahUserModal" tabindex="-1" aria-labelledby="tambahUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahUserModalLabel">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="id" id="editUserId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('template/js.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editButtons = document.querySelectorAll('.edit-user');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var username = this.getAttribute('data-username');
            var role = this.getAttribute('data-role');
            
            document.getElementById('editUserId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editRole').value = role;
        });
    });
});
</script>
</body>
</html>