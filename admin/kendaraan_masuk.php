<?php
date_default_timezone_set("Asia/Jakarta");
session_start();

if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";
include "../vendor/phpqrcode/qrlib.php";

$current_user_id = $_SESSION['id_user'] ?? null;
if (!$current_user_id) {
    die("Session tidak valid. Silakan login kembali.");
}

function generateKodeUnik() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

function generateQRCodeDataUrl($text) {
    ob_start();
    QRcode::png($text, null, QR_ECLEVEL_L, 4);
    $imageString = ob_get_contents();
    ob_end_clean();
    
    return 'data:image/png;base64,' . base64_encode($imageString);
}

$kodeUnik = '';
$showQR = false;
$qrDataUrl = '';

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kendaraan']);
    $jenis = $_POST['jenis_kendaraan'];

    $cekSlot = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT kapasitas_slot - (
            SELECT COUNT(*) FROM kendaraan_masuk WHERE id_jenisKendaraan = '$jenis'
        ) AS sisa FROM jenisKendaraan WHERE id_jenisKendaraan = '$jenis'
    "));

    if ($cekSlot['sisa'] <= 0) {
        echo "<script>
                alert('Slot penuh untuk jenis kendaraan ini');
                window.location='kendaraan_masuk.php';
              </script>";
        exit;
    }

    $kodeUnik = generateKodeUnik();

    $now = date("Y-m-d H:i:s");
    $simpan = mysqli_query($koneksi, "INSERT INTO kendaraan_masuk (kode_unik, nama_kendaraan, id_jenisKendaraan, waktu_masuk, id_user)
                                  VALUES ('$kodeUnik', '$nama', '$jenis', '$now', '$current_user_id')");

    if ($simpan) {
        $qrDataUrl = generateQRCodeDataUrl($kodeUnik);
        $showQR = true;
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "');</script>";
    }
}

$check_column = mysqli_query($koneksi, "SHOW COLUMNS FROM kendaraan_masuk LIKE 'id_user'");
if (mysqli_num_rows($check_column) > 0) {
    $riwayat = mysqli_query($koneksi, "
        SELECT km.*, jk.jenis_kendaraan 
        FROM kendaraan_masuk km
        JOIN jenisKendaraan jk ON km.id_jenisKendaraan = jk.id_jenisKendaraan
        WHERE km.id_user = '$current_user_id'
        ORDER BY km.waktu_masuk DESC
        LIMIT 10
    ");
} else {
    $riwayat = mysqli_query($koneksi, "
        SELECT km.*, jk.jenis_kendaraan 
        FROM kendaraan_masuk km
        JOIN jenisKendaraan jk ON km.id_jenisKendaraan = jk.id_jenisKendaraan
        ORDER BY km.waktu_masuk DESC
        LIMIT 10
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tambah Kendaraan Masuk</title>
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
                            <h4 class="fw-semibold mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Kendaraan Masuk</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Kendaraan Masuk</li>
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
                                <h5 class="card-title fw-semibold mb-4">Form Input Kendaraan</h5>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nama Kendaraan</label>
                                        <input type="text" name="nama_kendaraan" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Jenis Kendaraan</label>
                                        <select name="jenis_kendaraan" class="form-select" required>
                                            <?php
                                            $jenis = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
                                            while ($j = mysqli_fetch_assoc($jenis)) {
                                                echo "<option value='{$j['id_jenisKendaraan']}'>{$j['jenis_kendaraan']} - Rp. " . rupiah($j['harga']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="simpan" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-2"></i>Simpan & Generate QR
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card dashboard-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-4">Informasi Slot Parkir</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Jenis Kendaraan</th>
                                                <th>Kapasitas</th>
                                                <th>Terparkir</th>
                                                <th>Sisa Slot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $infoSlot = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
                                            while ($row = mysqli_fetch_assoc($infoSlot)) {
                                                $idJenis = $row['id_jenisKendaraan'];
                                                $kapasitas = (int)$row['kapasitas_slot'];

                                                $count = mysqli_fetch_assoc(mysqli_query($koneksi, "
                                                    SELECT COUNT(*) AS total FROM kendaraan_masuk WHERE id_jenisKendaraan = '$idJenis'
                                                "))['total'];

                                                $sisa = max(0, $kapasitas - $count);
                                                echo "<tr>
                                                        <td>{$row['jenis_kendaraan']}</td>
                                                        <td>{$kapasitas}</td>
                                                        <td>{$count}</td>
                                                        <td class='" . ($sisa <= 0 ? "text-danger fw-bold" : "text-success") . "'>{$sisa}</td>
                                                      </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<?php if ($showQR): ?>
<div class="modal fade show" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-modal="true" role="dialog" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="qrModalLabel">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>QR Code Berhasil Dibuat
                </h5>
                <button type="button" class="btn-close" onclick="closeModal()"></button>
            </div>
            <div class="modal-body text-center">
                <div class="bg-light rounded p-3 mb-3 d-inline-block">
                    <img src="<?= $qrDataUrl ?>" width="200" class="img-fluid">
                </div>
                <div class="mb-4">
                    <p class="mb-1 text-muted">Kode Unik:</p>
                    <h4 class="fw-bold text-primary"><?= $kodeUnik ?></h4>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button onclick="printStruk()" class="btn btn-success">
                    <i class="bi bi-printer-fill me-2"></i>Cetak QR
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-4">
                                    <i class="bi bi-clock-history me-2"></i>Riwayat Kendaraan Masuk Anda
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Unik</th>
                                                <th>Nama Kendaraan</th>
                                                <th>Jenis</th>
                                                <th>Waktu Masuk</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(mysqli_num_rows($riwayat) > 0): ?>
                                                <?php $no = 1; while ($row = mysqli_fetch_assoc($riwayat)): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><span class="badge bg-primary-light text-primary"><?= $row['kode_unik'] ?></span></td>
                                                    <td><?= $row['nama_kendaraan'] ?></td>
                                                    <td><?= $row['jenis_kendaraan'] ?></td>
                                                    <td><?= date("d/m/Y H:i", strtotime($row['waktu_masuk'])) ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Belum ada riwayat kendaraan masuk</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<?php require_once('template/js.php'); ?>
<script>
function printStruk() {
    const content = `
        <div style="text-align:center; font-family:monospace; padding:10px;">
            =========================<br>
            <strong style="font-size:14px;">PARKIR LOKAL STATION</strong><br>
            -------------------------<br>
            <img src="<?= $qrDataUrl ?>" width="120"><br>
            KODE: <strong><?= $kodeUnik ?></strong><br>
            =========================
        </div>
    `;
    const win = window.open('', '', 'width=300,height=400');
    win.document.write(`
        <html><head><title>Struk Parkir</title>
        <style>body{margin:0;padding:0;font-family:monospace;text-align:center;font-size:12px;}</style>
        </head><body onload="window.print();window.close();">
        ${content}
        </body></html>
    `);
    win.document.close();
}

function closeModal() {
    document.getElementById('qrModal').style.display = 'none';
    document.querySelector('.modal-backdrop').style.display = 'none';
}
</script>
</body>
</html>