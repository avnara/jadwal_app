<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_admin'])) {

    exit('Akses ditolak');
}

$query = mysqli_query(

    $conn,

    "SELECT

        activity_logs.*,
        users.nama

     FROM activity_logs

     LEFT JOIN users
     ON activity_logs.user_id = users.id

     ORDER BY activity_logs.id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Activity Logs</title>

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

            Activity Logs

        </h3>

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

                            <?= htmlspecialchars($log['nama'] ?? 'Unknown'); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($log['aktivitas']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($log['created_at']); ?>

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

</body>
</html>