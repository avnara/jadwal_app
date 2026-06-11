<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $namaMapel = trim($_POST['nama_mapel'] ?? '');
    $guru = trim($_POST['guru'] ?? '');
    $ruangan = trim($_POST['ruangan'] ?? '');
    $hari = trim($_POST['hari'] ?? '');

    if (
        empty($namaMapel) ||
        empty($guru)
    ) {

        $error = 'Mata pelajaran dan guru wajib diisi';
    }

    if (empty($error)) {

        $query = mysqli_prepare(

            $conn,

            "INSERT INTO mata_pelajaran
            (
                user_id,
                nama_mapel,
                guru,
                ruangan,
                hari
            )
            VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(

            $query,
            "issss",
            $idUser,
            $namaMapel,
            $guru,
            $ruangan,
            $hari
        );

        if (mysqli_stmt_execute($query)) {

            header("Location: mata_pelajaran.php");
            exit;
        }

        $error = 'Gagal menambah mata pelajaran';
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Tambah Mata Pelajaran</title>

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
Tambah Mata Pelajaran
</h3>

<?php if (!empty($error)) : ?>

<div class="alert alert-danger">

    <?= htmlspecialchars($error); ?>

</div>

<?php endif; ?>

<form method="POST">

<label class="mb-2">
Mata Pelajaran
</label>

<input
type="text"
name="nama_mapel"
class="form-control mb-3"
required>

<label class="mb-2">
Guru
</label>

<input
type="text"
name="guru"
class="form-control mb-3"
required>

<label class="mb-2">
Ruangan
</label>

<input
type="text"
name="ruangan"
class="form-control mb-3">

<label class="mb-2">
Hari
</label>

<select
name="hari"
class="form-control mb-4">

<option value="">Pilih Hari</option>
<option>Senin</option>
<option>Selasa</option>
<option>Rabu</option>
<option>Kamis</option>
<option>Jumat</option>
<option>Sabtu</option>

</select>

<button
type="submit"
class="btn btn-primary w-100">

Simpan

</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>