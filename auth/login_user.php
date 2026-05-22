<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $query = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($query, "s", $email);

    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);

    $user = mysqli_fetch_assoc($result);

    if ($user) {

        if (password_verify($password, $user['password'])) {

            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = 'user';

            header("Location: ../user/dashboard.php");
            exit;

        } else {

            echo "<script>alert('Password salah');</script>";

        }

    } else {

        echo "<script>alert('Email tidak ditemukan');</script>";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-4">

<div class="card shadow p-4 rounded-4">

<h3 class="text-center mb-4">
Login Pengguna
</h3>

<form method="POST">

<input
type="email"
name="email"
class="form-control mb-3"
placeholder="Email"
required
>

<input
type="password"
name="password"
class="form-control mb-3"
placeholder="Password"
required
>

<button
type="submit"
class="btn btn-primary w-100"
>
Login
</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>