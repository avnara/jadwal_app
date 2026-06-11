<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idMapel = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM mata_pelajaran
     WHERE id_mapel = ?"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $idMapel
);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

$data = mysqli_fetch_assoc($result);

if (!$data) {

    exit('Data tidak ditemukan');
}

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

        $error = 'Field wajib diisi';
    }

    if (empty($error)) {

        $update = mysqli_prepare(

            $conn,

            "UPDATE mata_pelajaran
             SET
                nama_mapel = ?,
                guru = ?,
                ruangan = ?,
                hari = ?
             WHERE id_mapel = ?"
        );

        mysqli_stmt_bind_param(

            $update,
            "ssssi",
            $namaMapel,
            $guru,
            $ruangan,
            $hari,
            $idMapel
        );

        if (mysqli_stmt_execute($update)) {

            header("Location: mata_pelajaran.php");
            exit;
        }

        $error = 'Gagal update data';
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Edit Mata Pelajaran</title>

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
Edit Mata Pelajaran
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
value="<?= htmlspecialchars($data['nama_mapel']); ?>"
required>

<label class="mb-2">
Guru
</label>

<input
type="text"
name="guru"
class="form-control mb-3"
value="<?= htmlspecialchars($data['guru']); ?>"
required>

<label class="mb-2">
Ruangan
</label>

<input
type="text"
name="ruangan"
class="form-control mb-3"
value="<?= htmlspecialchars($data['ruangan']); ?>">

<label class="mb-2">
Hari
</label>

<input
type="text"
name="hari"
class="form-control mb-4"
value="<?= htmlspecialchars($data['hari']); ?>">

<button
type="submit"
class="btn btn-primary w-100">

Update

</button>

</form>

</div>
</div>
</div>
</div>

</body>
</html>