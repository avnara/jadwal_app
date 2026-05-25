<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_admin'])) {

    exit('Akses ditolak');
}

$query = mysqli_query(

    $conn,

    "SELECT *
     FROM error_logs
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Error Logs</title>

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

            Error Logs

        </h3>

        <table class="table-modern">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Error</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($query) > 0
            ) : ?>

                <?php while (
                    $error = mysqli_fetch_assoc($query)
                ) : ?>

                    <tr>

                        <td>

                            <?= $error['id']; ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($error['pesan_error']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($error['lokasi_error']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($error['created_at']); ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td colspan="4">

                        Tidak ada error logs

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>