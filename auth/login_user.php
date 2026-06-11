<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    $password = trim($_POST['password'] ?? '');

    if (
        empty($email) ||
        empty($password)
    ) {

        $error = 'Email dan password wajib diisi';

    } else {

        /*
        |--------------------------------------------------------------------------
        | CEK USER
        |--------------------------------------------------------------------------
        */

        $query = mysqli_prepare(

            $conn,

            "SELECT *
             FROM users
             WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $query,
            "s",
            $email
        );

        mysqli_stmt_execute($query);

        $result =
        mysqli_stmt_get_result($query);

        /*
        |--------------------------------------------------------------------------
        | USER DITEMUKAN
        |--------------------------------------------------------------------------
        */

        if (
            mysqli_num_rows($result) > 0
        ) {

            $user =
            mysqli_fetch_assoc($result);

            /*
            |--------------------------------------------------------------------------
            | CEK PASSWORD HASH
            |--------------------------------------------------------------------------
            */

            if (
                password_verify(
                    $password,
                    $user['password']
                )
            ) {

                $_SESSION['id_user'] =
                $user['id_user'];

                $_SESSION['nama'] =
                $user['nama'];

                $_SESSION['role'] =
                $user['role'];

                header(
                    "Location: ../user/dashboard.php"
                );

                exit;

            } else {

                $error = 'Password salah';
            }

        } else {

            $error = 'Email tidak ditemukan';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Login User</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background:#eef2f7;
    font-family:Arial,sans-serif;
}

.login-box{

    max-width:420px;
    margin:80px auto;
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

</style>

</head>

<body>

<div class="login-box">

    <h2 class="mb-4 text-center">

        Login User

    </h2>

    <?php if (!empty($error)) : ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">

            <label>Email</label>

            <input
            type="email"
            name="email"
            class="form-control"
            required>

        </div>

        <div class="mb-4">

            <label>Password</label>

            <input
            type="password"
            name="password"
            class="form-control"
            required>

        </div>

        <button
        type="submit"
        class="btn btn-primary w-100">

            Login

        </button>

    </form>

</div>

</body>
</html>