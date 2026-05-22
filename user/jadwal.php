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
     FROM jadwal
     WHERE user_id = ?
     ORDER BY FIELD(
        hari,
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu'
     ),
     jam_mulai ASC"
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

<title>Jadwal Saya</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="../assets/css/style.css">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
                Jadwal Saya

            </a>

        </li>

        <li>

            <a href="kalender.php">

                <i class="fa fa-calendar-days"></i>
                Kalender

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

<div class="main-content">

    <div class="topbar">

        <h3>Jadwal Saya</h3>

        <a
        href="tambah_jadwal.php"
        class="btn-modern">

            + Tambah Jadwal

        </a>

    </div>

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($result) > 0
            ) : ?>

                <?php while (
                    $data = mysqli_fetch_assoc($result)
                ) : ?>

                    <tr>

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

                        <td>

                            <a
                            href="edit_jadwal.php?id=<?= $data['id']; ?>"
                            class="btn btn-primary btn-sm">

                                Edit

                            </a>

                            <a
                            href="hapus_jadwal.php?id=<?= $data['id']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus jadwal?')">

                                Hapus

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td colspan="6">

                        Belum ada jadwal

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>