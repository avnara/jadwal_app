<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$error = '';
$success = '';

/*
|--------------------------------------------------------------------------
| SIMPAN REMINDER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul = trim($_POST['judul'] ?? '');

    $deskripsi = trim(
        $_POST['deskripsi'] ?? ''
    );

    $deadline = trim(
        $_POST['deadline'] ?? ''
    );

    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if (
        empty($judul) ||
        empty($deadline)
    ) {

        $error =
        'Judul dan deadline wajib diisi';
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT DATABASE
    |--------------------------------------------------------------------------
    */

    if (empty($error)) {

        $query = mysqli_prepare(

            $conn,

            "INSERT INTO reminder
            (
                user_id,
                judul,
                deskripsi,
                deadline,
                status
            )
            VALUES
            (
                ?, ?, ?, ?, 'belum'
            )"
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

            $success =
            'Reminder berhasil ditambahkan';

        } else {

            $error =
            'Gagal menambahkan reminder';
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

<title>Tambah Reminder</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{

    background:#eef2f7;
    font-family:Arial,sans-serif;
}

.card-reminder{

    border:none;
    border-radius:20px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.btn-save{

    background:#2563eb;
    border:none;
    padding:12px;
    border-radius:12px;
    font-weight:bold;
}

.btn-save:hover{

    background:#1d4ed8;
}

</style>

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-reminder">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <h2>

                        <i class="fa fa-bell"></i>
                        Tambah Reminder

                    </h2>

                    <a
                    href="reminder.php"
                    class="btn btn-secondary">

                        Kembali

                    </a>

                </div>

                <?php if (!empty($error)) : ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>

                <?php if (!empty($success)) : ?>

                    <div class="alert alert-success">

                        <?= htmlspecialchars($success); ?>

                    </div>

                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">

                            Judul Reminder

                        </label>

                        <input
                        type="text"
                        name="judul"
                        class="form-control"
                        required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Deskripsi

                        </label>

                        <textarea
                        name="deskripsi"
                        class="form-control"
                        rows="4"></textarea>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">

                            Deadline

                        </label>

                        <input
                        type="datetime-local"
                        name="deadline"
                        class="form-control"
                        required>

                    </div>

                    <button
                    type="submit"
                    class="btn btn-primary w-100 btn-save">

                        <i class="fa fa-save"></i>
                        Simpan Reminder

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>