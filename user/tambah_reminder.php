<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul = trim($_POST['judul'] ?? '');

    $deskripsi = trim(
        $_POST['deskripsi'] ?? ''
    );

    $deadline = $_POST['deadline'] ?? '';

    $query = mysqli_prepare(
        $conn,
        "INSERT INTO reminder
        (
            user_id,
            judul,
            deskripsi,
            deadline
        )
        VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $query,
        "isss",
        $idUser,
        $judul,
        $deskripsi,
        $deadline
    );

    if (mysqli_stmt_execute($query)) {

        header("Location: reminder.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Tambah Reminder</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow p-4 rounded-4">

<h3 class="mb-4">
Tambah Reminder
</h3>

<form method="POST">

<label>Judul</label>

<input
type="text"
name="judul"
class="form-control mb-3"
required>

<label>Deskripsi</label>

<textarea
name="deskripsi"
class="form-control mb-3"></textarea>

<label>Deadline</label>

<input
type="datetime-local"
name="deadline"
class="form-control mb-3"
required>

<button
type="submit"
class="btn btn-primary w-100">

Simpan Reminder

</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>