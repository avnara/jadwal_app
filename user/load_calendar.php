<?php

session_start();

require_once __DIR__ . '/../config/koneksi.php';

header('Content-Type: application/json');

$idUser = $_SESSION['id_user'];

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM jadwal
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $idUser
);

mysqli_stmt_execute($query);

$result =
mysqli_stmt_get_result($query);

$events = [];

while (
    $data = mysqli_fetch_assoc($result)
) {

    $events[] = [

        'title' =>
        $data['mata_pelajaran'],

        'start' =>
        date('Y-m-d') . 'T' .
        $data['jam_mulai']
    ];
}

echo json_encode($events);