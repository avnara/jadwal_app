<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| VALIDASI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../auth/login_admin.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FUNCTION SAFE COUNT
|--------------------------------------------------------------------------
*/

function getTotal(mysqli $conn, string $table): int
{
    $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table");

    if (!$query) return 0;

    $data = mysqli_fetch_assoc($query);

    return (int) ($data['total'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| TOTAL DATA
|--------------------------------------------------------------------------
*/

$totalUser    = getTotal($conn, 'users');
$totalRequest = getTotal($conn, 'request_perbaikan');
$totalError   = getTotal($conn, 'error_logs');

/*
|--------------------------------------------------------------------------
| USER ONLINE HARI INI
|--------------------------------------------------------------------------
*/

$userOnline  = 0;
$onlineQuery = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM login_logs
     WHERE DATE(login_time) = CURDATE()"
);

if ($onlineQuery) {
    $onlineData = mysqli_fetch_assoc($onlineQuery);
    $userOnline = (int) ($onlineData['total'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| DATA CHART LOGIN
|--------------------------------------------------------------------------
*/

$chartLabels = [];
$chartData   = [];

$chartQuery = mysqli_query($conn,
    "SELECT
        DATE(login_time) AS tanggal,
        COUNT(*) AS total
     FROM login_logs
     GROUP BY DATE(login_time)
     ORDER BY DATE(login_time) ASC
     LIMIT 7"
);

if ($chartQuery) {
    while ($row = mysqli_fetch_assoc($chartQuery)) {
        $chartLabels[] = $row['tanggal'];
        $chartData[]   = (int) $row['total'];
    }
}

/*
|--------------------------------------------------------------------------
| ACTIVITY TERBARU
|--------------------------------------------------------------------------
*/

$activityQuery = mysqli_query($conn,
    "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5"
);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Chart JS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
      top: 0;
      left: 0;
      bottom: 0;
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

    .sidebar-logo span {
      color: #60a5fa;
    }

    /* Profile block */

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

    /* Menu */

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

    /* ─── MAIN ─────────────────────────────────────── */

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

    /* ─── CONTENT AREA ──────────────────────────────── */

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

    .welcome-banner i {
      font-size: 20px;
    }

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
      right: -10px;
      bottom: -10px;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      opacity: 0.07;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.11);
    }

    .card.blue  { border-top-color: var(--accent); }
    .card.green { border-top-color: var(--accent2); }
    .card.orange{ border-top-color: var(--accent3); }
    .card.purple{ border-top-color: var(--accent4); }

    .card.blue::after  { background: var(--accent);  }
    .card.green::after { background: var(--accent2); }
    .card.orange::after{ background: var(--accent3); }
    .card.purple::after{ background: var(--accent4); }

    .card-icon {
      width: 44px;
      height: 44px;
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

    /* ─── BOX (chart & table) ───────────────────────── */

    .box {
      background: var(--surface);
      border-radius: var(--radius);
      padding: 28px;
      box-shadow: var(--shadow);
      margin-bottom: 24px;
    }

    .box-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .box-title i {
      color: var(--accent);
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

    .table-modern thead th:first-child {
      border-radius: 8px 0 0 0;
    }

    .table-modern thead th:last-child {
      border-radius: 0 8px 0 0;
    }

    .table-modern tbody td {
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      color: var(--text);
      vertical-align: middle;
    }

    .table-modern tbody tr:last-child td {
      border-bottom: none;
    }

    .table-modern tbody tr:hover td {
      background: #f8fafc;
    }

    .badge-id {
      display: inline-block;
      background: #e0e7ff;
      color: #3730a3;
      border-radius: 6px;
      padding: 2px 10px;
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      font-weight: 500;
    }

    .empty-msg {
      text-align: center;
      color: var(--muted);
      padding: 32px;
      font-size: 14px;
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

  <div class="sidebar-logo">ADMIN <span>PANEL</span></div>

  <div class="sidebar-profile">
    <div class="sidebar-avatar">
      <i class="fa fa-user-shield"></i>
    </div>
    <div class="sidebar-profile-info">
      <p>Login sebagai</p>
      <strong><?= htmlspecialchars($_SESSION['nama_admin']); ?></strong>
    </div>
  </div>

  <ul class="sidebar-menu">
    <li class="active">
      <a href="dashboard.php">
        <i class="fa fa-chart-line"></i> Dashboard
      </a>
    </li>
    <li>
      <a href="users.php">
        <i class="fa fa-users"></i> Kelola User
      </a>
    </li>
    <li>
      <a href="jadwal_user.php">
        <i class="fa fa-calendar"></i> Jadwal User
      </a>
    </li>
    <li>
      <a href="activity_logs.php">
        <i class="fa fa-file-lines"></i> Activity Logs
      </a>
    </li>
    <li>
      <a href="error_logs.php">
        <i class="fa fa-bug"></i> Error Logs
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
      <i class="fa fa-user-shield"></i>
      <span><?= htmlspecialchars($_SESSION['nama_admin']); ?></span>
    </div>
  </header>

  <!-- CONTENT -->
  <main class="content">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
      <i class="fa fa-bell"></i>
      Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama_admin']); ?>! Berikut ringkasan sistem hari ini.
    </div>

    <!-- Stat Cards -->
    <div class="cards">

      <div class="card blue">
        <div class="card-icon"><i class="fa fa-users"></i></div>
        <h4>Total User</h4>
        <div class="card-value"><?= $totalUser; ?></div>
      </div>

      <div class="card green">
        <div class="card-icon"><i class="fa fa-circle-check"></i></div>
        <h4>User Online</h4>
        <div class="card-value"><?= $userOnline; ?></div>
      </div>

      <div class="card orange">
        <div class="card-icon"><i class="fa fa-wrench"></i></div>
        <h4>Permintaan</h4>
        <div class="card-value"><?= $totalRequest; ?></div>
      </div>

      <div class="card purple">
        <div class="card-icon"><i class="fa fa-triangle-exclamation"></i></div>
        <h4>Error Sistem</h4>
        <div class="card-value"><?= $totalError; ?></div>
      </div>

    </div>

    <!-- Chart -->
    <div class="box">
      <div class="box-title">
        <i class="fa fa-chart-bar"></i> Statistik Login User (7 Hari Terakhir)
      </div>
      <canvas id="myChart" height="90"></canvas>
    </div>

    <!-- Activity Table -->
    <div class="box">
      <div class="box-title">
        <i class="fa fa-clock-rotate-left"></i> Activity Terbaru
      </div>

      <table class="table-modern">
        <thead>
          <tr>
            <th>ID</th>
            <th>Aktivitas</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>

          <?php if ($activityQuery && mysqli_num_rows($activityQuery) > 0) : ?>

            <?php while ($activity = mysqli_fetch_assoc($activityQuery)) : ?>
              <tr>
                <td><span class="badge-id">#<?= $activity['id']; ?></span></td>
                <td><?= htmlspecialchars($activity['aktivitas']); ?></td>
                <td><?= htmlspecialchars($activity['created_at']); ?></td>
              </tr>
            <?php endwhile; ?>

          <?php else : ?>
            <tr>
              <td colspan="3" class="empty-msg">
                <i class="fa fa-inbox"></i> Tidak ada activity
              </td>
            </tr>
          <?php endif; ?>

        </tbody>
      </table>
    </div>

  </main>
</div>

<!-- Chart Script -->
<script>
const ctx = document.getElementById('myChart');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($chartLabels); ?>,
    datasets: [{
      label: 'Login User',
      data: <?= json_encode($chartData); ?>,
      backgroundColor: 'rgba(37,99,235,0.15)',
      borderColor: '#2563eb',
      borderWidth: 2,
      borderRadius: 8,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        labels: {
          font: { family: "'Plus Jakarta Sans', sans-serif", size: 13 }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9' },
        ticks: {
          font: { family: "'DM Mono', monospace", size: 12 },
          color: '#64748b'
        }
      },
      x: {
        grid: { display: false },
        ticks: {
          font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
          color: '#64748b'
        }
      }
    }
  }
});
</script>

</body>
</html>