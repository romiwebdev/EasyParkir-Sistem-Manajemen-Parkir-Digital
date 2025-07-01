<?php
session_start();
if (isset($_SESSION['login'])) {
    header("location:admin/index.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - EasyParkir</title>
    <?php require_once('template/css.php'); ?>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex flex-column min-vh-100 justify-content-center align-items-center">
    <div class="login-card shadow-lg rounded-4 overflow-hidden" style="width: 100%; max-width: 400px;">
        <div class="card-header bg-primary text-white p-4">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle p-2 me-3">
                    <i class="bi bi-lock-fill text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-semibold">Parkir Lokal Station</h4>
                    <small class="opacity-75">Administrator Login</small>
                </div>
            </div>
        </div>
        
        <div class="card-body p-4 bg-white">
            <form method="post" action="config/do_login.php">
                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold text-muted">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary-light border-end-0"><i class="bi bi-person-fill text-primary"></i></span>
                        <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary-light border-end-0"><i class="bi bi-key-fill text-primary"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </button>
            </form>
        </div>
        
        <div class="card-footer bg-light p-3 text-center">
            <a href="index.php" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Homepage
            </a>
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <p class="small text-muted">Dikembangkan oleh <a href="https://romifullstack.vercel.app" target="_blank" class="text-primary text-decoration-none">Romi</a></p>
    </div>
</div>

<?php require_once('template/js.php'); ?>

<style>
.login-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.bg-primary-light {
    background-color: rgba(13, 110, 253, 0.1);
}

.card-header {
    border-bottom: none;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
}

.form-control:focus {
    box-shadow: none;
    border-color: #86b7fe;
}

.input-group-text {
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
</style>
</body>
</html>