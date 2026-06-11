<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_SESSION['id_user'];

    $hari = trim($_POST['hari']);

    $jamMulai = trim($_POST['jam_mulai']);

    $jamSelesai = trim($_POST['jam_selesai']);

    $mapel = trim($_POST['mata_pelajaran']);

    $guru = trim($_POST['guru']);

    $ruangan = trim($_POST['ruangan']);

    $query = mysqli_prepare(

        $conn,

        "INSERT INTO jadwal (

            user_id,
            hari,
            jam_mulai,
            jam_selesai,
            mata_pelajaran,
            guru,
            ruangan

        )

        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(

        $query,
        "issssss",

        $userId,
        $hari,
        $jamMulai,
        $jamSelesai,
        $mapel,
        $guru,
        $ruangan
    );

    mysqli_stmt_execute($query);

    header("Location: jadwal.php");
    exit;
}

$activity = mysqli_prepare(

    $conn,

    "INSERT INTO activity_logs (

        user_id,
        aktivitas

    )

    VALUES (?, ?)"
);

$aktivitas =
"Menambahkan jadwal baru";

mysqli_stmt_bind_param(

    $activity,
    "is",

    $_SESSION['id_user'],
    $aktivitas
);

mysqli_stmt_execute($activity);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Tambah Jadwal</title>

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

            Tambah Jadwal

        </h3>

        <form method="POST">

            <div class="mb-3">

                <label>Hari</label>

                <select
                name="hari"
                class="form-control"
                required>

                    <option value="">Pilih Hari</option>

                    <option>Senin</option>
                    <option>Selasa</option>
                    <option>Rabu</option>
                    <option>Kamis</option>
                    <option>Jumat</option>
                    <option>Sabtu</option>

                </select>

            </div>

            <div class="mb-3">

                <label>Jam Mulai</label>

                <input
                type="time"
                name="jam_mulai"
                class="form-control"
                required>

            </div>

            <div class="mb-3">

                <label>Jam Selesai</label>

                <input
                type="time"
                name="jam_selesai"
                class="form-control"
                required>

            </div>

            <div class="mb-3">

                <label>Mata Pelajaran</label>

                <input
                type="text"
                name="mata_pelajaran"
                class="form-control"
                required>

            </div>

            <div class="mb-3">

                <label>Guru</label>

                <input
                type="text"
                name="guru"
                class="form-control"
                required>

            </div>

            <div class="mb-3">

                <label>Ruangan</label>

                <input
                type="text"
                name="ruangan"
                class="form-control"
                required>

            </div>

            <button
            type="submit"
            class="btn btn-primary">

                Simpan

            </button>

            <a
            href="jadwal.php"
            class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>

</body>
</html>