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

/*
|--------------------------------------------------------------------------
| AMBIL DATA JADWAL USER
|--------------------------------------------------------------------------
*/

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM jadwal
     WHERE user_id = ?
     ORDER BY FIELD(
        hari,
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu'
     ),
     jam_mulai ASC"
);

mysqli_stmt_bind_param(

    $query,
    "i",
    $idUser
);

mysqli_stmt_execute($query);

$result =
mysqli_stmt_get_result($query);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Jadwal Saya</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{

    margin:0;
    font-family:Arial, sans-serif;
    background:#eef2f7;
}

/* SIDEBAR */

.sidebar{

    width:260px;
    height:100vh;
    background:#071739;
    position:fixed;
    left:0;
    top:0;
    padding-top:20px;
}

.sidebar h2{

    color:white;
    text-align:center;
    margin-bottom:40px;
}

.sidebar ul{

    list-style:none;
    padding:0;
}

.sidebar ul li{

    padding:15px 25px;
}

.sidebar ul li:hover{

    background:#0b244f;
}

.sidebar ul li a{

    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:12px;
    font-size:17px;
}

/* MAIN CONTENT */

.main-content{

    margin-left:260px;
    padding:30px;
}

/* TOPBAR */

.topbar{

    background:white;
    padding:25px;
    border-radius:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    flex-wrap:wrap;
    gap:10px;
}

/* CARD */

.card-box{

    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* TABLE */

.table-modern{

    width:100%;
    border-collapse:collapse;
}

.table-modern thead{

    background:#2563eb;
    color:white;
}

.table-modern th,
.table-modern td{

    padding:16px;
    text-align:left;
}

.table-modern tbody tr{

    border-bottom:1px solid #ddd;
}

/* BUTTON */

.btn-modern{

    background:#2563eb;
    color:white;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
    border:none;
}

.btn-modern:hover{

    background:#1e40af;
    color:white;
}

.action-btn{

    padding:7px 12px;
    border-radius:8px;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.btn-edit{

    background:#2563eb;
}

.btn-delete{

    background:#dc2626;
}

.empty{

    text-align:center;
    padding:30px;
    color:#777;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <h2>Jadwal App</h2>

    <ul>

        <li>

            <a href="dashboard.php">

                <i class="fa fa-home"></i>
                Dashboard

            </a>

        </li>

        <li>

            <a href="jadwal.php">

                <i class="fa fa-calendar"></i>
                Jadwal Saya

            </a>

        </li>

        <li>

            <a href="kalender.php">

                <i class="fa fa-calendar-days"></i>
                Kalender

            </a>

        </li>

        <li>

            <a href="mata_pelajaran.php">

                <i class="fa fa-book"></i>
                Mata Pelajaran

            </a>

        </li>

        <li>

            <a href="reminder.php">

                <i class="fa fa-bell"></i>
                Reminder

            </a>

        </li>

        <li>

            <a href="../auth/logout.php">

                <i class="fa fa-sign-out-alt"></i>
                Logout

            </a>

        </li>

    </ul>

</div>

<!-- MAIN -->

<div class="main-content">

    <!-- TOPBAR -->

    <div class="topbar">

        <h3>

            Jadwal Saya

        </h3>

        <div class="d-flex gap-2 flex-wrap">

            <a
            href="tambah_jadwal.php"
            class="btn-modern">

                <i class="fa fa-plus"></i>
                Tambah Jadwal

            </a>

            <a
            href="export_jadwal.php"
            class="btn btn-danger">

                <i class="fa fa-file-pdf"></i>
                Export PDF

            </a>

            <a
            href="print_jadwal.php"
            class="btn btn-success">

                <i class="fa fa-print"></i>
                Print

            </a>

        </div>

    </div>

    <!-- TABLE -->

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($result) > 0
            ) : ?>

                <?php while (
                    $data = mysqli_fetch_assoc($result)
                ) : ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($data['hari']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($data['jam_mulai']); ?>
                            -
                            <?= htmlspecialchars($data['jam_selesai']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($data['mata_pelajaran']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($data['guru']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($data['ruangan']); ?>

                        </td>

                        <td>

                        <?php if (
                            !empty($data['id_jadwal'])
                        ) : ?>

                            <a
                            href="edit_jadwal.php?id=<?= $data['id_jadwal']; ?>"
                            class="action-btn btn-edit">

                                Edit

                            </a>

                            <a
                            href="hapus_jadwal.php?id=<?= $data['id_jadwal']; ?>"
                            class="action-btn btn-delete"
                            onclick="return confirm('Hapus jadwal ini?')">

                                Hapus

                            </a>

                        <?php else : ?>

                            <span style="color:red;">

                                ID tidak ditemukan

                            </span>

                        <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td
                    colspan="6"
                    class="empty">

                        Belum ada jadwal

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>