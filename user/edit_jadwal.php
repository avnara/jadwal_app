<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM jadwal
     WHERE id_jadwal = ?
     AND user_id = ?"
);

mysqli_stmt_bind_param($query, "ii", $id, $idUser);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

$data = mysqli_fetch_assoc($result);

if (!$data) {

    die("Data tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tanggal = $_POST['tanggal'] ?? '';
    $jamMulai = $_POST['jam_mulai'] ?? '';
    $jamSelesai = $_POST['jam_selesai'] ?? '';
    $mapel = trim($_POST['mata_pelajaran'] ?? '');
    $guru = trim($_POST['guru'] ?? '');
    $ruangan = trim($_POST['ruangan'] ?? '');

    $update = mysqli_prepare(
        $conn,
        "UPDATE jadwal
         SET
            tanggal = ?,
            jam_mulai = ?,
            jam_selesai = ?,
            mata_pelajaran = ?,
            guru = ?,
            ruangan = ?
         WHERE id_jadwal = ?
         AND user_id = ?"
    );

    mysqli_stmt_bind_param(
        $update,
        "ssssssii",
        $tanggal,
        $jamMulai,
        $jamSelesai,
        $mapel,
        $guru,
        $ruangan,
        $id,
        $idUser
    );

    if (mysqli_stmt_execute($update)) {

        header("Location: jadwal.php");
        exit;

    } else {

        echo "Gagal update data";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Edit Jadwal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow p-4 rounded-4">

<h3 class="mb-4">
Edit Jadwal
</h3>

<form method="POST">

<label>Tanggal</label>

<input
type="date"
name="tanggal"
class="form-control mb-3"
value="<?= htmlspecialchars($data['tanggal']); ?>"
required
>

<label>Jam Mulai</label>

<input
type="time"
name="jam_mulai"
class="form-control mb-3"
value="<?= htmlspecialchars($data['jam_mulai']); ?>"
required
>

<label>Jam Selesai</label>

<input
type="time"
name="jam_selesai"
class="form-control mb-3"
value="<?= htmlspecialchars($data['jam_selesai']); ?>"
required
>

<label>Mata Pelajaran</label>

<input
type="text"
name="mata_pelajaran"
class="form-control mb-3"
value="<?= htmlspecialchars($data['mata_pelajaran']); ?>"
required
>

<label>Guru</label>

<input
type="text"
name="guru"
class="form-control mb-3"
value="<?= htmlspecialchars($data['guru']); ?>"
required
>

<label>Ruangan</label>

<input
type="text"
name="ruangan"
class="form-control mb-3"
value="<?= htmlspecialchars($data['ruangan']); ?>"
>

<button type="submit" class="btn btn-warning w-100">
Update Jadwal
</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>