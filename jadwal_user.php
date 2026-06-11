<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN ADMIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_admin'])) {

    header("Location: ../auth/login_admin.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA JADWAL USER
|--------------------------------------------------------------------------
*/

$query = mysqli_query(

    $conn,

    "SELECT

        jadwal.*,
        users.nama

     FROM jadwal

     INNER JOIN users
     ON jadwal.user_id = users.id_user

     ORDER BY jadwal.id_jadwal DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Jadwal User</title>

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
    margin-bottom:30px;
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

    <h2>Admin Panel</h2>

    <ul>

        <li>

            <a href="dashboard.php">

                <i class="fa fa-chart-line"></i>
                Dashboard

            </a>

        </li>

        <li>

            <a href="users.php">

                <i class="fa fa-users"></i>
                Kelola User

            </a>

        </li>

        <li>

            <a href="jadwal_user.php">

                <i class="fa fa-calendar"></i>
                Jadwal User

            </a>

        </li>

        <li>

            <a href="activity_logs.php">

                <i class="fa fa-file"></i>
                Activity Logs

            </a>

        </li>

        <li>

            <a href="error_logs.php">

                <i class="fa fa-bug"></i>
                Error Logs

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

        <h3>

            Data Jadwal User

        </h3>

    </div>

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>User</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($query) > 0
            ) : ?>

                <?php while (
                    $data = mysqli_fetch_assoc($query)
                ) : ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($data['nama']); ?>

                        </td>

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

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td
                    colspan="6"
                    class="empty">

                        Belum ada data jadwal

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>