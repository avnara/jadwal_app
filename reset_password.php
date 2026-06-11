<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_admin'])) {

    header("Location: ../auth/login_admin.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

$passwordBaru = password_hash(
    "123456",
    PASSWORD_DEFAULT
);

$query = mysqli_prepare(
    $conn,
    "UPDATE users
     SET password = ?
     WHERE id_user = ?"
);

mysqli_stmt_bind_param(
    $query,
    "si",
    $passwordBaru,
    $id
);

if (mysqli_stmt_execute($query)) {

    /* LOG */

    $adminId = $_SESSION['id_admin'];

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

    $aktivitas =
        "Admin reset password user ID "
        . $id;

    mysqli_stmt_bind_param(
        $log,
        "iis",
        $adminId,
        $id,
        $aktivitas
    );

    mysqli_stmt_execute($log);

    header("Location: users.php");
    exit;
}