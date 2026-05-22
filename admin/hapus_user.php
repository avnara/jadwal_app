<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_admin'])) {

    header("Location: ../auth/login_admin.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

/* LOG DULU */

$adminId = $_SESSION['id_admin'];

$aktivitas =
    "Admin menghapus user ID "
    . $id;

$log = mysqli_prepare(
    $conn,
    "INSERT INTO activity_logs
    (
        admin_id,
        user_id,
        aktivitas
    )
    VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $log,
    "iis",
    $adminId,
    $id,
    $aktivitas
);

mysqli_stmt_execute($log);

/* HAPUS USER */

$query = mysqli_prepare(
    $conn,
    "DELETE FROM users
     WHERE id_user = ?"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $id
);

if (mysqli_stmt_execute($query)) {

    header("Location: users.php");
    exit;
}