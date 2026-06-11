<?php

/*
|--------------------------------------------------------------------------
| START SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_user'])) {

    exit;
}

$idUser = (int) $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| AMBIL NOTIFIKASI TUGAS
|--------------------------------------------------------------------------
*/

$query = mysqli_prepare(

    $conn,

    "SELECT *
     FROM tugas
     WHERE user_id = ?
     AND status = 'belum'
     AND deadline <= CURDATE()
     ORDER BY deadline ASC"
);

mysqli_stmt_bind_param(

    $query,
    "i",
    $idUser
);

mysqli_stmt_execute($query);

$result =
mysqli_stmt_get_result($query);

/*
|--------------------------------------------------------------------------
| TAMPILKAN NOTIFIKASI
|--------------------------------------------------------------------------
*/

while (
    $data = mysqli_fetch_assoc($result)
) {

?>

    <div class="alert alert-danger">

        Deadline tugas:

        <strong>

            <?= htmlspecialchars($data['judul']); ?>

        </strong>

        hari ini!

    </div>

<?php

}

?>