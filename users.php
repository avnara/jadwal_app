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
| SEARCH USER
|--------------------------------------------------------------------------
*/

$search = $_GET['search'] ?? '';

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM users
     WHERE nama LIKE CONCAT('%', ?, '%')
     OR email LIKE CONCAT('%', ?, '%')
     ORDER BY id_user DESC"
);

mysqli_stmt_bind_param(

    $query,
    "ss",
    $search,
    $search
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

<title>Kelola User</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{

    margin:0;
    font-family:Arial, sans-serif;
    background:#eef2f7;
}

/* SIDEBAR */

.sidebar{

    width:260px;
    height:100vh;
    background:#071739;
    position:fixed;
    padding-top:20px;
}

.sidebar h2{

    color:white;
    text-align:center;
    margin-bottom:40px;
}

.sidebar ul{

    list-style:none;
    padding:0;
}

.sidebar ul li{

    padding:15px 25px;
}

.sidebar ul li a{

    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:12px;
    font-size:19px;
}

.sidebar ul li:hover{

    background:#0b244f;
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
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

/* CARD */

.card-box{

    background:white;
    padding:30px;
    border-radius:20px;
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

    padding:18px;
}

.table-modern tbody tr{

    border-bottom:1px solid #ddd;
}

/* BUTTON */

.action-btn{

    padding:8px 14px;
    border:none;
    border-radius:10px;
    color:white;
    text-decoration:none;
    font-size:14px;
    margin-right:5px;
}

.btn-edit{

    background:#2563eb;
}

.btn-delete{

    background:#dc2626;
}

.btn-reset{

    background:#ea580c;
}

/* SEARCH */

.search-box{

    width:300px;
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

<!-- MAIN -->

<div class="main-content">

    <!-- TOPBAR -->

    <div class="topbar">

        <h3>

            Kelola User

        </h3>

        <form method="GET">

            <input
            type="text"
            name="search"
            class="form-control search-box"
            placeholder="Cari user..."
            value="<?= htmlspecialchars($search); ?>">

        </form>

    </div>

    <!-- TABLE -->

    <div class="card-box">

        <table class="table-modern">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (
                mysqli_num_rows($result) > 0
            ) : ?>

                <?php while (
                    $user = mysqli_fetch_assoc($result)
                ) : ?>

                    <tr>

                        <td>

                            <?= $user['id_user']; ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($user['nama']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($user['email']); ?>

                        </td>

                        <td>

                            ********

                        </td>

                        <td>

                            <a
                            href="edit_user.php?id=<?= $user['id_user']; ?>"
                            class="action-btn btn-edit">

                                Edit

                            </a>

                            <a
                            href="reset_password.php?id=<?= $user['id_user']; ?>"
                            class="action-btn btn-reset">

                                Reset

                            </a>

                            <a
                            href="hapus_user.php?id=<?= $user['id_user']; ?>"
                            class="action-btn btn-delete"
                            onclick="return confirm('Hapus user ini?')">

                                Hapus

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else : ?>

                <tr>

                    <td colspan="5">

                        Tidak ada user

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>