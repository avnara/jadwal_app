<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    exit('Akses ditolak');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_SESSION['id_user'];

    $judul = trim($_POST['judul']);

    $deskripsi = trim($_POST['deskripsi']);

    $deadline = trim($_POST['deadline']);

    $query = mysqli_prepare(

        $conn,

        "INSERT INTO tugas (

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

        $userId,
        $judul,
        $deskripsi,
        $deadline
    );

    mysqli_stmt_execute($query);

    header("Location: tugas.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Tambah Tugas</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="../assets/css/style.css">

</head>

<body>

<div class="main-content">

    <div class="card-box">

        <h3 class="mb-4">

            Tambah Tugas

        </h3>

        <form method="POST">

            <div class="mb-3">

                <label>Judul Tugas</label>

                <input
                type="text"
                name="judul"
                class="form-control"
                required>

            </div>

            <div class="mb-3">

                <label>Deskripsi</label>

                <textarea
                name="deskripsi"
                class="form-control"
                rows="5"></textarea>

            </div>

            <div class="mb-3">

                <label>Deadline</label>

                <input
                type="date"
                name="deadline"
                class="form-control"
                required>

            </div>

            <button
            type="submit"
            class="btn btn-primary">

                Simpan

            </button>

            <a
            href="tugas.php"
            class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>

</body>
</html>