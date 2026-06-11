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
| AMBIL DATA MATA PELAJARAN
|--------------------------------------------------------------------------
*/

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM mata_pelajaran
     WHERE user_id = ?
     ORDER BY nama_mapel ASC"
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

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Mata Pelajaran</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{

    background:#eef2f7;
    font-family:Arial, sans-serif;
}

/* CARD */

.card-box{

    background:white;
    border-radius:20px;
    padding:30px;
    margin-top:40px;
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

.table-modern th,
.table-modern td{

    padding:16px;
}

.table-modern tbody tr{

    border-bottom:1px solid #ddd;
}

.empty{

    text-align:center;
    color:#777;
    padding:30px;
}

.btn-action{

    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:14px;
    margin-right:5px;
}

.btn-edit{

    background:#2563eb;
}

.btn-delete{

    background:#dc2626;
}

.btn-add{

    background:#16a34a;
    border:none;
    padding:10px 18px;
    border-radius:12px;
    color:white;
    text-decoration:none;
    font-weight:bold;
}

.btn-add:hover{

    background:#15803d;
    color:white;
}

</style>

</head>

<body>

<div class="container">

    <div class="card-box">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2>

                Mata Pelajaran Saya

            </h2>

            <div>

                <a
                href="dashboard.php"
                class="btn btn-primary">

                    <i class="fa fa-arrow-left"></i>
                    Dashboard

                </a>

                <a
                href="tambah_mapel.php"
                class="btn-add">

                    <i class="fa fa-plus"></i>
                    Tambah Pelajaran

                </a>

            </div>

        </div>

        <table class="table-modern">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>
                    <th>Hari</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php

            if (
                mysqli_num_rows($result) > 0
            ) :

                $no = 1;

                while (
                    $row = mysqli_fetch_assoc($result)
                ) :

            ?>

                <tr>

                    <td>

                        <?= $no++; ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($row['nama_mapel']); ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($row['guru']); ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($row['ruangan'] ?? '-'); ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($row['hari'] ?? '-'); ?>

                    </td>

                    <td>

                        <a
                        href="edit_mapel.php?id=<?= $row['id_mapel']; ?>"
                        class="btn-action btn-edit">

                            Edit

                        </a>

                        <a
                        href="hapus_mapel.php?id=<?= $row['id_mapel']; ?>"
                        class="btn-action btn-delete"
                        onclick="return confirm('Hapus mata pelajaran ini?')">

                            Hapus

                        </a>

                    </td>

                </tr>

            <?php

                endwhile;

            else :

            ?>

                <tr>

                    <td
                    colspan="6"
                    class="empty">

                        Belum ada mata pelajaran

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>