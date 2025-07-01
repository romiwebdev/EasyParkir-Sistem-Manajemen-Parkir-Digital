<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

// Get logged in user data
$id = $_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

// Process profile update
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $passwordBaru = $_POST['password'];
    $updatePassword = "";

    if (!empty($passwordBaru)) {
        if (strlen($passwordBaru) < 8) {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Password harus minimal 8 karakter'
                    });
                  </script>";
        } else {
            $hashed = password_hash($passwordBaru, PASSWORD_DEFAULT);
            $updatePassword = ", password = '$hashed'";
        }
    }

    if (empty($updatePassword) || strlen($passwordBaru) >= 8) {
        $update = mysqli_query($koneksi, "UPDATE user SET username='$username' $updatePassword WHERE id = '$id'");
        if ($update) {
            $_SESSION['username'] = $username;
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Profil berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location='profil.php';
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memperbarui profil: " . addslashes(mysqli_error($koneksi)) . "'
                    });
                  </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil - EasyParkir</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <?php require_once('template/css.php'); ?>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 3px solid white;
            font-size: 3.5rem;
        }
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            padding: 0.5rem 1.5rem;
        }
        .btn-primary:hover {
            background-color: #3a0ca3;
            border-color: #3a0ca3;
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <!-- Sidebar -->
    <?php require_once('template/sidebar.php'); ?>

    <div class="main-content">
        <!-- Navbar -->
        <?php require_once('template/nav.php'); ?>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <div class="profile-avatar rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                            <i class="bi bi-person-fill text-white"></i>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <h2 class="fw-bold mb-1"><?= $_SESSION['username'] ?></h2>
                        <p class="mb-0">
                            <span class="badge bg-light text-primary"><?= ucfirst($_SESSION['role']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="profile-card card">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold mb-4 text-primary">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profil
                            </h4>
                            
                            <form method="POST" id="profilForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-person-fill"></i>
                                            </span>
                                            <input type="text" name="username" id="username" class="form-control" value="<?= $user['username'] ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-envelope-fill"></i>
                                            </span>
                                            <input type="email" class="form-control" value="<?= $user['email'] ?? 'Belum diatur' ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Password Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimal 8 karakter</div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberUsername">
                                        <label class="form-check-label" for="rememberUsername">
                                            Ingat username saya
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="index.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" name="update" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<?php require_once('template/js.php'); ?>

<script>
// Toggle password visibility
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}

// Remember username feature
document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById("rememberUsername");
    const usernameInput = document.getElementById("username");

    // Load saved username if exists
    if (localStorage.getItem("rememberedUsername")) {
        usernameInput.value = localStorage.getItem("rememberedUsername");
        checkbox.checked = true;
    }

    // Update storage when checkbox changes
    checkbox.addEventListener("change", function() {
        if (this.checked) {
            localStorage.setItem("rememberedUsername", usernameInput.value);
        } else {
            localStorage.removeItem("rememberedUsername");
        }
    });

    // Update storage when username changes
    usernameInput.addEventListener("input", function() {
        if (checkbox.checked) {
            localStorage.setItem("rememberedUsername", usernameInput.value);
        }
    });

    // Form validation
    const form = document.getElementById('profilForm');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        
        if (password.length > 0 && password.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password terlalu pendek',
                text: 'Password harus minimal 8 karakter',
            });
        }
    });
});
</script>
</body>
</html>