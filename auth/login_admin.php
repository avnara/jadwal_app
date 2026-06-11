<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {

        $error = "Semua field wajib diisi";

    } else {

        $query = mysqli_prepare(
            $conn,
            "SELECT * FROM admins WHERE username = ? LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $query,
            "s",
            $username
        );

        mysqli_stmt_execute($query);

        $result = mysqli_stmt_get_result($query);

        $admin = mysqli_fetch_assoc($result);

        /*
        DEBUG CEK DATA
        */

        if (!$admin) {

            $error = "Username admin tidak ditemukan";

        } else {

            /*
            SEMENTARA PAKAI PASSWORD BIASA
            karena database kamu masih plain text
            */

            if ($password === $admin['password']) {

                $_SESSION['id_admin'] =
                    $admin['id_admin'];

                $_SESSION['username_admin'] =
                    $admin['username'];

                $_SESSION['nama_admin'] =
                    $admin['nama_admin'];

                header(
                    "Location: ../admin/dashboard.php"
                );

                exit;

            } else {

                $error = "Password admin salah";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Login Admin</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    margin:0;
    height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:
    linear-gradient(
    135deg,
    #111827,
    #1e3a8a
    );

    font-family:Arial;
}

.login-box{

    width:400px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.2);
}

.login-box h2{

    text-align:center;
    margin-bottom:30px;
}

.btn-login{

    width:100%;
    padding:12px;

    background:#111827;
    color:white;

    border:none;

    border-radius:10px;

    font-weight:bold;
}

.error-box{

    background:#fee2e2;

    color:#b91c1c;

    padding:10px;

    border-radius:10px;

    margin-bottom:20px;
}

</style>

</head>

<body>

<div class="login-box">

    <h2>Login Admin</h2>

    <?php if ($error !== '') : ?>

        <div class="error-box">

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">

            <input
            type="text"
            name="username"
            class="form-control"
            placeholder="Username Admin"
            required>

        </div>

        <div class="mb-3">

            <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Password"
            required>

        </div>

        <button
        type="submit"
        class="btn-login">

            Login Admin

        </button>

    </form>

</div>

</body>
</html>