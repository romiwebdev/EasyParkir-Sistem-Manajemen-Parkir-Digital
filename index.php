<?php 
date_default_timezone_set("Asia/Jakarta");
include "config/config.php";

$per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan_masuk");
$total = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total / $per_page);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_unik']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_unik']);
    $cek = mysqli_query($koneksi, "
        SELECT km.*, jk.jenis_kendaraan, jk.harga 
        FROM kendaraan_masuk km 
        JOIN jenisKendaraan jk ON km.id_jenisKendaraan = jk.id_jenisKendaraan 
        WHERE kode_unik = '$kode'
    ");

    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        $masuk = new DateTime($data['waktu_masuk']);
        $sekarang = new DateTime();
        $durasi = $masuk->diff($sekarang)->days;
        if ($durasi < 1) $durasi = 1;
        $biaya = $durasi * $data['harga'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'nama' => $data['nama_kendaraan'],
                'jenis' => $data['jenis_kendaraan'],
                'masuk' => $masuk->format('d/m/Y H:i'),
                'durasi' => $durasi,
                'biaya' => rupiah($biaya),
                'kode' => $kode
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Kode tidak ditemukan atau kendaraan sudah keluar'
        ]);
    }
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyParkir - Sistem Parkir Digital</title>
    <?php require_once('template/css.php'); ?>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            color: #333;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .pagination a:hover {
            background-color: #4361ee;
            color: white;
            border-color: #4361ee;
        }
        .pagination .active {
            background-color: #4361ee;
            color: white;
            border-color: #4361ee;
        }
        .pagination .disabled {
            color: #ddd;
            pointer-events: none;
        }
        
        /* Modal Styles */
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        .modal-footer {
            border-top: none;
            padding-top: 0;
        }
        .biaya-detail {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .biaya-detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .biaya-total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #4361ee;
        }
        .kode-badge {
            background-color: #e8f4fd;
            color: #4361ee;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<?php require_once('template/nav.php'); ?>

<div class="container-fluid px-3 px-md-4 py-4">
    <div class="hero-section text-center mb-4 p-4 bg-white rounded-3 shadow-sm">
        <h1 class="display-6 fw-bold mb-2">Selamat Datang di <span class="text-primary">EasyParkir</span></h1>
        <p class="lead text-muted mb-0">Sistem parkir digital yang mudah dan efisien</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <?php
                $qJenis = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
                while ($j = mysqli_fetch_assoc($qJenis)) {
                    $id = $j['id_jenisKendaraan'];
                    $kapasitas = $j['kapasitas_slot'];
                    $terparkir = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan_masuk WHERE id_jenisKendaraan = '$id'"))['total'];
                    $sisa = max(0, $kapasitas - $terparkir);
                    $persentase = ($terparkir / $kapasitas) * 100;
                    
                    echo '<div class="col-md-6 col-xl-4">
                        <div class="card stat-card h-100 border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title mb-0 fw-semibold">'.$j['jenis_kendaraan'].'</h6>
                                    <span class="badge bg-primary-light text-primary rounded-pill">'.$terparkir.'/'.$kapasitas.'</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: '.$persentase.'%" aria-valuenow="'.$persentase.'" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Terisi</span>
                                    <span class="text-primary fw-semibold">'.$sisa.' Tersedia</span>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-car-front-fill text-primary me-2"></i>Kendaraan Parkir</h5>
                        <div>
                            <span class="badge bg-primary text-white">Total: <?= $total ?> kendaraan</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Nama Kendaraan</th>
                                    <th>Jenis</th>
                                    <th>Waktu Masuk</th>
                                    <th class="pe-4">Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $dataParkir = mysqli_query($koneksi, "
                                SELECT km.*, jk.jenis_kendaraan 
                                FROM kendaraan_masuk km 
                                JOIN jenisKendaraan jk ON km.id_jenisKendaraan = jk.id_jenisKendaraan 
                                ORDER BY km.waktu_masuk DESC
                                LIMIT $start, $per_page
                            ");
                            
                            $no = $start + 1;
                            while ($k = mysqli_fetch_assoc($dataParkir)) {
                                $waktu_masuk = new DateTime($k['waktu_masuk']);
                                $sekarang = new DateTime();
                                $durasi = $waktu_masuk->diff($sekarang);
                                
                                echo "<tr>
                                        <td class='ps-4'>$no</td>
                                        <td>{$k['nama_kendaraan']}</td>
                                        <td><span class='badge bg-primary-light text-primary rounded-pill'>{$k['jenis_kendaraan']}</span></td>
                                        <td>" . $waktu_masuk->format('d/m/Y H:i') . "</td>
                                        <td class='pe-4'>";
                                
                                if ($durasi->d > 0) echo $durasi->d . ' hari ';
                                if ($durasi->h > 0) echo $durasi->h . ' jam ';
                                echo $durasi->i . ' menit';
                                
                                echo "</td>
                                      </tr>";
                                $no++;
                            }
                            
                            if (mysqli_num_rows($dataParkir) == 0) {
                                echo '<tr><td colspan="5" class="text-center py-4">Tidak ada kendaraan parkir</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination p-3">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>">&laquo;</a>
                        <?php else: ?>
                            <span class="disabled">&laquo;</span>
                        <?php endif; ?>
                        
                        <?php 
                        $start_page = max(1, $page - 2);
                        $end_page = min($pages, $page + 2);
                        
                        if ($start_page > 1) {
                            echo '<a href="?page=1">1</a>';
                            if ($start_page > 2) echo '<span>...</span>';
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?= $i ?>" <?= ($i == $page) ? 'class="active"' : '' ?>><?= $i ?></a>
                        <?php endfor;
                        
                        if ($end_page < $pages) {
                            if ($end_page < $pages - 1) echo '<span>...</span>';
                            echo '<a href="?page='.$pages.'">'.$pages.'</a>';
                        }
                        ?>
                        
                        <?php if ($page < $pages): ?>
                            <a href="?page=<?= $page + 1 ?>">&raquo;</a>
                        <?php else: ?>
                            <span class="disabled">&raquo;</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4"><i class="bi bi-credit-card-fill text-primary me-2"></i>Cek Tarif Parkir</h5>
                    
                    <form id="cekTarifForm" class="mb-4">
                        <div class="mb-3">
                            <label for="kode_unik" class="form-label small fw-semibold">Masukkan Kode Unik</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-light border-primary-light"><i class="bi bi-upc-scan text-primary"></i></span>
                                <input type="text" name="kode_unik" id="kode_unik" class="form-control border-primary-light" placeholder="PKR-1234" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-calculator-fill me-2"></i>Hitung Tarif
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <button class="btn btn-outline-primary w-100 mb-3" onclick="startScanner()">
                            <i class="bi bi-qr-code-scan me-2"></i>Scan QR Code
                        </button>
                        <div id="reader" style="width:100%; display:none;"></div>
                        <p class="small text-muted">Arahkan kamera ke QR Code untuk memeriksa tarif parkir</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tarifModal" tabindex="-1" aria-labelledby="tarifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="tarifModalLabel"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Detail Tarif Parkir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Kode Unik:</span>
                    <span id="modalKode" class="kode-badge"></span>
                </div>
                <div class="biaya-detail">
                    <div class="biaya-detail-item">
                        <span class="text-muted">Nama Kendaraan:</span>
                        <span id="modalNama" class="fw-semibold"></span>
                    </div>
                    <div class="biaya-detail-item">
                        <span class="text-muted">Jenis Kendaraan:</span>
                        <span id="modalJenis" class="fw-semibold"></span>
                    </div>
                    <div class="biaya-detail-item">
                        <span class="text-muted">Waktu Masuk:</span>
                        <span id="modalMasuk" class="fw-semibold"></span>
                    </div>
                    <div class="biaya-detail-item">
                        <span class="text-muted">Durasi Parkir:</span>
                        <span id="modalDurasi" class="fw-semibold"></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h6 class="mb-0 fw-semibold">Total Biaya:</h6>
                    <h4 id="modalBiaya" class="mb-0 biaya-total"></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('template/footer.php'); ?>
<?php require_once('template/js.php'); ?>

<script>
function startScanner() {
    const reader = document.getElementById('reader');
    reader.style.display = 'block';
    const qr = new Html5Qrcode("reader");

    qr.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (code) => {
            document.getElementById('kode_unik').value = code;
            qr.stop().then(() => {
                reader.innerHTML = '';
                document.getElementById('cekTarifForm').dispatchEvent(new Event('submit'));
            });
        },
        (err) => {
            console.error(err);
        }
    );
}

function showTarifModal(data) {
    document.getElementById('modalKode').textContent = data.kode;
    document.getElementById('modalNama').textContent = data.nama;
    document.getElementById('modalJenis').textContent = data.jenis;
    document.getElementById('modalMasuk').textContent = data.masuk;
    document.getElementById('modalDurasi').textContent = data.durasi + ' hari';
    document.getElementById('modalBiaya').textContent = 'Rp ' + data.biaya;
    
    window.tarifData = data;
    
    var modal = new bootstrap.Modal(document.getElementById('tarifModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cekTarifForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = form.querySelector('button[type="submit"]');
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
            
            fetch('', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-calculator-fill me-2"></i>Hitung Tarif';
                
                if (data.status === 'success') {
                    showTarifModal(data.data);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-calculator-fill me-2"></i>Hitung Tarif';
                alert('Terjadi kesalahan saat memproses permintaan');
            });
        });
    }
});
</script>
</body>
</html>