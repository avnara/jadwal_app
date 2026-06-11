<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_admin'])) {

    header("Location: ../auth/login_admin.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM users WHERE id_user = ?"
);

mysqli_stmt_bind_param($query, "i", $id);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    $update = mysqli_prepare(
        $conn,
        "UPDATE users
         SET
            nama = ?,
            email = ?,
            tingkat = ?,
            jurusan = ?
         WHERE id_user = ?"
    );

    mysqli_stmt_bind_param(
        $update,
        "ssssi",
        $nama,
        $email,
        $tingkat,
        $jurusan,
        $id
    );

    if (mysqli_stmt_execute($update)) {

        /* ACTIVITY LOG */

        $adminId = $_SESSION['id_admin'];

        $log = mysqli_prepare(
            $conn,
            "INSERT INTO activity_logs
            (
                admin_id,
                user_id,
                aktivitas
            )
            VALUES (?, ?, ?)"
        );

        $aktivitas =
            "Admin mengedit data user ID "
            . $id;

        mysqli_stmt_bind_param(
            $log,
            "iis",
            $adminId,
            $id,
            $aktivitas
        );

        mysqli_stmt_execute($log);

        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Edit User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow p-4 rounded-4">

<h3 class="mb-4">
Edit User
</h3>

<form method="POST">

<input
type="text"
name="nama"
class="form-control mb-3"
value="<?= htmlspecialchars($user['nama']); ?>"
required
>

<input
type="email"
name="email"
class="form-control mb-3"
value="<?= htmlspecialchars($user['email']); ?>"
required
>

<input
type="text"
name="tingkat"
class="form-control mb-3"
value="<?= htmlspecialchars($user['tingkat']); ?>"
>

<input
type="text"
name="jurusan"
class="form-control mb-3"
value="<?= htmlspecialchars($user['jurusan']); ?>"
>

<button type="submit" class="btn btn-warning w-100">
Update User
</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>