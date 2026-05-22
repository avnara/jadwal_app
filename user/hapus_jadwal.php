<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(
    $conn,
    "DELETE FROM jadwal
     WHERE id_jadwal = ?
     AND user_id = ?"
);

mysqli_stmt_bind_param(
    $query,
    "ii",
    $id,
    $idUser
);

if (mysqli_stmt_execute($query)) {

    header("Location: jadwal.php");
    exit;

} else {

    echo "Gagal hapus data";

}