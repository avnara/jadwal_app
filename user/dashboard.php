<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];

/*
|--------------------------------------------------------------------------
| TOTAL JADWAL
|--------------------------------------------------------------------------
*/

$qJadwal = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total FROM jadwal WHERE user_id = ?"
);
mysqli_stmt_bind_param($qJadwal, "i", $idUser);
mysqli_stmt_execute($qJadwal);
$totalJadwal = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($qJadwal))['total'];

/*
|--------------------------------------------------------------------------
| TOTAL TUGAS
|--------------------------------------------------------------------------
*/

$qTugas = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total FROM tugas WHERE user_id = ?"
);
mysqli_stmt_bind_param($qTugas, "i", $idUser);
mysqli_stmt_execute($qTugas);
$totalTugas = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($qTugas))['total'];

/*
|--------------------------------------------------------------------------
| TOTAL REMINDER
|--------------------------------------------------------------------------
*/

$qReminder = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total FROM reminder WHERE user_id = ?"
);
mysqli_stmt_bind_param($qReminder, "i", $idUser);
mysqli_stmt_execute($qReminder);
$totalReminder = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($qReminder))['total'];

/*
|--------------------------------------------------------------------------
| TOTAL MATA PELAJARAN
|--------------------------------------------------------------------------
*/

$qMapel = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total FROM mata_pelajaran WHERE user_id = ?"
);
mysqli_stmt_bind_param($qMapel, "i", $idUser);
mysqli_stmt_execute($qMapel);
$totalMapel = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($qMapel))['total'];

/*
|--------------------------------------------------------------------------
| NOTIF TUGAS DEADLINE
|--------------------------------------------------------------------------
*/

$notifQuery = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total
     FROM tugas
     WHERE user_id = ?
     AND status = 'belum'
     AND deadline <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)"
);
mysqli_stmt_bind_param($notifQuery, "i", $idUser);
mysqli_stmt_execute($notifQuery);
$totalNotif = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($notifQuery))['total'];

/*
|--------------------------------------------------------------------------
| JADWAL HARI INI
|--------------------------------------------------------------------------
*/

$hariIni = date('l');
$hariIndonesia = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu',
];
$hari = $hariIndonesia[$hariIni];

$qHariIni = mysqli_prepare($conn,
    "SELECT * FROM jadwal
     WHERE user_id = ? AND hari = ?
     ORDER BY jam_mulai ASC"
);
mysqli_stmt_bind_param($qHariIni, "is", $idUser, $hari);
mysqli_stmt_execute($qHariIni);
$dataHariIni = mysqli_stmt_get_result($qHariIni);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>

    /* ─── RESET & BASE ─────────────────────────────── */

    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --sidebar-w: 260px;
      --bg:        #f0f4f8;
      --surface:   #ffffff;
      --accent:    #2563eb;
      --accent2:   #16a34a;
      --accent3:   #ea580c;
      --accent4:   #7c3aed;
      --text:      #1e293b;
      --muted:     #64748b;
      --border:    #e2e8f0;
      --radius:    16px;
      --shadow:    0 4px 24px rgba(0,0,0,0.07);
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
    }

    /* ─── SIDEBAR ──────────────────────────────────── */

    .sidebar {
      width: var(--sidebar-w);
      min-height: 100vh;
      background: #0f172a;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 100;
      padding: 0 0 24px;
    }

    .sidebar-logo {
      padding: 28px 28px 20px;
      font-size: 22px;
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.5px;
      border-bottom: 1px solid rgba(255,255,255,0.07);
    }

    .sidebar-logo span { color: #60a5fa; }

    .sidebar-profile {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 20px 24px;
      border-bottom: 1px solid rgba(255,255,255,0.07);
      margin-bottom: 8px;
    }

    .sidebar-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: linear-gradient(135deg, #2563eb, #7c3aed);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: #fff;
      flex-shrink: 0;
    }

    .sidebar-profile-info p {
      font-size: 11px;
      color: #94a3b8;
      margin-bottom: 2px;
    }

    .sidebar-profile-info strong {
      font-size: 13px;
      color: #f1f5f9;
      font-weight: 700;
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 155px;
    }

    .sidebar-menu {
      list-style: none;
      padding: 0 12px;
      flex: 1;
    }

    .sidebar-menu li a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 16px;
      border-radius: 10px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: background 0.2s, color 0.2s;
      margin-bottom: 2px;
      position: relative;
    }

    .sidebar-menu li a i {
      width: 18px;
      text-align: center;
      font-size: 15px;
    }

    .sidebar-menu li a:hover,
    .sidebar-menu li.active a {
      background: rgba(96,165,250,0.12);
      color: #60a5fa;
    }

    .sidebar-menu li.logout a:hover {
      background: rgba(239,68,68,0.12);
      color: #f87171;
    }

    /* Notification badge in sidebar */
    .notif-badge {
      margin-left: auto;
      background: #ef4444;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      border-radius: 20px;
      padding: 1px 7px;
      line-height: 1.6;
    }

    /* ─── WRAPPER ───────────────────────────────────── */

    .wrapper {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ─── TOPBAR ────────────────────────────────────── */

    .topbar {
      background: var(--surface);
      padding: 18px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .topbar h3 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text);
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--muted);
      font-size: 14px;
    }

    .topbar-right i {
      font-size: 20px;
      color: var(--accent);
    }

    /* Topbar notif bell */
    .topbar-notif {
      position: relative;
      margin-right: 4px;
    }

    .topbar-notif .dot {
      position: absolute;
      top: -3px;
      right: -3px;
      width: 9px;
      height: 9px;
      background: #ef4444;
      border-radius: 50%;
      border: 2px solid #fff;
    }

    /* ─── CONTENT ───────────────────────────────────── */

    .content {
      padding: 28px 32px;
      flex: 1;
    }

    /* Welcome banner */
    .welcome-banner {
      background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 60%, #7c3aed 100%);
      color: #fff;
      border-radius: var(--radius);
      padding: 20px 28px;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0.3px;
      margin-bottom: 28px;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 8px 24px rgba(37,99,235,0.3);
    }

    .welcome-banner i { font-size: 20px; }

    /* Deadline warning strip (shown only if $totalNotif > 0) */
    .deadline-banner {
      background: #fff7ed;
      border: 1px solid #fed7aa;
      border-radius: var(--radius);
      padding: 14px 20px;
      font-size: 13px;
      font-weight: 600;
      color: #9a3412;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .deadline-banner i { color: #ea580c; }

    /* ─── STAT CARDS ────────────────────────────────── */

    .cards {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      margin-bottom: 28px;
    }

    .card {
      background: var(--surface);
      border-radius: var(--radius);
      padding: 24px;
      box-shadow: var(--shadow);
      border-top: 4px solid transparent;
      display: flex;
      flex-direction: column;
      gap: 10px;
      transition: transform 0.25s, box-shadow 0.25s;
      position: relative;
      overflow: hidden;
    }

    .card::after {
      content: '';
      position: absolute;
      right: -10px; bottom: -10px;
      width: 80px; height: 80px;
      border-radius: 50%;
      opacity: 0.07;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.11);
    }

    .card.blue   { border-top-color: var(--accent); }
    .card.green  { border-top-color: var(--accent2); }
    .card.orange { border-top-color: var(--accent3); }
    .card.purple { border-top-color: var(--accent4); }

    .card.blue::after   { background: var(--accent); }
    .card.green::after  { background: var(--accent2); }
    .card.orange::after { background: var(--accent3); }
    .card.purple::after { background: var(--accent4); }

    .card-icon {
      width: 44px; height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: #fff;
    }

    .card.blue   .card-icon { background: var(--accent); }
    .card.green  .card-icon { background: var(--accent2); }
    .card.orange .card-icon { background: var(--accent3); }
    .card.purple .card-icon { background: var(--accent4); }

    .card h4 {
      font-size: 12px;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .card .card-value {
      font-size: 36px;
      font-weight: 800;
      color: var(--text);
      font-family: 'DM Mono', monospace;
      line-height: 1;
    }

    /* ─── BOX ───────────────────────────────────────── */

    .box {
      background: var(--surface);
      border-radius: var(--radius);
      padding: 28px;
      box-shadow: var(--shadow);
      margin-bottom: 24px;
    }

    .box-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .box-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .box-title i { color: var(--accent); }

    .btn-add {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--accent);
      color: #fff;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      font-weight: 600;
      padding: 9px 18px;
      border-radius: 10px;
      text-decoration: none;
      transition: background 0.2s, transform 0.2s;
    }

    .btn-add:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    /* ─── TABLE ─────────────────────────────────────── */

    .table-modern {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .table-modern thead th {
      background: #f8fafc;
      color: var(--muted);
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      padding: 12px 16px;
      text-align: left;
      border-bottom: 2px solid var(--border);
    }

    .table-modern thead th:first-child { border-radius: 8px 0 0 0; }
    .table-modern thead th:last-child  { border-radius: 0 8px 0 0; }

    .table-modern tbody td {
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      color: var(--text);
      vertical-align: middle;
    }

    .table-modern tbody tr:last-child td { border-bottom: none; }
    .table-modern tbody tr:hover td     { background: #f8fafc; }

    /* Time pill */
    .time-pill {
      display: inline-block;
      background: #eff6ff;
      color: #1d4ed8;
      border-radius: 6px;
      padding: 3px 10px;
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      font-weight: 500;
    }

    /* Subject badge */
    .subject-badge {
      display: inline-block;
      background: #f0fdf4;
      color: #166534;
      border-radius: 6px;
      padding: 3px 10px;
      font-size: 12px;
      font-weight: 600;
    }

    /* Day chip */
    .day-chip {
      display: inline-block;
      background: #faf5ff;
      color: #6d28d9;
      border-radius: 6px;
      padding: 3px 10px;
      font-size: 12px;
      font-weight: 600;
    }

    .empty-msg {
      text-align: center;
      color: var(--muted);
      padding: 40px;
      font-size: 14px;
    }

    .empty-msg i {
      display: block;
      font-size: 32px;
      margin-bottom: 10px;
      opacity: 0.4;
    }

    /* ─── RESPONSIVE ─────────────────────────────────── */

    @media (max-width: 1024px) {
      .cards { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
      .sidebar { width: 0; overflow: hidden; }
      .wrapper { margin-left: 0; }
      .content { padding: 20px 16px; }
      .cards   { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
      .cards { grid-template-columns: 1fr; }
    }

  </style>
</head>
<body>

<!-- ─── SIDEBAR ──────────────────────────────── -->
<aside class="sidebar">

  <div class="sidebar-logo">JADWAL <span>APP</span></div>

  <div class="sidebar-profile">
    <div class="sidebar-avatar">
      <i class="fa fa-user-graduate"></i>
    </div>
    <div class="sidebar-profile-info">
      <p>Login sebagai</p>
      <strong><?= htmlspecialchars($_SESSION['nama']); ?></strong>
    </div>
  </div>

  <ul class="sidebar-menu">
    <li class="active">
      <a href="dashboard.php">
        <i class="fa fa-home"></i> Dashboard
      </a>
    </li>
    <li>
      <a href="jadwal.php">
        <i class="fa fa-calendar"></i> Jadwal Saya
      </a>
    </li>
    <li>
      <a href="mata_pelajaran.php">
        <i class="fa fa-book"></i> Mata Pelajaran
      </a>
    </li>
    <li>
      <a href="reminder.php">
        <i class="fa fa-bell"></i> Reminder
        <?php if ($totalReminder > 0) : ?>
          <span class="notif-badge"><?= $totalReminder; ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="tugas.php">
        <i class="fa fa-list-check"></i> Tugas
        <?php if ($totalNotif > 0) : ?>
          <span class="notif-badge"><?= $totalNotif; ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li class="logout">
      <a href="../auth/logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
    </li>
  </ul>

</aside>

<!-- ─── WRAPPER ──────────────────────────────── -->
<div class="wrapper">

  <!-- TOPBAR -->
  <header class="topbar">
    <h3>Dashboard</h3>
    <div class="topbar-right">
      <?php if ($totalNotif > 0) : ?>
        <a href="tugas.php" class="topbar-notif" title="<?= $totalNotif; ?> tugas deadline dekat">
          <i class="fa fa-bell" style="color:var(--accent3);"></i>
          <span class="dot"></span>
        </a>
      <?php endif; ?>
      <i class="fa fa-user-circle"></i>
      <span><?= htmlspecialchars($_SESSION['nama']); ?></span>
    </div>
  </header>

  <!-- CONTENT -->
  <main class="content">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
      <i class="fa fa-graduation-cap"></i>
      Selamat datang, <?= htmlspecialchars($_SESSION['nama']); ?>! Hari ini adalah <strong><?= $hari; ?></strong>.
    </div>

    <!-- Deadline Warning (only if tasks near deadline) -->
    <?php if ($totalNotif > 0) : ?>
    <div class="deadline-banner">
      <i class="fa fa-triangle-exclamation"></i>
      Kamu punya <strong><?= $totalNotif; ?> tugas</strong> yang mendekati deadline dalam 3 hari ke depan.
      <a href="tugas.php" style="margin-left:8px;color:#9a3412;text-decoration:underline;">Lihat sekarang →</a>
    </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="cards">

      <div class="card blue">
        <div class="card-icon"><i class="fa fa-calendar-days"></i></div>
        <h4>Total Jadwal</h4>
        <div class="card-value"><?= $totalJadwal; ?></div>
      </div>

      <div class="card green">
        <div class="card-icon"><i class="fa fa-list-check"></i></div>
        <h4>Total Tugas</h4>
        <div class="card-value"><?= $totalTugas; ?></div>
      </div>

      <div class="card orange">
        <div class="card-icon"><i class="fa fa-bell"></i></div>
        <h4>Reminder</h4>
        <div class="card-value"><?= $totalReminder; ?></div>
      </div>

      <div class="card purple">
        <div class="card-icon"><i class="fa fa-book-open"></i></div>
        <h4>Mata Pelajaran</h4>
        <div class="card-value"><?= $totalMapel; ?></div>
      </div>

    </div>

    <!-- Jadwal Hari Ini -->
    <div class="box">
      <div class="box-header">
        <div class="box-title">
          <i class="fa fa-calendar-check"></i>
          Jadwal Hari Ini &mdash; <span style="color:var(--muted);font-weight:500;"><?= $hari; ?></span>
        </div>
        <a href="tambah_jadwal.php" class="btn-add">
          <i class="fa fa-plus"></i> Tambah Jadwal
        </a>
      </div>

      <table class="table-modern">
        <thead>
          <tr>
            <th>Hari</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Mata Pelajaran</th>
            <th>Guru</th>
            <th>Ruangan</th>
          </tr>
        </thead>
        <tbody>

          <?php if (mysqli_num_rows($dataHariIni) > 0) : ?>

            <?php while ($data = mysqli_fetch_assoc($dataHariIni)) : ?>
              <tr>
                <td><span class="day-chip"><?= htmlspecialchars($data['hari']); ?></span></td>
                <td><span class="time-pill"><?= htmlspecialchars($data['jam_mulai']); ?></span></td>
                <td><span class="time-pill"><?= htmlspecialchars($data['jam_selesai']); ?></span></td>
                <td><span class="subject-badge"><?= htmlspecialchars($data['mata_pelajaran']); ?></span></td>
                <td><?= htmlspecialchars($data['guru']); ?></td>
                <td><?= htmlspecialchars($data['ruangan']); ?></td>
              </tr>
            <?php endwhile; ?>

          <?php else : ?>
            <tr>
              <td colspan="6" class="empty-msg">
                <i class="fa fa-calendar-xmark"></i>
                Tidak ada jadwal hari ini
              </td>
            </tr>
          <?php endif; ?>

        </tbody>
      </table>
    </div>

  </main>
</div>

</body>
</html>
