<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_admin'])) {

    header("Location: ../auth/login_admin.php");
    exit;
}

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM activity_logs
     ORDER BY waktu DESC"
);

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Activity Logs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="sidebar">

    <h2>Admin Panel</h2>

</div>

<div class="main-content">

    <div class="topbar">

        <h4>Activity Logs</h4>

    </div>

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Admin</th>
                    <th>User</th>
                    <th>Aktivitas</th>
                    <th>Waktu</th>

                </tr>

            </thead>

            <tbody>

            <?php while($log = mysqli_fetch_assoc($query)) : ?>

                <tr>

                    <td>
                        <?= $log['id_log']; ?>
                    </td>

                    <td>
                        <?= $log['admin_id']; ?>
                    </td>

                    <td>
                        <?= $log['user_id']; ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($log['aktivitas']); ?>
                    </td>

                    <td>
                        <?= $log['waktu']; ?>
                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>