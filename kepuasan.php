<?php
session_start();
require_once "config.php";

/* =========================
   KONEKSI DATABASE
   ========================= */
$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$conn) {
  die("Koneksi PostgreSQL gagal: " . htmlspecialchars(pg_last_error()));
}
$_SESSION['tipe_form'] = 'kepuasan';
/* =========================
   LOCK SERVICE VIA URL ?service=...
   ========================= */
$allowed_services = [
  'admisi',
  'igd',
  'lab',
  'farmasi',
  'radiologi',
  'gizi',
  'icu',
  'operasi',
  'rawat_jalan'
];

$service_locked = false;
$service_from_url = '';

if (isset($_GET['service'])) {
  $candidate = strtolower(trim((string)$_GET['service']));
  if (in_array($candidate, $allowed_services, true)) {
    $service_locked = true;
    $service_from_url = $candidate;
  }
}

$date = date('Y-m-d');
$time = date('H:i');

function is_time_in_range($time, $start, $end)
{
  $time = strtotime($time);
  $start = strtotime($start);
  $end = strtotime($end);
  return ($time >= $start) && ($time <= $end);
}

$jenisKelamin = pg_query($conn, "SELECT * FROM jenis_kelamin ORDER BY id ASC");
$pendidikan = pg_query($conn, "SELECT * FROM pendidikan ORDER BY id ASC");
$pekerjaan = pg_query($conn, "SELECT * FROM pekerjaan ORDER BY id ASC");
$pelayanan = pg_query($conn, "SELECT * FROM pelayanan ORDER BY id ASC");
$penjamin = pg_query($conn, "SELECT * FROM penjamin ORDER BY id ASC");
$pertanyaan = pg_query($conn, "SELECT * FROM pertanyaan ORDER BY id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // INSERT PROFIL
  $sqlProfil = "
    INSERT INTO profil (
      jenis_kelamin_id, pendidikan_id, pekerjaan_id, pelayanan_id, penjamin_id
    ) VALUES ($1, $2, $3, $4, $5)
    RETURNING id
  ";

  $resultProfil = pg_query_params($conn, $sqlProfil, [
    $_POST['jenis_kelamin'],
    $_POST['pendidikan'],
    $_POST['pekerjaan'],
    $_POST['pelayanan'],
    $_POST['penjamin']
  ]);

  $profil_id = pg_fetch_assoc($resultProfil)['id'];

  // AMBIL ULANG PERTANYAAN
  $pertanyaan = pg_query($conn, "SELECT id FROM pertanyaan ORDER BY id ASC");

  // INSERT KUISIONER
  while ($row = pg_fetch_assoc($pertanyaan)) {
    $pid = $row['id'];

    if (!isset($_POST['nilai' . $pid])) continue;

    pg_query_params($conn, "
      INSERT INTO kuisioner (
        pertanyaan_id, nilai, profil_id, survey_date, survey_time
      ) VALUES ($1, $2, $3, $4, $5)
    ", [
      $pid,
      $_POST['nilai' . $pid],
      $profil_id,
      $_POST['surveyDate'],
      $_POST['surveyTime']
    ]);
  }

  header("Location: thank-you.php");
  exit;
}
?>


<?php
$pageTitle = "KUESIONER SURVEI KEPUASAN PASIEN";
include 'layout/header.php';
?>

<div class="form-container">
  <?php if (isset($error)): ?>
    <div class="error-message" style="background:#fee;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #c00;font-weight:500;">
      ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="date-time-section">
      <div class="input-group">
        <label for="surveyDate">Tanggal Survei</label>
        <input type="date" id="surveyDate" name="surveyDate" value="<?= $date ?>" required>
      </div>
      <div class="input-group">
        <label for="surveyTime">Jam Survei</label>
        <select id="surveyTime" name="surveyTime" required>
          <option value="">Pilih Jam</option>
          <?php
          if (is_time_in_range($time, '08:00', '12:00')) {
            echo '<option value="08-12" selected>08.00 - 12.00 WIB</option>';
            echo '<option value="12-18">12.00 - 18.00 WIB</option>';
          } elseif (is_time_in_range($time, '12:00', '18:00')) {
            echo '<option value="08-12">08.00 - 12.00 WIB</option>';
            echo '<option value="12-18" selected>12.00 - 18.00 WIB</option>';
          } else {
            echo '<option value="08-12">08.00 - 12.00 WIB</option>';
            echo '<option value="12-18">12.00 - 18.00 WIB</option>';
          }
          ?>
        </select>
      </div>
    </div>

    <h2 class="section-title">Profil Pasien</h2>
    <div class="profile-section">

      <div class="form-row">
        <div class="form-field">
          <label>Jenis Kelamin * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
          <div class="radio-group">
            <?php
            while ($row = pg_fetch_assoc($jenisKelamin)) {
              $gender = htmlspecialchars($row['nama'] === 'L' ? 'Laki-laki' : 'Perempuan');
              $id = strtolower(str_replace(' ', '_', $gender));
              echo '<div class="radio-option">';
              echo '<input type="radio" id="' . $id . '" name="jenis_kelamin" value="' . $row['id'] . '" required>';
              echo '<label for="' . $id . '">' . $gender . '</label>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>Pendidikan * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
          <div class="radio-group">
            <?php
            while ($row = pg_fetch_assoc($pendidikan)) {
              $edu = htmlspecialchars($row['nama']);
              $id = strtolower(str_replace(' ', '_', $edu));
              echo '<div class="radio-option">';
              echo '<input type="radio" id="' . $id . '" name="pendidikan" value="' . $row['id'] . '" required>';
              echo '<label for="' . $id . '">' . $edu . '</label>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Pekerjaan * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
          <div class="radio-group">
            <?php
            while ($row = pg_fetch_assoc($pekerjaan)) {
              $job = htmlspecialchars($row['nama']);
              $id = strtolower(str_replace(' ', '_', $job));
              echo '<div class="radio-option">';
              echo '<input type="radio" id="' . $id . '" name="pekerjaan" value="' . $row['id'] . '" required>';
              echo '<label for="' . $id . '">' . $job . '</label>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>

      <!-- =========================
               JENIS LAYANAN (RADIO) + LOCK
               ========================= -->
      <div class="form-row">
        <div class="form-field">
          <label>Jenis Layanan * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>

          <?php if ($service_locked): ?>
            <div class="complaint-note" style="margin-top:10px;">
              Jenis layanan sudah ditentukan oleh link dan tidak bisa diubah.
            </div>
            <!-- radio disabled gak ikut POST, jadi kirim via hidden -->
            <input type="hidden" name="service" value="<?php echo htmlspecialchars($service_from_url); ?>">
          <?php endif; ?>

          <div class="radio-group">
            <?php
            function svc_checked($svc, $current)
            {
              return $svc === $current ? 'checked' : '';
            }
            function svc_disabled($locked)
            {
              return $locked ? 'disabled' : '';
            }
            $current_service = $service_locked ? $service_from_url : '';
            ?>
            <?php
            while ($row = pg_fetch_assoc($pelayanan)) {
              $service = htmlspecialchars($row['nama']);
              $id = strtolower(str_replace(' ', '_', $service));
              echo '<div class="radio-option">';
              echo '<input type="radio" id="' . $id . '" name="pelayanan" value="' . $row['id'] . '" ' .
                svc_checked($service, $current_service) . ' ' .
                svc_disabled($service_locked) . '>';
              echo '<label for="' . $id . '">' . $service . '</label>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>Penjamin * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
          <div class="radio-group">
            <?php
            while ($row = pg_fetch_assoc($penjamin)) {
              $pjm = htmlspecialchars($row['nama']);
              $id = strtolower(str_replace(' ', '_', $pjm));
              $is_bpjs = ($pjm === 'BPJS') ? 'bpjs' : 'umum';
              echo '<div class="radio-option">';
              echo '<input type="radio" id="' . $id . '" name="penjamin" value="' . $row['id'] . '" class="' . $is_bpjs . '" required>';
              echo '<label for="' . $id . '">' . $pjm . '</label>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>

    <h2 class="section-title">Pertanyaan Kepuasan Layanan</h2>
    <div class="scale-note">Skala Penilaian: 1 = Tidak Sesuai | 2 = Kurang Sesuai | 3 = Sesuai | 4 = Sangat Sesuai</div>

    <div class="questions-section">
      <?php
      $qnum = 1;
      while ($row = pg_fetch_assoc($pertanyaan)) {
        $desc = htmlspecialchars($row['deskripsi']);
        $nilai_id = 'nilai' . $row['id'];
        $pertanyaan_id = 'pertanyaan' . $row['id'];
      ?>
        <div class="question-card">
          <div class="question-text">
            <?= $qnum . '. ' . $desc ?>
            <input hidden name="<?= $pertanyaan_id ?>" type="text" value="<?= $row['id'] ?>">
          </div>

          <div class="question-options">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <div class="option">
                <input
                  type="radio"
                  name="<?= $nilai_id ?>"
                  value="<?= $i ?>"
                  <?= $i === 1 ? 'required' : '' ?>>
                <label><?= $i ?></label>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      <?php
        $qnum++;
      }
      ?>
    </div>

    <input type="hidden" name="q4_bpjs" id="q4-bpjs" value="">

    <div class="complaint-note">
      Masukan Anda sangat berarti bagi kami untuk meningkatkan kualitas layanan
    </div>

    <button type="submit" class="submit-btn">Kirim Kuesioner</button>
  </form>
</div>

<?php include 'layout/footer.php'; ?>