<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

if (isset($_POST['update'])) {
    foreach ($_POST['kapasitas'] as $id => $nilai) {
        $id = intval($id);
        $nilai = intval($nilai);
        mysqli_query($koneksi, "UPDATE jenisKendaraan SET kapasitas_slot = '$nilai' WHERE id_jenisKendaraan = '$id'");
    }
    echo "<script>alert('Slot berhasil diperbarui'); window.location='slot_parkir.php';</script>";
    exit;
}

$data = [];
$query = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");

while ($jenis = mysqli_fetch_assoc($query)) {
    $id = $jenis['id_jenisKendaraan'];
    $kapasitas = intval($jenis['kapasitas_slot']);

    $terparkirQuery = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan_masuk WHERE id_jenisKendaraan = '$id'");
    $terparkir = mysqli_fetch_assoc($terparkirQuery)['total'];

    $tersisa = max(0, $kapasitas - $terparkir);

    $data[] = [
        'id' => $id,
        'jenis' => $jenis['jenis_kendaraan'],
        'kapasitas' => $kapasitas,
        'terparkir' => $terparkir,
        'sisa' => $tersisa
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manajemen Slot Parkir</title>
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Manajemen Slot Parkir</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Slot Parkir</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <form method="POST">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Jenis Kendaraan</th>
                                            <th>Kapasitas</th>
                                            <th>Terparkir</th>
                                            <th>Sisa Slot</th>
                                            <th>Ubah Kapasitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $d): ?>
                                        <tr>
                                            <td><?= $d['jenis'] ?></td>
                                            <td><?= $d['kapasitas'] ?></td>
                                            <td><?= $d['terparkir'] ?></td>
                                            <td>
                                                <span class="badge <?= $d['sisa'] <= 0 ? 'bg-danger-light text-danger' : 'bg-success-light text-success' ?> rounded-pill">
                                                    <?= $d['sisa'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <input type="number" name="kapasitas[<?= $d['id'] ?>]" value="<?= $d['kapasitas'] ?>" min="0" class="form-control form-control-sm">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<?php require_once('template/js.php'); ?>
</body>
</html>