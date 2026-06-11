<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}

$idMapel = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare(

    $conn,

    "DELETE FROM mata_pelajaran
     WHERE id_mapel = ?"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $idMapel
);

mysqli_stmt_execute($query);

header("Location: mata_pelajaran.php");
exit;
?>