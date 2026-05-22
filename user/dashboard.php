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
| TOTAL JADWAL
|--------------------------------------------------------------------------
*/

$qJadwal = mysqli_prepare(

    $conn,

    "SELECT COUNT(*) AS total
     FROM jadwal
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $qJadwal,
    "i",
    $idUser
);

mysqli_stmt_execute($qJadwal);

$rJadwal =
mysqli_stmt_get_result($qJadwal);

$totalJadwal =
mysqli_fetch_assoc($rJadwal)['total'];

/*
|--------------------------------------------------------------------------
| TOTAL TUGAS
|--------------------------------------------------------------------------
*/

$qTugas = mysqli_prepare(

    $conn,

    "SELECT COUNT(*) AS total
     FROM tugas
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $qTugas,
    "i",
    $idUser
);

mysqli_stmt_execute($qTugas);

$rTugas =
mysqli_stmt_get_result($qTugas);

$totalTugas =
mysqli_fetch_assoc($rTugas)['total'];

/*
|--------------------------------------------------------------------------
| TOTAL REMINDER
|--------------------------------------------------------------------------
*/

$qReminder = mysqli_prepare(

    $conn,

    "SELECT COUNT(*) AS total
     FROM reminder
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $qReminder,
    "i",
    $idUser
);

mysqli_stmt_execute($qReminder);

$rReminder =
mysqli_stmt_get_result($qReminder);

$totalReminder =
mysqli_fetch_assoc($rReminder)['total'];

/*
|--------------------------------------------------------------------------
| TOTAL MATA PELAJARAN
|--------------------------------------------------------------------------
*/

$qMapel = mysqli_prepare(

    $conn,

    "SELECT COUNT(*) AS total
     FROM mata_pelajaran
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $qMapel,
    "i",
    $idUser
);

mysqli_stmt_execute($qMapel);

$rMapel =
mysqli_stmt_get_result($qMapel);

$totalMapel =
mysqli_fetch_assoc($rMapel)['total'];

/*
|--------------------------------------------------------------------------
| JADWAL HARI INI
|--------------------------------------------------------------------------
*/

$hariIni = date('l');

$hariIndonesia = [

    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu'
];

$hari = $hariIndonesia[$hariIni];

$qHariIni = mysqli_prepare(

    $conn,

    "SELECT *
     FROM jadwal
     WHERE user_id = ?
     AND hari = ?
     ORDER BY jam_mulai ASC"
);

mysqli_stmt_bind_param(
    $qHariIni,
    "is",
    $idUser,
    $hari
);

mysqli_stmt_execute($qHariIni);

$dataHariIni =
mysqli_stmt_get_result($qHariIni);

?>

<?php

$notifQuery = mysqli_prepare(

    $conn,

    "SELECT COUNT(*) AS total
     FROM tugas
     WHERE user_id = ?
     AND status = 'belum'
     AND deadline <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)"
);

mysqli_stmt_bind_param(

    $notifQuery,
    "i",
    $_SESSION['id_user']
);

mysqli_stmt_execute($notifQuery);

$notifResult =
mysqli_stmt_get_result($notifQuery);

$totalNotif =
mysqli_fetch_assoc($notifResult);
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dashboard User</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link
rel="stylesheet"
href="../assets/css/style.css">

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

            <a href="mata_pelajaran.php">

                <i class="fa fa-book"></i>
                Mata Pelajaran

            </a>

        </li>

        <li>

            <a href="reminder.php">

                <i class="fa fa-bell"></i>

                Reminder

                <?php if ($totalReminder > 0) : ?>

                    <span class="notif-badge">

                        <?= $totalReminder; ?>

                    </span>

                <?php endif; ?>

            </a>

        </li>

        <li>

            <a href="tugas.php">

                <i class="fa fa-tasks"></i>
                Tugas

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

            Selamat Datang,
            <?= htmlspecialchars($_SESSION['nama']); ?>

        </h3>

        <div>

            <i class="fa fa-user-circle fa-2x"></i>

        </div>

    </div>

    <!-- CARD -->

    <div class="row">

        <div class="col-md-3">

            <div class="card-stat bg-blue">

                <h5>Total Jadwal</h5>

                <h3>

                    <?= $totalJadwal; ?>

                </h3>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card-stat bg-green">

                <h5>Total Tugas</h5>

                <h3>

                    <?= $totalTugas; ?>

                </h3>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card-stat bg-orange">

                <h5>Reminder</h5>

                <h3>

                    <?= $totalReminder; ?>

                </h3>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card-stat bg-purple">

                <h5>Mata Pelajaran</h5>

                <h3>

                    <?= $totalMapel; ?>

                </h3>

            </div>

        </div>

    </div>

    <!-- TABEL -->

    <div class="card-box">

        <div class="d-flex justify-content-between mb-4">

            <h3>

                Jadwal Hari Ini

            </h3>

            <a
            href="tambah_jadwal.php"
            class="btn-modern">

                Tambah Jadwal

            </a>

        </div>

        <table class="table-modern">

            <thead>

                <tr>

                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($dataHariIni) > 0
            ) : ?>

                <?php while (
                    $data = mysqli_fetch_assoc($dataHariIni)
                ) : ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($data['hari']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($data['jam_mulai']); ?>

                        </td>

                        <td>

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

                    <td colspan="6">

                        Tidak ada jadwal hari ini

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>