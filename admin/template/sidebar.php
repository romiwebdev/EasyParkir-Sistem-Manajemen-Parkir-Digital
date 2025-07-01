<div class="sidebar">
    <div class="sidebar-brand">
        <a href="index.php" class="text-white text-decoration-none fw-bold fs-5">
            <i class="bi bi-p-square-fill me-2"></i>EasyParkir
        </a>
    </div>
    
    <div class="sidebar-nav">
        <a href="index.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="users.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span>Manajemen User</span>
        </a>
        <?php endif; ?>

        <a href="kendaraan_masuk.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'kendaraan_masuk.php' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Kendaraan Masuk</span>
        </a>

        <a href="keluar.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'keluar.php' ? 'active' : '' ?>">
            <i class="bi bi-check-circle"></i>
            <span>Kendaraan Keluar</span>
        </a>

        <a href="parkir_aktif.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'parkir_aktif.php' ? 'active' : '' ?>">
            <i class="bi bi-list-ul"></i>
            <span>Kendaraan Parkir</span>
        </a>

        <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="slot_parkir.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'slot_parkir.php' ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <span>Slot Parkir</span>
        </a>

        <a href="jenis_kendaraan.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'jenis_kendaraan.php' ? 'active' : '' ?>">
            <i class="bi bi-truck"></i>
            <span>Jenis Kendaraan</span>
        </a>

        <a href="laporan.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span>Laporan Parkir</span>
        </a>
        <?php endif; ?>

        <a href="../index.php" target="_blank" class="sidebar-link">
            <i class="bi bi-globe"></i>
            <span>Halaman Publik</span>
        </a>

        <a href="../config/do_logout.php" class="sidebar-link text-danger">
            <i class="bi bi-power"></i>
            <span>Logout</span>
        </a>
    </div>
</div>