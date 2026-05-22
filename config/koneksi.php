<?php

declare(strict_types=1);

$host = "localhost";
$username = "root";
$password = "1";
$database = "jadwal_pelajaran";

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");