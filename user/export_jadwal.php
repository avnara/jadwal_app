<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

require_once __DIR__ . '/../tcpdf/tcpdf.php';

if (!isset($_SESSION['id_user'])) {

    exit('Akses ditolak');
}

$idUser = (int) $_SESSION['id_user'];

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

/*
|--------------------------------------------------------------------------
| PDF
|--------------------------------------------------------------------------
*/

$pdf = new TCPDF();

$pdf->SetCreator('Jadwal App');

$pdf->SetAuthor('Kiara');

$pdf->SetTitle('Jadwal Pelajaran');

$pdf->AddPage();

$pdf->SetFont('helvetica', '', 12);

$html = '

<h1 align="center">
Jadwal Pelajaran
</h1>

<table border="1" cellpadding="6">

<tr style="background-color:#2563eb;color:white;">

    <th><b>Hari</b></th>
    <th><b>Jam</b></th>
    <th><b>Mapel</b></th>
    <th><b>Guru</b></th>
    <th><b>Ruangan</b></th>

</tr>
';

while (
    $data = mysqli_fetch_assoc($result)
) {

    $html .= '

    <tr>

        <td>'.$data['hari'].'</td>

        <td>
            '.$data['jam_mulai'].' -
            '.$data['jam_selesai'].'
        </td>

        <td>'.$data['mata_pelajaran'].'</td>

        <td>'.$data['guru'].'</td>

        <td>'.$data['ruangan'].'</td>

    </tr>
    ';
}

$html .= '</table>';

$pdf->writeHTML($html);

$pdf->Output('jadwal_pelajaran.pdf', 'I');