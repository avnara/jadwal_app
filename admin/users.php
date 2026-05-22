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
     ORDER BY id DESC"
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

<link
rel="stylesheet"
href="../assets/css/style.css">

<style>

.action-btn{

    padding:8px 14px;

    border:none;

    border-radius:10px;

    color:white;

    text-decoration:none;

    font-size:14px;
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

        <h3>Kelola User</h3>

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

                            <?= $user['id']; ?>

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
                            href="edit_user.php?id=<?= $user['id']; ?>"
                            class="action-btn btn-edit">

                                Edit

                            </a>

                            <a
                            href="reset_password.php?id=<?= $user['id']; ?>"
                            class="action-btn btn-reset">

                                Reset

                            </a>

                            <a
                            href="hapus_user.php?id=<?= $user['id']; ?>"
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