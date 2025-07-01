<?php
session_start();
include "config.php";

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = mysqli_prepare($koneksi, "SELECT id, username, password, role FROM user WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        echo "<script>
            alert('Login Berhasil');
            window.location.href = '../admin/index.php';
        </script>";
    } else {
        echo "<script>
            alert('Login gagal! Cek kembali username dan password.');
            window.location.href = '../login.php';
        </script>";
    }

} else {
    echo "<script>
        alert('Login gagal! Cek kembali username dan password.');
        window.location.href = '../login.php';
    </script>";
}
?>
