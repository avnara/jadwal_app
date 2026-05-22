<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_admin'])) {

    header("Location: ../auth/login_admin.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FUNCTION SAFE COUNT
|--------------------------------------------------------------------------
*/

function getTotal(mysqli $conn, string $table): int
{
    $query = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM $table"
    );

    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);

    return (int) ($data['total'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| TOTAL DATA
|--------------------------------------------------------------------------
*/

$totalUser     = getTotal($conn, 'users');
$totalRequest  = getTotal($conn, 'request_perbaikan');
$totalError    = getTotal($conn, 'error_logs');

/*
|--------------------------------------------------------------------------
| USER ONLINE HARI INI
|--------------------------------------------------------------------------
*/

$userOnline = 0;

$onlineQuery = mysqli_query(

    $conn,

    "SELECT COUNT(*) AS total
     FROM login_logs
     WHERE DATE(login_time) = CURDATE()"
);

if ($onlineQuery) {

    $onlineData =
    mysqli_fetch_assoc($onlineQuery);

    $userOnline =
    (int) ($onlineData['total'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| DATA CHART LOGIN
|--------------------------------------------------------------------------
*/

$chartLabels = [];
$chartData   = [];

$chartQuery = mysqli_query(

    $conn,

    "SELECT
        DATE(login_time) AS tanggal,
        COUNT(*) AS total
     FROM login_logs
     GROUP BY DATE(login_time)
     ORDER BY DATE(login_time) ASC
     LIMIT 7"
);

if ($chartQuery) {

    while (
        $row = mysqli_fetch_assoc($chartQuery)
    ) {

        $chartLabels[] = $row['tanggal'];
        $chartData[]   = (int) $row['total'];
    }
}

/*
|--------------------------------------------------------------------------
| ACTIVITY TERBARU
|--------------------------------------------------------------------------
*/

$activityQuery = mysqli_query(

    $conn,

    "SELECT *
     FROM activity_logs
     ORDER BY created_at DESC
     LIMIT 5"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin</title>

<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- Font Awesome -->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<!-- Chart JS -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- CSS -->

<link
rel="stylesheet"
href="../assets/css/style.css">

<style>

body{

    background:#f1f5f9;
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

    display:flex;

    justify-content:space-between;

    align-items:center;

    box-shadow:
    0 5px 20px rgba(0,0,0,0.05);
}

/* CARD */

.card-stat{

    padding:25px;

    border-radius:20px;

    color:white;

    margin-bottom:20px;

    transition:0.3s;

    box-shadow:
    0 5px 20px rgba(0,0,0,0.08);
}

.card-stat:hover{

    transform:translateY(-5px);
}

.card-stat h3{

    font-size:40px;
}

/* COLOR */

.bg-blue{
    background:#2563eb;
}

.bg-green{
    background:#16a34a;
}

.bg-orange{
    background:#ea580c;
}

.bg-purple{
    background:#7c3aed;
}

/* BOX */

.card-box{

    background:white;

    border-radius:20px;

    padding:30px;

    margin-top:20px;

    box-shadow:
    0 5px 20px rgba(0,0,0,0.05);
}

/* TABLE */

.table-modern{

    width:100%;

    border-collapse:collapse;
}

.table-modern th{

    background:#2563eb;

    color:white;

    padding:15px;
}

.table-modern td{

    padding:15px;

    border-bottom:
    1px solid #e5e7eb;
}

.table-modern tr:hover{

    background:#f8fafc;
}

/* RESPONSIVE */

@media(max-width:900px){

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

            Selamat Datang Admin,
            <?= htmlspecialchars($_SESSION['nama_admin']); ?>

        </h3>

        <i class="fa fa-user-shield fa-2x"></i>

    </div>

    <!-- CARD -->

    <div class="row">

        <div class="col-md-3">

            <div class="card-stat bg-blue">

                <h5>Total User</h5>

                <h3>

                    <?= $totalUser; ?>

                </h3>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card-stat bg-green">

                <h5>User Online</h5>

                <h3>

                    <?= $userOnline; ?>

                </h3>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card-stat bg-orange">

                <h5>Permintaan</h5>

                <h3>

                    <?= $totalRequest; ?>

                </h3>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card-stat bg-purple">

                <h5>Error Sistem</h5>

                <h3>

                    <?= $totalError; ?>

                </h3>

            </div>

        </div>

    </div>

    <!-- CHART -->

    <div class="card-box">

        <h3 class="mb-4">

            Statistik Login User

        </h3>

        <canvas id="myChart"></canvas>

    </div>

    <!-- ACTIVITY -->

    <div class="card-box">

        <h3 class="mb-4">

            Activity Terbaru

        </h3>

        <table class="table-modern">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Aktivitas</th>
                    <th>Tanggal</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                $activityQuery &&
                mysqli_num_rows($activityQuery) > 0
            ) : ?>

                <?php while (
                    $activity = mysqli_fetch_assoc($activityQuery)
                ) : ?>

                    <tr>

                        <td>

                            <?= $activity['id']; ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($activity['aktivitas']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($activity['created_at']); ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td colspan="3">

                        Tidak ada activity

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<!-- CHART -->

<script>

const ctx =
document.getElementById('myChart');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels:
        <?= json_encode($chartLabels); ?>,

        datasets: [{

            label: 'Login User',

            data:
            <?= json_encode($chartData); ?>,

            borderWidth: 1
        }]
    },

    options: {

        responsive: true,

        scales: {

            y: {

                beginAtZero: true
            }
        }
    }
});

</script>

</body>
</html>