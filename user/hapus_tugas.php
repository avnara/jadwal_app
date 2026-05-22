<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    exit('Akses ditolak');
}

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(

    $conn,

    "DELETE FROM tugas
     WHERE id = ?
     AND user_id = ?"
);

mysqli_stmt_bind_param(

    $query,
    "ii",
    $id,
    $_SESSION['id_user']
);

mysqli_stmt_execute($query);

header("Location: tugas.php");
exit;