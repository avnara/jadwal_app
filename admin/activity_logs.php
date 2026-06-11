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
| AMBIL DATA ACTIVITY LOGS
|--------------------------------------------------------------------------
|
| FIX:
| - users.id diganti users.id_user
| - hapus ORDER BY id_log karena kolom tidak ada
| - gunakan created_at jika ada
|
*/

$query = mysqli_query(

    $conn,

    "SELECT

        activity_logs.*,
        users.nama

     FROM activity_logs

     LEFT JOIN users
     ON activity_logs.user_id = users.id_user

     ORDER BY activity_logs.created_at DESC"
);

/*
|--------------------------------------------------------------------------
| CEK ERROR QUERY
|--------------------------------------------------------------------------
*/

if (!$query) {

    die(

        'Query Error: ' .
        mysqli_error($conn)
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Activity Logs</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{

    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial,sans-serif;
    background:#eef2f7;
}

/* SIDEBAR */

.sidebar{

    width:260px;
    height:100vh;
    background:#071739;
    position:fixed;
    top:0;
    left:0;
    overflow-y:auto;
}

.sidebar h2{

    color:white;
    text-align:center;
    padding:25px 0;
    font-size:28px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

.sidebar ul{

    list-style:none;
}

.sidebar ul li{

    border-bottom:1px solid rgba(255,255,255,0.05);
}

.sidebar ul li a{

    display:flex;
    align-items:center;
    gap:12px;

    padding:18px 25px;

    color:white;
    text-decoration:none;

    transition:0.3s;
}

.sidebar ul li a:hover{

    background:#0b244f;
    padding-left:30px;
}

/* MAIN CONTENT */

.main-content{

    margin-left:260px;
    padding:30px;
}

/* TOPBAR */

.topbar{

    background:white;
    border-radius:18px;
    padding:25px;

    margin-bottom:25px;

    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.topbar h3{

    color:#071739;
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

.table-modern th{

    padding:16px;
    text-align:left;
}

.table-modern td{

    padding:16px;
    border-bottom:1px solid #ddd;
}

.table-modern tbody tr:hover{

    background:#f5f7fb;
}

/* EMPTY */

.empty{

    text-align:center;
    color:#777;
    padding:35px;
}

/* BADGE */

.badge-log{

    background:#2563eb;
    color:white;

    padding:6px 12px;

    border-radius:8px;

    font-size:13px;
}

/* RESPONSIVE */

@media(max-width:900px){

    .sidebar{

        width:100%;
        height:auto;
        position:relative;
    }

    .main-content{

        margin-left:0;
    }
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

<!-- MAIN CONTENT -->

<div class="main-content">

    <!-- TOPBAR -->

    <div class="topbar">

        <h3>

            <i class="fa fa-file"></i>
            Activity Logs

        </h3>

    </div>

    <!-- TABLE -->

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>User</th>
                    <th>Aktivitas</th>
                    <th>Tanggal</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($query) > 0
            ) : ?>

                <?php while (
                    $log = mysqli_fetch_assoc($query)
                ) : ?>

                    <tr>

                        <td>

                            <span class="badge-log">

                                <?= htmlspecialchars(
                                    $log['nama'] ?? 'Unknown'
                                ); ?>

                            </span>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $log['aktivitas'] ?? '-'
                            ); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $log['created_at'] ?? '-'
                            ); ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td
                    colspan="3"
                    class="empty">

                        Tidak ada activity logs

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>