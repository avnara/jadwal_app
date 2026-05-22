<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

$idUser = $_SESSION['id_user'];

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM tugas
     WHERE user_id = ?
     AND status = 'belum'
     AND deadline <= CURDATE()"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $idUser
);

mysqli_stmt_execute($query);

$result =
mysqli_stmt_get_result($query);

while (
    $data = mysqli_fetch_assoc($result)
) {

    echo "

    <div class='alert alert-danger'>

        Deadline tugas:
        <strong>

            {$data['judul']}

        </strong>

        hari ini!

    </div>
    ";
}