<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| AMBIL REMINDER USER
|--------------------------------------------------------------------------
*/

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM reminder
     WHERE user_id = ?
     ORDER BY deadline ASC"
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

<title>Reminder</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{

    margin:0;
    font-family:Arial,sans-serif;
    background:#eef2f7;
}

/* SIDEBAR */

.sidebar{

    width:260px;
    height:100vh;
    background:#071739;
    position:fixed;
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

.sidebar ul li a{

    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:12px;
    font-size:18px;
}

.sidebar ul li:hover{

    background:#0b244f;
}

/* MAIN */

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
}

/* CARD */

.card-box{

    background:white;
    padding:30px;
    border-radius:20px;
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

    padding:18px;
}

.table-modern tbody tr{

    border-bottom:1px solid #ddd;
}

/* BADGE */

.badge-status{

    padding:6px 12px;
    border-radius:10px;
    color:white;
    font-size:12px;
}

.belum{

    background:#dc2626;
}

.selesai{

    background:#16a34a;
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
                Jadwal

            </a>

        </li>

        <li>

            <a href="kalender.php">

                <i class="fa fa-calendar-days"></i>
                Kalender

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

    <div class="topbar">

        <h4>

            Reminder Tugas

        </h4>

        <a
        href="tambah_reminder.php"
        class="btn btn-primary">

            <i class="fa fa-plus"></i>
            Tambah Reminder

        </a>

    </div>

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>Judul</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($result) > 0
            ) : ?>

                <?php while(
                    $data = mysqli_fetch_assoc($result)
                ) : ?>

                    <tr>

                        <td>

                            <strong>

                                <?= htmlspecialchars($data['judul']); ?>

                            </strong>

                            <br>

                            <small>

                                <?= htmlspecialchars($data['deskripsi']); ?>

                            </small>

                        </td>

                        <td>

                            <?php

                            if (!empty($data['deadline'])) {

                                echo date(
                                    'd M Y H:i',
                                    strtotime($data['deadline'])
                                );

                            } else {

                                echo '-';
                            }

                            ?>

                        </td>

                        <td>

                            <span class="
                            badge-status
                            <?= htmlspecialchars($data['status']); ?>
                            ">

                                <?= ucfirst(htmlspecialchars($data['status'])); ?>

                            </span>

                        </td>

                        <td>

                            <?php if (
                                $data['status'] === 'belum'
                            ) : ?>

                                <a
                                href="selesai_reminder.php?id=<?= $data['id_reminder']; ?>"
                                class="btn btn-success btn-sm">

                                    Selesai

                                </a>

                            <?php else : ?>

                                <span class="text-success">

                                    Sudah selesai

                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td colspan="4" class="empty">

                        Tidak ada reminder

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>