<?php
session_start();
require_once "config.php";

/* =========================
   KONEKSI POSTGRESQL
   ========================= */
$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$conn) {
  die("Koneksi PostgreSQL gagal");
}
$_SESSION['tipe_form'] = 'keluhan';
/* =========================
   PROSES SUBMIT
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (
    empty($_POST['tanggal']) ||
    empty($_POST['pukul']) ||
    empty($_POST['alamat']) ||
    empty($_POST['no_hp']) ||
    empty($_POST['masukan'])
  ) {
    $error_message = "Tanggal, pukul, alamat, dan nomor HP wajib diisi.";
  } else {

    $sql = "INSERT INTO keluhan (
      alamat, no_hp, masukan, pukul, tanggal
    ) VALUES (
      $1, $2, $3, $4, $5
    )";

    $params = [
      $_POST['alamat'],
      $_POST['no_hp'],
      $_POST['masukan'] ?? null,
      $_POST['pukul'],
      $_POST['tanggal']
    ];

    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
      header("Location: thank-you.php");
      exit;
    } else {
      $error_message = "Gagal menyimpan data ke database.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keluhan & Saran Pasien - RS Ekahusada</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

  <div class="header">
    <div class="header-logo">
      <div class="logo-container">
        <img src="images/logo.png" class="logo-icon">
      </div>
      <div class="header-text">
        <h1>RS EKAHUSADA</h1>
        <p>FORMULIR KELUHAN & SARAN</p>
      </div>
    </div>
  </div>

  <div class="form-container">

    <?php if (isset($error_message)): ?>
      <div class="error-message">
        ⚠️ <?= htmlspecialchars($error_message) ?>
      </div>
    <?php endif; ?>

    <form method="POST">

      <h2 class="section-title">Identitas Pasien</h2>

      <!-- TANGGAL -->
      <div class="form-row">
        <div class="form-field">
          <label>Tanggal *</label>
          <input type="date" name="tanggal" required>
        </div>
      </div>

      <!-- PUKUL -->
      <div class="form-row">
        <div class="form-field">
          <label>Pukul *</label>
          <input class="form-profil" type="time" name="pukul" required>
        </div>
      </div>

      <!-- ALAMAT -->
      <div class="form-row">
        <div class="form-field">
          <label>Alamat *</label>
          <input class="form-profil" name="alamat" required placeholder="Contoh: Jl. Merdeka No. 123">
        </div>
      </div>

      <!-- NO HP -->
      <div class="form-row">
        <div class="form-field">
          <label>Nomor HP *</label>
          <input class="form-profil" type="tel" name="no_hp" required placeholder="Contoh: 081234567890">
        </div>
      </div>

      <h2 class="section-title">Keluhan / Saran</h2>

      <div class="form-row">
        <div class="form-field">
          <label>Uraian Keluhan / Saran</label>
          <textarea name="masukan" placeholder="Tuliskan keluhan atau saran Anda..."></textarea>
        </div>
      </div>

      <button type="submit" class="submit-btn">Kirim & Selesai</button>

    </form>
  </div>
</div>
</body>
</html>
