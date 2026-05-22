<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM tugas
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

<title>Tugas Saya</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="../assets/css/style.css">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

.status-belum{

    background:#dc2626;
    color:white;
    padding:6px 12px;
    border-radius:20px;
}

.status-selesai{

    background:#16a34a;
    color:white;
    padding:6px 12px;
    border-radius:20px;
}

</style>

</head>

<body>

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

            <a href="tugas.php">

                <i class="fa fa-book"></i>
                Tugas

            </a>

        </li>

        <li>

            <a href="reminder.php">

                <i class="fa fa-bell"></i>
                Reminder

            </a>

        </li>

    </ul>

</div>

<div class="main-content">

    <div class="topbar">

        <h3>Tugas Saya</h3>

        <a
        href="tambah_tugas.php"
        class="btn-modern">

            + Tambah Tugas

        </a>

    </div>

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($result) > 0
            ) : ?>

                <?php while (
                    $tugas = mysqli_fetch_assoc($result)
                ) : ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($tugas['judul']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($tugas['deskripsi']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($tugas['deadline']); ?>

                        </td>

                        <td>

                            <?php if (
                                $tugas['status'] === 'selesai'
                            ) : ?>

                                <span class="status-selesai">

                                    Selesai

                                </span>

                            <?php else : ?>

                                <span class="status-belum">

                                    Belum

                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <a
                            href="update_status.php?id=<?= $tugas['id']; ?>"
                            class="btn btn-success btn-sm">

                                Selesai

                            </a>

                            <a
                            href="edit_tugas.php?id=<?= $tugas['id']; ?>"
                            class="btn btn-primary btn-sm">

                                Edit

                            </a>

                            <a
                            href="hapus_tugas.php?id=<?= $tugas['id']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus tugas?')">

                                Hapus

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td colspan="5">

                        Belum ada tugas

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>