<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard Parkir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="fw-semibold">Selamat datang, <span class="text-primary"><?= $_SESSION['username']; ?></span>!</h5>
                                <p class="text-muted mb-0">Sistem parkir sudah aktif. Gunakan menu sidebar untuk navigasi fitur.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-primary-light rounded p-4 d-inline-block">
                                    <i class="bi bi-car-front-fill fs-1 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <?php
                    include "../config/config.php";
                    $qJenis = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
                    while ($j = mysqli_fetch_assoc($qJenis)) {
                        $id = $j['id_jenisKendaraan'];
                        $kapasitas = $j['kapasitas_slot'];
                        $terparkir = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan_masuk WHERE id_jenisKendaraan = '$id'"))['total'];
                        $persentase = ($terparkir / $kapasitas) * 100;
                        
                        echo '<div class="col-md-4">
                            <div class="card dashboard-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-semibold">'.$j['jenis_kendaraan'].'</h6>
                                        <span class="badge bg-primary-light text-primary rounded-pill">'.$terparkir.'/'.$kapasitas.'</span>
                                    </div>
                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: '.$persentase.'%" aria-valuenow="'.$persentase.'" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Terisi</span>
                                        <span class="text-primary fw-semibold">'.($kapasitas - $terparkir).' Slot Tersedia</span>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<?php require_once('template/js.php'); ?>
</body>
</html>