<?php

declare(strict_types=1);

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {

    echo json_encode([]);
    exit;
}

$idUser = (int) $_SESSION['id_user'];

$query = mysqli_prepare(
    $conn,
    "SELECT 
        id_jadwal,
        mata_pelajaran,
        tanggal,
        jam_mulai,
        jam_selesai,
        guru
     FROM jadwal
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($query, "i", $idUser);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

$events = [];

while ($row = mysqli_fetch_assoc($result)) {

    $events[] = [

        'id' => $row['id_jadwal'],

        'title' =>
            $row['mata_pelajaran']
            . ' - '
            . $row['guru'],

        'start' =>
            $row['tanggal']
            . 'T'
            . $row['jam_mulai'],

        'end' =>
            $row['tanggal']
            . 'T'
            . $row['jam_selesai'],

        'backgroundColor' => '#2563eb',
        'borderColor' => '#2563eb'

    ];
}

echo json_encode($events);