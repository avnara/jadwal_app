<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login_user.php");
    exit;
}

$idUser = (int) $_SESSION['id_user'];
$id     = (int) ($_GET['id'] ?? 0);

/*
|--------------------------------------------------------------------------
| FETCH DATA
|--------------------------------------------------------------------------
*/

$query = mysqli_prepare($conn,
    "SELECT * FROM jadwal WHERE id_jadwal = ? AND user_id = ?"
);
mysqli_stmt_bind_param($query, "ii", $id, $idUser);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$data   = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: jadwal.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| HANDLE POST
|--------------------------------------------------------------------------
*/

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tanggal    = $_POST['tanggal']        ?? '';
    $jamMulai   = $_POST['jam_mulai']      ?? '';
    $jamSelesai = $_POST['jam_selesai']    ?? '';
    $mapel      = trim($_POST['mata_pelajaran'] ?? '');
    $guru       = trim($_POST['guru']      ?? '');
    $ruangan    = trim($_POST['ruangan']   ?? '');

    // Derive hari from tanggal so dashboard filter stays in sync
    $hariIndonesia = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];
    $hari = $tanggal
        ? ($hariIndonesia[date('l', strtotime($tanggal))] ?? '')
        : '';

    // Basic validation
    if (!$tanggal)        $errors[] = "Tanggal wajib diisi.";
    if (!$jamMulai)       $errors[] = "Jam mulai wajib diisi.";
    if (!$jamSelesai)     $errors[] = "Jam selesai wajib diisi.";
    if ($jamSelesai && $jamMulai && $jamSelesai <= $jamMulai)
                          $errors[] = "Jam selesai harus lebih besar dari jam mulai.";
    if ($mapel === '')    $errors[] = "Mata pelajaran wajib diisi.";

    if (empty($errors)) {

        $update = mysqli_prepare($conn,
            "UPDATE jadwal
             SET
                tanggal        = ?,
                hari           = ?,
                jam_mulai      = ?,
                jam_selesai    = ?,
                mata_pelajaran = ?,
                guru           = ?,
                ruangan        = ?
             WHERE id_jadwal = ?
             AND user_id    = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssssssii",
            $tanggal,
            $hari,
            $jamMulai,
            $jamSelesai,
            $mapel,
            $guru,
            $ruangan,
            $id,
            $idUser
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: jadwal.php?updated=1");
            exit;
        } else {
            $errors[] = "Gagal menyimpan perubahan. Silakan coba lagi.";
        }
    }

    // Repopulate $data with submitted values so form stays filled on error
    $data = array_merge($data, [
        'tanggal'        => $tanggal,
        'jam_mulai'      => $jamMulai,
        'jam_selesai'    => $jamSelesai,
        'mata_pelajaran' => $mapel,
        'guru'           => $guru,
        'ruangan'        => $ruangan,
    ]);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Jadwal</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --sidebar-w: 260px;
      --bg:      #f0f4f8;
      --surface: #ffffff;
      --accent:  #2563eb;
      --text:    #1e293b;
      --muted:   #64748b;
      --border:  #e2e8f0;
      --radius:  16px;
      --shadow:  0 4px 24px rgba(0,0,0,0.07);
      --danger:  #ef4444;
      --warn:    #ea580c;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
    }

    /* ─── SIDEBAR ─────────────────────────────────── */

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
      width: 42px; height: 42px;
      border-radius: 50%;
      background: linear-gradient(135deg, #2563eb, #7c3aed);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #fff; flex-shrink: 0;
    }

    .sidebar-profile-info p      { font-size: 11px; color: #94a3b8; margin-bottom: 2px; }
    .sidebar-profile-info strong {
      font-size: 13px; color: #f1f5f9; font-weight: 700;
      display: block; white-space: nowrap; overflow: hidden;
      text-overflow: ellipsis; max-width: 155px;
    }

    .sidebar-menu {
      list-style: none;
      padding: 0 12px;
      flex: 1;
    }

    .sidebar-menu li a {
      display: flex; align-items: center; gap: 12px;
      padding: 11px 16px; border-radius: 10px;
      color: #94a3b8; text-decoration: none;
      font-size: 14px; font-weight: 500;
      transition: background 0.2s, color 0.2s;
      margin-bottom: 2px;
    }

    .sidebar-menu li a i { width: 18px; text-align: center; font-size: 15px; }

    .sidebar-menu li a:hover,
    .sidebar-menu li.active a {
      background: rgba(96,165,250,0.12);
      color: #60a5fa;
    }

    .sidebar-menu li.logout a:hover {
      background: rgba(239,68,68,0.12);
      color: #f87171;
    }

    /* ─── WRAPPER ────────────────────────────────── */

    .wrapper {
      margin-left: var(--sidebar-w);
      flex: 1; display: flex; flex-direction: column; min-height: 100vh;
    }

    .topbar {
      background: var(--surface);
      padding: 18px 32px;
      display: flex; align-items: center; justify-content: space-between;
      border-bottom: 1px solid var(--border);
      position: sticky; top: 0; z-index: 50;
    }

    .topbar h3 { font-size: 18px; font-weight: 700; color: var(--text); }

    .topbar-right {
      display: flex; align-items: center; gap: 10px;
      color: var(--muted); font-size: 14px;
    }

    .topbar-right i { font-size: 20px; color: var(--accent); }

    .content { padding: 28px 32px; flex: 1; }

    /* ─── FORM CARD ──────────────────────────────── */

    .form-card {
      background: var(--surface);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      max-width: 640px;
      margin: 0 auto;
      overflow: hidden;
    }

    .form-card-header {
      background: linear-gradient(135deg, #1d4ed8, #2563eb);
      padding: 24px 32px;
      display: flex; align-items: center; gap: 12px;
    }

    .form-card-header .header-icon {
      width: 44px; height: 44px;
      border-radius: 12px;
      background: rgba(255,255,255,0.15);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; color: #fff;
    }

    .form-card-header h2 {
      font-size: 20px; font-weight: 800; color: #fff; margin: 0;
    }

    .form-card-header p {
      font-size: 13px; color: rgba(255,255,255,0.7); margin: 2px 0 0;
    }

    .form-body { padding: 32px; }

    /* ─── ERRORS ─────────────────────────────────── */

    .error-box {
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 24px;
      font-size: 13px;
      color: #b91c1c;
    }

    .error-box ul { margin: 6px 0 0 16px; }
    .error-box ul li { margin-bottom: 4px; }

    /* ─── FORM ELEMENTS ──────────────────────────── */

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .form-group { margin-bottom: 20px; }

    .form-group label {
      display: block;
      font-size: 12px;
      font-weight: 700;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.7px;
      margin-bottom: 7px;
    }

    .form-group label span { color: var(--danger); }

    .form-control {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 14px;
      color: var(--text);
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
    }

    .form-control:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }

    .form-control.time {
      font-family: 'DM Mono', monospace;
    }

    /* Derived day display */
    .day-preview {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 11px 14px;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      background: #f8fafc;
      font-size: 14px;
      font-weight: 600;
      color: var(--text);
      min-height: 44px;
    }

    .day-preview i { color: var(--accent); font-size: 14px; }

    /* ─── BUTTONS ────────────────────────────────── */

    .btn-row {
      display: flex;
      gap: 12px;
      margin-top: 28px;
    }

    .btn {
      flex: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 10px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      border: none;
      text-decoration: none;
      transition: background 0.2s, transform 0.15s;
    }

    .btn:hover { transform: translateY(-1px); }

    .btn-primary {
      background: var(--accent);
      color: #fff;
    }

    .btn-primary:hover { background: #1d4ed8; }

    .btn-secondary {
      background: #f1f5f9;
      color: var(--muted);
    }

    .btn-secondary:hover { background: #e2e8f0; color: var(--text); }

    /* ─── RESPONSIVE ─────────────────────────────── */

    @media (max-width: 768px) {
      .sidebar { width: 0; overflow: hidden; }
      .wrapper { margin-left: 0; }
      .content { padding: 20px 16px; }
      .form-row { grid-template-columns: 1fr; }
    }

  </style>
</head>
<body>

<!-- ─── SIDEBAR ──────────────────────────────── -->
<aside class="sidebar">

  <div class="sidebar-logo">JADWAL <span>APP</span></div>

  <div class="sidebar-profile">
    <div class="sidebar-avatar"><i class="fa fa-user-graduate"></i></div>
    <div class="sidebar-profile-info">
      <p>Login sebagai</p>
      <strong><?= htmlspecialchars($_SESSION['nama']); ?></strong>
    </div>
  </div>

  <ul class="sidebar-menu">
    <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
    <li class="active"><a href="jadwal.php"><i class="fa fa-calendar"></i> Jadwal Saya</a></li>
    <li><a href="mata_pelajaran.php"><i class="fa fa-book"></i> Mata Pelajaran</a></li>
    <li><a href="reminder.php"><i class="fa fa-bell"></i> Reminder</a></li>
    <li><a href="tugas.php"><i class="fa fa-list-check"></i> Tugas</a></li>
    <li class="logout"><a href="../auth/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
  </ul>

</aside>

<!-- ─── WRAPPER ──────────────────────────────── -->
<div class="wrapper">

  <header class="topbar">
    <h3>Edit Jadwal</h3>
    <div class="topbar-right">
      <i class="fa fa-user-circle"></i>
      <span><?= htmlspecialchars($_SESSION['nama']); ?></span>
    </div>
  </header>

  <main class="content">

    <div class="form-card">

      <!-- Header -->
      <div class="form-card-header">
        <div class="header-icon"><i class="fa fa-calendar-pen"></i></div>
        <div>
          <h2>Edit Jadwal</h2>
          <p>Perubahan akan langsung tersimpan ke jadwal kamu</p>
        </div>
      </div>

      <div class="form-body">

        <!-- Validation errors -->
        <?php if (!empty($errors)) : ?>
          <div class="error-box">
            <strong><i class="fa fa-circle-exclamation"></i> Terdapat kesalahan:</strong>
            <ul>
              <?php foreach ($errors as $e) : ?>
                <li><?= htmlspecialchars($e); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST">

          <!-- Tanggal + Hari (auto-derived) -->
          <div class="form-row">

            <div class="form-group">
              <label>Tanggal <span>*</span></label>
              <input
                type="date"
                name="tanggal"
                id="tanggal"
                class="form-control"
                value="<?= htmlspecialchars($data['tanggal']); ?>"
                required
              >
            </div>

            <div class="form-group">
              <label>Hari</label>
              <div class="day-preview" id="hariPreview">
                <i class="fa fa-calendar-day"></i>
                <span id="hariText">
                  <?php
                    // Show hari from DB if tanggal is set
                    if (!empty($data['tanggal'])) {
                        $hariMap = [
                            'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
                            'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'
                        ];
                        echo $hariMap[date('l', strtotime($data['tanggal']))] ?? '—';
                    } else {
                        echo '—';
                    }
                  ?>
                </span>
              </div>
            </div>

          </div>

          <!-- Jam Mulai + Jam Selesai -->
          <div class="form-row">

            <div class="form-group">
              <label>Jam Mulai <span>*</span></label>
              <input
                type="time"
                name="jam_mulai"
                id="jamMulai"
                class="form-control time"
                value="<?= htmlspecialchars($data['jam_mulai']); ?>"
                required
              >
            </div>

            <div class="form-group">
              <label>Jam Selesai <span>*</span></label>
              <input
                type="time"
                name="jam_selesai"
                id="jamSelesai"
                class="form-control time"
                value="<?= htmlspecialchars($data['jam_selesai']); ?>"
                required
              >
            </div>

          </div>

          <!-- Mata Pelajaran -->
          <div class="form-group">
            <label>Mata Pelajaran <span>*</span></label>
            <input
              type="text"
              name="mata_pelajaran"
              class="form-control"
              value="<?= htmlspecialchars($data['mata_pelajaran']); ?>"
              placeholder="Contoh: Matematika"
              required
            >
          </div>

          <!-- Guru -->
          <div class="form-group">
            <label>Guru</label>
            <input
              type="text"
              name="guru"
              class="form-control"
              value="<?= htmlspecialchars($data['guru']); ?>"
              placeholder="Contoh: Pak Budi"
            >
          </div>

          <!-- Ruangan -->
          <div class="form-group">
            <label>Ruangan</label>
            <input
              type="text"
              name="ruangan"
              class="form-control"
              value="<?= htmlspecialchars($data['ruangan']); ?>"
              placeholder="Contoh: Lab Komputer 1"
            >
          </div>

          <!-- Buttons -->
          <div class="btn-row">
            <a href="jadwal.php" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-floppy-disk"></i> Simpan Perubahan
            </button>
          </div>

        </form>

      </div><!-- /form-body -->
    </div><!-- /form-card -->

  </main>
</div>

<script>
  // Auto-update the Hari preview when date changes
  const hariMap = {
    0: 'Minggu', 1: 'Senin', 2: 'Selasa', 3: 'Rabu',
    4: 'Kamis',  5: 'Jumat', 6: 'Sabtu'
  };

  const tanggalInput = document.getElementById('tanggal');
  const hariText     = document.getElementById('hariText');

  tanggalInput.addEventListener('change', function () {
    if (this.value) {
      // Parse as local date to avoid timezone shift
      const [y, m, d] = this.value.split('-').map(Number);
      const day = new Date(y, m - 1, d).getDay();
      hariText.textContent = hariMap[day];
    } else {
      hariText.textContent = '—';
    }
  });

  // Validate jam selesai > jam mulai client-side
  const jamMulai   = document.getElementById('jamMulai');
  const jamSelesai = document.getElementById('jamSelesai');

  function validateJam() {
    if (jamMulai.value && jamSelesai.value && jamSelesai.value <= jamMulai.value) {
      jamSelesai.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
    } else {
      jamSelesai.setCustomValidity('');
    }
  }

  jamMulai.addEventListener('change', validateJam);
  jamSelesai.addEventListener('change', validateJam);
</script>

</body>
</html>
