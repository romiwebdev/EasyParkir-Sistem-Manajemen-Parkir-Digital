<?php
date_default_timezone_set("Asia/Jakarta");
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $per_page;

$id_petugas = $_SESSION['id_user'];
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM riwayat_keluar WHERE id_user = '$id_petugas'");
$total_row = mysqli_fetch_assoc($total_query);
$total_pages = ceil($total_row['total'] / $per_page);

$riwayat_query = mysqli_query($koneksi, "
    SELECT r.*, j.jenis_kendaraan 
    FROM riwayat_keluar r
    JOIN jenisKendaraan j ON r.id_jenisKendaraan = j.id_jenisKendaraan
    WHERE r.id_user = '$id_petugas'
    ORDER BY r.waktu_keluar DESC
    LIMIT $offset, $per_page
");

$hasil = null;
$kode_unik = $_POST['kode_unik'] ?? $_GET['kode'] ?? '';

if (isset($_GET['check_kode'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['check_kode']);
    $data = mysqli_query($koneksi, "SELECT * FROM kendaraan_masuk 
        JOIN jenisKendaraan ON kendaraan_masuk.id_jenisKendaraan = jenisKendaraan.id_jenisKendaraan 
        WHERE kode_unik = '$kode'");

    if (mysqli_num_rows($data) > 0) {
        $d = mysqli_fetch_assoc($data);
        $masuk = new DateTime($d['waktu_masuk']);
        $keluar = new DateTime();
        $durasi = $masuk->diff($keluar)->days;
        if ($durasi < 1) $durasi = 1;

        $biaya = $durasi * $d['harga'];

        echo json_encode([
            'status' => 'success',
            'data' => [
                'kode' => $d['kode_unik'],
                'nama' => $d['nama_kendaraan'],
                'jenis' => $d['jenis_kendaraan'],
                'masuk' => $masuk->format("d/m/Y H:i"),
                'durasi' => $durasi,
                'biaya' => $biaya
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Kode tidak ditemukan']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_unik']);
    $data = mysqli_query($koneksi, "SELECT * FROM kendaraan_masuk WHERE kode_unik = '$kode'");

    if (mysqli_num_rows($data) > 0) {
        $d = mysqli_fetch_assoc($data);

        $masuk = new DateTime($d['waktu_masuk']);
        $keluar = new DateTime();
        $durasi = $masuk->diff($keluar)->days;
        if ($durasi < 1) $durasi = 1;

        $jenis = $d['id_jenisKendaraan'];
        $jenisData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM jenisKendaraan WHERE id_jenisKendaraan = $jenis"));
        $biaya = $durasi * $jenisData['harga'];

        $id_user = $_SESSION['id_user'];
        $waktu_keluar = date("Y-m-d H:i:s");
        mysqli_query($koneksi, "INSERT INTO riwayat_keluar 
            (kode_unik, nama_kendaraan, id_jenisKendaraan, waktu_masuk, durasi_hari, biaya, id_user, waktu_keluar) 
            VALUES ('$kode', '{$d['nama_kendaraan']}', '{$jenis}', '{$d['waktu_masuk']}', '$durasi', '$biaya', '$id_user', '$waktu_keluar')");

        mysqli_query($koneksi, "DELETE FROM kendaraan_masuk WHERE kode_unik = '$kode'");

        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Konfirmasi Kendaraan Keluar</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <?php require_once('template/css.php'); ?>
    <script src="https://unpkg.com/html5-qrcode"></script>
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-check-circle me-2"></i>Konfirmasi Kendaraan Keluar</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Kendaraan Keluar</li>
                                </ol>
                            </nav>
                        </div>
                        <hr class="mt-2">
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card dashboard-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-4">Scan/Masukkan Kode</h5>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Kode Unik Kendaraan</label>
                                    <input type="text" id="kode_unik" class="form-control" placeholder="Masukkan kode unik" autofocus>
                                    <div class="invalid-feedback" id="kode-feedback"></div>
                                </div>
                                <button class="btn btn-primary w-100 mb-3" onclick="startScanner()">
                                    <i class="bi bi-qr-code-scan me-2"></i>Scan QR Code
                                </button>
                                <div id="reader" class="mt-3" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-4">
                                    <i class="bi bi-clock-history me-2"></i>Riwayat Keluar
                                </h5>
                                
                                <?php if (mysqli_num_rows($riwayat_query) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kode</th>
                                                <th>Kendaraan</th>
                                                <th>Waktu</th>
                                                <th>Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($riwayat_query)): ?>
                                            <tr>
                                                <td><span class="badge bg-primary-light text-primary"><?= $row['kode_unik'] ?></span></td>
                                                <td>
                                                    <div><?= $row['nama_kendaraan'] ?></div>
                                                    <small class="text-muted"><?= $row['jenis_kendaraan'] ?></small>
                                                </td>
                                                <td><?= date("d/m H:i", strtotime($row['waktu_keluar'])) ?></td>
                                                <td class="fw-semibold">Rp <?= rupiah($row['biaya']) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <nav aria-label="Page navigation" class="mt-3">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page-1 ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php 
                                        $start = max(1, $page - 2);
                                        $end = min($total_pages, $page + 2);
                                        
                                        for ($i = $start; $i <= $end; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page+1 ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                <?php else: ?>
                                <div class="alert alert-info">Belum ada riwayat keluar</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="detailModalLabel">
                    <i class="bi bi-car-front me-2"></i>Detail Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted">Kode Unik</th>
                                <td id="detail-kode"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Nama Kendaraan</th>
                                <td id="detail-nama"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Jenis Kendaraan</th>
                                <td id="detail-jenis"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Waktu Masuk</th>
                                <td id="detail-masuk"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Durasi Parkir</th>
                                <td id="detail-durasi"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Total Biaya</th>
                                <td class="fw-bold text-success" id="detail-biaya"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btn-konfirmasi">
                    <i class="bi bi-check-circle-fill me-2"></i>Konfirmasi Keluar
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once('template/js.php'); ?>

<script>
let scannerActive = false;
let html5QrCode;
let currentKode = '';

const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

function checkKodeUnik(kode) {
    if (kode.length < 3) return;
    
    fetch(`keluar.php?check_kode=${kode}`)
        .then(response => response.json())
        .then(data => {
            const kodeInput = document.getElementById('kode_unik');
            const feedback = document.getElementById('kode-feedback');
            
            if (data.status === 'success') {
                kodeInput.classList.remove('is-invalid');
                feedback.textContent = '';
                
                currentKode = data.data.kode;
                document.getElementById('detail-kode').textContent = data.data.kode;
                document.getElementById('detail-nama').textContent = data.data.nama;
                document.getElementById('detail-jenis').textContent = data.data.jenis;
                document.getElementById('detail-masuk').textContent = data.data.masuk;
                document.getElementById('detail-durasi').textContent = data.data.durasi + ' hari';
                document.getElementById('detail-biaya').textContent = 'Rp. ' + data.data.biaya.toLocaleString('id-ID');
                
                detailModal.show();
            } else {
                kodeInput.classList.add('is-invalid');
                feedback.textContent = data.message;
            }
        });
}

document.getElementById('kode_unik').addEventListener('input', function(e) {
    checkKodeUnik(e.target.value);
});

document.getElementById('btn-konfirmasi').addEventListener('click', function() {
    const formData = new FormData();
    formData.append('kode_unik', currentKode);
    formData.append('konfirmasi', 'true');
    
    fetch('keluar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            detailModal.hide();
            window.location.reload();
        }
    });
});

function startScanner() {
    if (scannerActive) return;

    const reader = document.getElementById('reader');
    reader.style.display = 'block';

    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        qrCodeMessage => {
            document.getElementById("kode_unik").value = qrCodeMessage;
            html5QrCode.stop().then(() => {
                scannerActive = false;
                reader.innerHTML = "";
                checkKodeUnik(qrCodeMessage);
            });
        },
        error => {}
    ).then(() => {
        scannerActive = true;
    }).catch(err => {
        alert("Gagal akses kamera: " + err);
    });
}
</script>
</body>
</html>