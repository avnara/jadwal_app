<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM reminder
     WHERE user_id = ?
     ORDER BY deadline ASC"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $idUser
);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Reminder</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet"
href="../assets/css/style.css">

<style>

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

        <h4>Reminder Tugas</h4>

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

            <?php while($data = mysqli_fetch_assoc($result)) : ?>

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

                        <?= date(
                            'd M Y H:i',
                            strtotime($data['deadline'])
                        ); ?>

                    </td>

                    <td>

                        <span class="
                        badge-status
                        <?= $data['status']; ?>
                        ">

                            <?= ucfirst($data['status']); ?>

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

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>