<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passwordInput = $_POST['password'] ?? '';
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if (
        empty($nama) ||
        empty($email) ||
        empty($passwordInput)
    ) {
        die("Semua field wajib diisi");
    }

    $check = mysqli_prepare(
        $conn,
        "SELECT id_user FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);

    $result = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($result) > 0) {
        die("Email sudah digunakan");
    }

    $passwordHash = password_hash(
        $passwordInput,
        PASSWORD_DEFAULT
    );

    $query = mysqli_prepare(
        $conn,
        "INSERT INTO users 
        (nama, email, password, tingkat, jurusan)
        VALUES (?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $query,
        "sssss",
        $nama,
        $email,
        $passwordHash,
        $tingkat,
        $jurusan
    );

    if (mysqli_stmt_execute($query)) {

        header("Location: login_user.php");
        exit;

    } else {

        echo "Register gagal";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow p-4 rounded-4">

<h3 class="text-center mb-4">
Register Pengguna
</h3>

<form method="POST">

<input
type="text"
name="nama"
class="form-control mb-3"
placeholder="Nama Lengkap"
required
>

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

<input
type="text"
name="tingkat"
class="form-control mb-3"
placeholder="Kelas / Tingkat"
>

<input
type="text"
name="jurusan"
class="form-control mb-3"
placeholder="Jurusan"
>

<button
type="submit"
class="btn btn-success w-100"
>
Register
</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>