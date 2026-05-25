<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

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
     ON jadwal.user_id = users.id

     ORDER BY jadwal.id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Jadwal User</title>

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

    </ul>

</div>

<!-- MAIN -->

<div class="main-content">

    <div class="topbar">

        <h3>Data Jadwal User</h3>

    </div>

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>User</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mapel</th>
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

                    <td colspan="6">

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