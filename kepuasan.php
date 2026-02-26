<?php
session_start();

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/db.php";

$_SESSION['tipe_form'] = 'kepuasan';

$allowed_services = [
  'admisi',
  'igd',
  'lab',
  'farmasi',
  'radiologi',
  'gizi',
  'icu',
  'operasi',
  'rawat_jalan',
  'rawat_inap',
  'laboratorium'
];

try {
  $pelayanan_rows = $pdo->query("SELECT id, nama FROM pelayanan ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
  error_log($e->getMessage(), 3, __DIR__ . "/logs/error.log");
  exit("Gagal mengambil data pelayanan");
}

$service_slug_to_id = [];
foreach ($pelayanan_rows as $r) {
  $nama = strtolower(trim((string)$r['nama']));
  $slug = preg_replace('/[^a-z0-9]+/', '_', $nama);
  $slug = trim((string)$slug, '_');
  $service_slug_to_id[$slug] = (int)$r['id'];
}


$service_locked = false;
$service_from_url_id = null;

if (isset($_GET['service'])) {
  $candidate = strtolower(trim((string)$_GET['service']));
  if (in_array($candidate, $allowed_services, true) && isset($service_slug_to_id[$candidate])) {
    $service_locked = true;
    $service_from_url_id = (int)$service_slug_to_id[$candidate];
  }
}


$serverDate_open  = date('Y-m-d');
$serverClock_open = date('H:i');


try {
  $jenisKelamin_rows = $pdo->query("SELECT * FROM jenis_kelamin ORDER BY id ASC")->fetchAll();
  $pendidikan_rows   = $pdo->query("SELECT * FROM pendidikan ORDER BY id ASC")->fetchAll();
  $pekerjaan_rows    = $pdo->query("SELECT * FROM pekerjaan ORDER BY id ASC")->fetchAll();
  $penjamin_rows     = $pdo->query("SELECT * FROM penjamin ORDER BY id ASC")->fetchAll();
  $pertanyaan_rows   = $pdo->query("SELECT * FROM pertanyaan ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
  error_log($e->getMessage(), 3, __DIR__ . "/logs/error.log");
  exit("Gagal mengambil master data");
}


$bpjs_id = null;
foreach ($penjamin_rows as $row) {
  if (strtoupper(trim((string)$row['nama'])) === 'BPJS') {
    $bpjs_id = (int)$row['id'];
    break;
  }
}


try {
  $stmtQ4 = $pdo->query("
      SELECT id
      FROM pertanyaan
      WHERE LOWER(deskripsi) LIKE '%biaya%'
         OR LOWER(deskripsi) LIKE '%tarif%'
      ORDER BY id ASC
      LIMIT 1
    ");
  $Q4_ID = (int)($stmtQ4->fetchColumn() ?: 0);
} catch (PDOException $e) {
  $Q4_ID = 0;
}
if (!$Q4_ID) $Q4_ID = 4;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  if (isset($_POST['validate_only']) && $_POST['validate_only'] === '1') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
    exit;
  }

  $surveyDateFinal = date('Y-m-d');
  $surveyTimeFinal = date('H:i');


  $valid = true;
  $required = ['jenis_kelamin', 'pendidikan', 'pekerjaan', 'penjamin'];
  foreach ($required as $f) {
    if (!isset($_POST[$f]) || trim((string)$_POST[$f]) === '') {
      $valid = false;
      break;
    }
  }

  if ($service_locked) {
    $pelayanan_id = (int)$service_from_url_id;
  } else {
    $pelayanan_id = isset($_POST['pelayanan']) ? (int)$_POST['pelayanan'] : 0;
    if (!$pelayanan_id) $valid = false;
  }

  $penjamin_id = isset($_POST['penjamin']) ? (int)$_POST['penjamin'] : 0;
  if (!$penjamin_id) $valid = false;
  $is_bpjs = ($bpjs_id !== null && $penjamin_id === (int)$bpjs_id);


  $pertanyaanIds = array_map(fn($r) => (int)$r['id'], $pertanyaan_rows);
  foreach ($pertanyaanIds as $pid) {
    $field = 'nilai' . $pid;
    if ($pid === $Q4_ID && $is_bpjs) continue;
    if (!isset($_POST[$field]) || (string)$_POST[$field] === '') {
      $valid = false;
      break;
    }
  }

  if (!$valid) {
    $_SESSION['form_error'] = "Mohon lengkapi semua isian yang wajib diisi.";
    $_SESSION['old_input'] = $_POST;
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['service']) ? '?service=' . $_GET['service'] : ''));
    exit;
  }


  try {
    $pdo->beginTransaction();


    $sqlProfil = "INSERT INTO profil (jenis_kelamin_id, pendidikan_id, pekerjaan_id, pelayanan_id, penjamin_id) 
                  VALUES (:jk, :pd, :pk, :pl, :pn) RETURNING id";
    $stmtProfil = $pdo->prepare($sqlProfil);
    $stmtProfil->execute([
      ':jk' => (int)$_POST['jenis_kelamin'],
      ':pd' => (int)$_POST['pendidikan'],
      ':pk' => (int)$_POST['pekerjaan'],
      ':pl' => (int)$pelayanan_id,
      ':pn' => (int)$penjamin_id,
    ]);
    $profil_id = (int)$stmtProfil->fetchColumn();
    if (!$profil_id) throw new RuntimeException("Gagal mendapatkan ID profil");


    $sqlSurvei = "INSERT INTO survei (profil_id) VALUES (:pid) RETURNING id";
    $stmtSurvei = $pdo->prepare($sqlSurvei);
    $stmtSurvei->execute([':pid' => (int)$profil_id]);
    $survei_id = (int)$stmtSurvei->fetchColumn();
    if (!$survei_id) throw new RuntimeException("Gagal mendapatkan ID survei");


    $stmtIns = $pdo->prepare("INSERT INTO kuisioner (pertanyaan_id, nilai, survei_id, survey_date, survey_time) 
                              VALUES (:pid, :nilai, :survei_id, :sdate, :stime)");
    foreach ($pertanyaanIds as $pid) {
      $field = 'nilai' . $pid;
      $nilai = ($pid === $Q4_ID && $is_bpjs) ? 4 : (int)$_POST[$field];
      $stmtIns->execute([
        ':pid' => (int)$pid,
        ':nilai' => (int)$nilai,
        ':survei_id' => (int)$survei_id,
        ':sdate' => $surveyDateFinal,
        ':stime' => $surveyTimeFinal,
      ]);
    }

    $pdo->commit();


    unset($_SESSION['form_error'], $_SESSION['old_input']);
    header("Location: thank-you.php");
    exit;
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage(), 3, __DIR__ . "/logs/error.log");
    $_SESSION['form_error'] = "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.";
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['service']) ? '?service=' . $_GET['service'] : ''));
    exit;
  }
}


$form_error = $_SESSION['form_error'] ?? null;
$old_input = $_SESSION['old_input'] ?? [];


unset($_SESSION['form_error'], $_SESSION['old_input']);
?>

<?php
$pageTitle = "KUESIONER SURVEI KEPUASAN PASIEN";
include 'layout/header.php';
?>

<div class="form-container">
  <?php if (!empty($form_error)): ?>
    <div class="error-message" style="background:#fee;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #c00;font-weight:500;">
      ⚠️ <?php echo htmlspecialchars($form_error); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="" id="kepuasanForm" novalidate>


    <div class="date-time-section" style="display:flex; gap:15px; align-items:flex-end; justify-content:space-between; flex-wrap:wrap;">
      <div class="input-group" style="flex:1; min-width:100%; margin-bottom:10px;">
        <label style="font-weight:600; margin-bottom:8px; display:block; font-size:14px;">Tanggal Survei</label>
        <input type="text" value="<?= htmlspecialchars($serverDate_open) ?>" readonly
          style="width:100%;padding:14px 16px;border:1px solid rgba(0,0,0,.15);border-radius:10px;background:#f7f9fb;color:#111;font-weight:600;letter-spacing:.3px;box-shadow:0 2px 8px rgba(0,0,0,.06);cursor:not-allowed;outline:none;font-size:16px;">
      </div>
      <div class="input-group" style="flex:1; min-width:100%; margin-bottom:15px;">
        <label style="font-weight:600; margin-bottom:8px; display:block; font-size:14px;">Jam Survei</label>
        <input type="text" value="<?= htmlspecialchars($serverClock_open . ' WIB') ?>" readonly
          style="width:100%;padding:14px 16px;border:1px solid rgba(0,0,0,.15);border-radius:10px;background:#f7f9fb;color:#111;font-weight:600;letter-spacing:.3px;box-shadow:0 2px 8px rgba(0,0,0,.06);cursor:not-allowed;outline:none;font-size:16px;">
      </div>
    </div>

    <h2 class="section-title" style="font-size:20px; margin:25px 0 15px;">Profil Pasien</h2>
    <div class="profile-section">

      <div class="form-row" data-field="jenis_kelamin" style="margin-bottom:25px;">
        <div class="form-field">
          <label style="font-weight:600; margin-bottom:12px; display:block; font-size:15px;">Jenis Kelamin <span style="color:#e74c3c;">*</span></label>
          <span style="color:#e74c3c; font-size:13px; display:block; margin-bottom:12px;">(Pilih salah satu)</span>

          <div class="radio-group" style="display: flex; flex-direction: column; gap: 15px; margin-top: 10px;">
            <?php foreach ($jenisKelamin_rows as $row): ?>
              <?php $genderLabel = ((string)$row['nama'] === 'L') ? 'Laki-laki' : 'Perempuan'; ?>
              <?php $id = strtolower(str_replace(' ', '_', $genderLabel)); ?>
              <div class="radio-option" style="display: flex; align-items: center; gap: 12px; padding:12px; border:1px solid rgba(0,0,0,0.1); border-radius:10px; background:#fff; transition:all 0.2s ease;">
                <input type="radio" id="<?= $id ?>" name="jenis_kelamin" value="<?= (int)$row['id'] ?>" required
                  style="width: 20px; height: 20px; cursor: pointer; flex-shrink:0; accent-color:#2563eb;">
                <label for="<?= $id ?>" style="cursor: pointer; font-weight: 500; padding: 5px 0; flex:1; font-size:15px;"><?= htmlspecialchars($genderLabel) ?></label>
              </div>
            <?php endforeach; ?>
          </div>

        </div>
      </div>

      <div class="form-row" data-field="pendidikan" style="margin-bottom:25px;">
        <div class="form-field">
          <label style="font-weight:600; margin-bottom:12px; display:block; font-size:15px;">Pendidikan <span style="color:#e74c3c;">*</span></label>
          <span style="color:#e74c3c; font-size:13px; display:block; margin-bottom:12px;">(Pilih salah satu)</span>
          <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($pendidikan_rows as $row): ?>
              <?php $edu = (string)$row['nama'];
              $id = strtolower(str_replace(' ', '_', $edu)); ?>
              <div class="radio-option" style="display: flex; align-items: center; gap: 12px; padding:12px; border:1px solid rgba(0,0,0,0.1); border-radius:10px; background:#fff;">
                <input type="radio" id="<?= $id ?>" name="pendidikan" value="<?= (int)$row['id'] ?>" required
                  style="width: 20px; height: 20px; cursor: pointer; flex-shrink:0; accent-color:#2563eb;">
                <label for="<?= $id ?>" style="cursor: pointer; font-weight: 500; flex:1; font-size:15px;"><?= htmlspecialchars($edu) ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="form-row" data-field="pekerjaan" style="margin-bottom:25px;">
        <div class="form-field">
          <label style="font-weight:600; margin-bottom:12px; display:block; font-size:15px;">Pekerjaan <span style="color:#e74c3c;">*</span></label>
          <span style="color:#e74c3c; font-size:13px; display:block; margin-bottom:12px;">(Pilih salah satu)</span>
          <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($pekerjaan_rows as $row): ?>
              <?php $job = (string)$row['nama'];
              $id = strtolower(str_replace(' ', '_', $job)); ?>
              <div class="radio-option" style="display: flex; align-items: center; gap: 12px; padding:12px; border:1px solid rgba(0,0,0,0.1); border-radius:10px; background:#fff;">
                <input type="radio" id="<?= $id ?>" name="pekerjaan" value="<?= (int)$row['id'] ?>" required
                  style="width: 20px; height: 20px; cursor: pointer; flex-shrink:0; accent-color:#2563eb;">
                <label for="<?= $id ?>" style="cursor: pointer; font-weight: 500; flex:1; font-size:15px;"><?= htmlspecialchars($job) ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>


      <div class="form-row" data-field="pelayanan" style="margin-bottom:25px;">
        <div class="form-field">
          <label style="font-weight:600; margin-bottom:12px; display:block; font-size:15px;">Jenis Layanan <span style="color:#e74c3c;">*</span></label>
          <span style="color:#e74c3c; font-size:13px; display:block; margin-bottom:12px;">(Pilih salah satu)</span>
          <?php if ($service_locked): ?>
            <div class="complaint-note" style="margin:10px 0; padding:12px; background:#e0f2fe; border-left:4px solid #0284c7; border-radius:6px; font-size:14px; color:#0369a1;">
              ℹ️ Jenis layanan sudah ditentukan oleh link dan tidak bisa diubah.
            </div>
            <input type="hidden" name="pelayanan" value="<?= (int)$service_from_url_id ?>">
          <?php endif; ?>
          <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($pelayanan_rows as $row): ?>
              <?php
              $serviceName = (string)$row['nama'];
              $serviceId   = (int)$row['id'];
              $slug = preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($serviceName)));
              $slug = trim((string)$slug, '_');
              $inputId  = 'svc_' . $serviceId;
              $checked  = ($service_locked && $serviceId === (int)$service_from_url_id) ? 'checked' : '';
              $disabled = $service_locked ? 'disabled' : '';
              $required = $service_locked ? '' : 'required';
              ?>
              <div class="radio-option" style="display: flex; align-items: center; gap: 12px; padding:12px; border:1px solid rgba(0,0,0,0.1); border-radius:10px; background:#fff; <?= $disabled ? 'opacity:0.6;' : '' ?>">
                <input type="radio" id="<?= $inputId ?>" name="pelayanan" value="<?= $serviceId ?>"
                  data-slug="<?= htmlspecialchars($slug) ?>" <?= $checked ?> <?= $disabled ?> <?= $required ?>
                  style="width: 20px; height: 20px; cursor: pointer; flex-shrink:0; accent-color:#2563eb;">
                <label for="<?= $inputId ?>" style="cursor: pointer; font-weight: 500; flex:1; font-size:15px;"><?= htmlspecialchars($serviceName) ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="form-row" data-field="penjamin" style="margin-bottom:25px;">
        <div class="form-field">
          <label style="font-weight:600; margin-bottom:12px; display:block; font-size:15px;">Penjamin <span style="color:#e74c3c;">*</span></label>
          <span style="color:#e74c3c; font-size:13px; display:block; margin-bottom:12px;">(Pilih salah satu)</span>
          <div class="radio-group" style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($penjamin_rows as $row): ?>
              <?php $pjm = (string)$row['nama'];
              $pid = (int)$row['id'];
              $inputId = 'penjamin_' . $pid; ?>
              <div class="radio-option" style="display: flex; align-items: center; gap: 12px; padding:12px; border:1px solid rgba(0,0,0,0.1); border-radius:10px; background:#fff;">
                <input type="radio" id="<?= $inputId ?>" name="penjamin" value="<?= $pid ?>" required
                  style="width: 20px; height: 20px; cursor: pointer; flex-shrink:0; accent-color:#2563eb;">
                <label for="<?= $inputId ?>" style="cursor: pointer; font-weight: 500; flex:1; font-size:15px;"><?= htmlspecialchars($pjm) ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <h2 class="section-title" style="font-size:20px; margin:30px 0 15px;">Pertanyaan Kepuasan Layanan</h2>
    <div class="scale-note" style="background:#f0f9ff; padding:12px 15px; border-radius:8px; margin-bottom:20px; font-size:13px; color:#0369a1; border-left:4px solid #0284c7;">
      📊 <strong>Skala Penilaian:</strong> 1 = Tidak Sesuai | 2 = Kurang Sesuai | 3 = Sesuai | 4 = Sangat Sesuai
    </div>

    <div class="questions-section">
      <?php
      $qnum = 1;
      foreach ($pertanyaan_rows as $row) {
        $pid  = (int)$row['id'];
        $desc = htmlspecialchars((string)$row['deskripsi']);
        $nilai_name = 'nilai' . $pid;
        $wrapId = ($pid === $Q4_ID) ? 'question-q4' : '';
      ?>
        <div class="question-card" <?= $wrapId ? 'id="' . $wrapId . '"' : '' ?> data-field="nilai<?= $pid ?>"
          style="background:#fff; border:1px solid rgba(0,0,0,0.1); border-radius:12px; padding:18px; margin-bottom:18px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
          <div class="question-text" style="font-weight:600; margin-bottom:15px; font-size:15px; line-height:1.5; color:#1e293b;"><?= $qnum . '. ' . $desc ?></div>
          <div class="question-options" style="display: flex; gap: 10px; justify-content: space-between;">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <div class="option" style="flex:1; text-align:center;">
                <input type="radio" name="<?= $nilai_name ?>" value="<?= $i ?>" id="q<?= $pid ?>_<?= $i ?>"
                  <?= ($pid !== $Q4_ID) ? 'data-required="true"' : '' ?>
                  style="width:100%; max-width:50px; height:44px; cursor:pointer; accent-color:#2563eb;">
                <label for="q<?= $pid ?>_<?= $i ?>" style="display:block; margin-top:6px; font-size:13px; font-weight:500; color:#64748b;"><?= $i ?></label>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      <?php $qnum++;
      } ?>
    </div>

    <div class="complaint-note" style="background:#fef3c7; padding:15px; border-radius:10px; margin:25px 0; font-size:14px; color:#92400e; border-left:4px solid #f59e0b; text-align:center;">
      💡 Masukan Anda sangat berarti bagi kami untuk meningkatkan kualitas layanan
    </div>

    <button type="submit" class="submit-btn">
      <i class="fas fa-paper-plane"></i> Kirim Kuesioner
    </button>
  </form>
</div>


<div id="validationModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-icon">!</div>
    <h3 class="modal-title">Form Belum Lengkap</h3>
    <p id="modalMessage" class="modal-text"></p>
    <button type="button" id="closeModal" class="modal-btn">Mengerti</button>
  </div>
</div>

<style>
  * {
    box-sizing: border-box;
    -webkit-tap-highlight-color: transparent;
  }

  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    margin: 0;
    padding: 0;
    background: #f1f5f9;
    font-size: 16px;
    line-height: 1.5;
  }

  .form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px 15px;
  }

  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(6px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
  }

  .modal-box {
    width: 100%;
    max-width: 340px;
    min-height: 340px;
    background: #fff;
    border-radius: 22px;
    padding: 35px 25px;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: scaleIn .25s ease;
  }

  .modal-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #ff6b6b, #ff3b3b);
    color: white;
    font-size: 32px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .modal-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #1e293b;
  }

  .modal-text {
    font-size: 15px;
    color: #64748b;
    margin-bottom: 25px;
    line-height: 1.5;
  }

  .modal-btn {
    background: linear-gradient(135deg, #0e97d6, #0ea5e9);
    border: none;
    color: white;
    padding: 14px 28px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    width: 100%;
    transition: all 0.2s ease;
  }

  .modal-btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
  }

  .modal-btn:active {
    transform: translateY(0);
  }

  @keyframes scaleIn {
    from {
      opacity: 0;
      transform: scale(.9);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .error-highlight {
    border: 2px solid #e74c3c !important;
    border-radius: 10px;
    padding: 10px;
    background: rgba(231, 76, 60, 0.08);
    transition: all 0.2s ease;
    animation: shake 0.5s ease-in-out;
  }

  @keyframes shake {

    0%,
    100% {
      transform: translateX(0);
    }

    25% {
      transform: translateX(-5px);
    }

    75% {
      transform: translateX(5px);
    }
  }

  .error-highlight .radio-option label {
    color: #c0392b;
    font-weight: 600;
  }

  .section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin: 30px 0 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
  }

  .submit-btn {
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, #0ea5e9 0%, #14b8a6 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
    transition: all 0.2s ease;
    margin-top: 20px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: relative;
    overflow: hidden;
  }

  .submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
  }

  .submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(14, 165, 233, 0.45);
  }

  .submit-btn:hover::before {
    left: 100%;
  }

  .submit-btn:active {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
  }

  .submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  .submit-btn i {
    transition: transform 0.2s ease;
    font-size: 14px;
  }

  .submit-btn:hover i {
    transform: translateX(3px);
  }

  .submit-btn.loading {
    pointer-events: none;
    color: transparent;
  }

  .submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  @media (max-width: 640px) {
    .form-container {
      padding: 15px 12px;
    }

    .section-title {
      font-size: 18px;
      margin: 25px 0 12px;
    }

    .question-card {
      padding: 15px 12px;
      margin-bottom: 15px;
    }

    .question-text {
      font-size: 14px !important;
      margin-bottom: 12px;
    }

    .question-options {
      gap: 8px !important;
    }

    .option input[type="radio"] {
      height: 40px !important;
      max-width: 45px;
    }

    .option label {
      font-size: 12px !important;
    }

    .radio-option {
      padding: 14px !important;
    }

    .radio-option input[type="radio"] {
      width: 22px !important;
      height: 22px !important;
    }

    .radio-option label {
      font-size: 15px !important;
    }

    .modal-box {
      padding: 30px 20px;
      min-height: 320px;
    }

    .modal-title {
      font-size: 18px;
    }

    .modal-text {
      font-size: 14px;
    }

    .submit-btn {
      padding: 13px 20px;
      font-size: 14px;
      margin-top: 15px;
      margin-bottom: 12px;
      position: relative;
    }
  }

  @media (max-width: 380px) {
    .submit-btn {
      padding: 12px 18px;
      font-size: 13px;
    }

    .question-options {
      flex-wrap: wrap;
      gap: 10px !important;
    }

    .option {
      flex: 0 0 calc(50% - 5px) !important;
    }

    .option input[type="radio"] {
      height: 44px !important;
      max-width: 100%;
    }

    .modal-box {
      padding: 25px 18px;
    }
  }

  @media (min-width: 641px) {
    .date-time-section {
      flex-direction: row !important;
    }

    .input-group {
      min-width: calc(50% - 10px) !important;
      margin-bottom: 0 !important;
    }
  }

  @media (hover: none) {
    .radio-option:active {
      background: #f1f5f9;
    }

    .submit-btn:active {
      opacity: 0.95;
    }

    input[type="radio"],
    button {
      min-height: 44px;
    }
  }

  @media print {

    .modal-overlay,
    .submit-btn {
      display: none !important;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const SERVICE_LOCKED = <?= $service_locked ? 'true' : 'false' ?>;
    const BPJS_ID = <?= ($bpjs_id === null ? 'null' : (int)$bpjs_id) ?>;
    const Q4_ID = <?= (int)$Q4_ID ?>;


    if (!SERVICE_LOCKED) {
      document.querySelectorAll('input[name="pelayanan"]').forEach(radio => {
        radio.addEventListener('change', () => {
          const slug = radio.dataset.slug;
          if (!slug) return;
          const url = new URL(window.location.href);
          url.searchParams.set('service', slug);
          window.history.pushState({}, '', url);
        });
      });
    }


    const q4Section = document.getElementById('question-q4');
    const q4Radios = q4Section ? q4Section.querySelectorAll('input[type="radio"]') : [];

    function setQ4Required(isRequired) {
      q4Radios.forEach(r => r.required = !!isRequired);
    }

    function disableQ4() {
      if (!q4Section) return;
      q4Section.style.opacity = '0.5';
      q4Section.style.pointerEvents = 'none';
      q4Radios.forEach(r => {
        r.checked = false;
        r.disabled = true;
        r.required = false;
      });
    }

    function enableQ4() {
      if (!q4Section) return;
      q4Section.style.opacity = '1';
      q4Section.style.pointerEvents = 'auto';
      q4Radios.forEach(r => {
        r.disabled = false;
        r.required = true;
      });
    }

    function syncQ4ByPenjamin() {
      if (BPJS_ID === null) return;
      const selected = document.querySelector('input[name="penjamin"]:checked');
      if (!selected) return;
      const isBpjs = parseInt(selected.value, 10) === parseInt(BPJS_ID, 10);
      if (isBpjs) disableQ4();
      else enableQ4();
    }
    document.querySelectorAll('input[name="penjamin"]').forEach(r => {
      r.addEventListener('change', syncQ4ByPenjamin);
    });
    syncQ4ByPenjamin();


    const form = document.getElementById('kepuasanForm');
    const modal = document.getElementById('validationModal');
    const modalMessage = document.getElementById('modalMessage');
    const closeModal = document.getElementById('closeModal');
    let isSubmitting = false;

    function showModal(msg) {
      modalMessage.textContent = msg;
      modal.style.display = 'flex';
    }

    function scrollToEl(el) {
      el.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
    }

    function clearErrorHighlights() {
      document.querySelectorAll('.error-highlight').forEach(el => {
        el.classList.remove('error-highlight');
      });
    }

    function checkRadioGroup(name) {
      return document.querySelector(`input[name="${name}"]:checked`) !== null;
    }

    function getRadioGroupContainer(name) {
      const checked = document.querySelector(`input[name="${name}"]:checked`);
      if (checked) {
        return checked.closest('.form-row') || checked.closest('.question-card');
      }
      const radio = document.querySelector(`input[name="${name}"]`);
      return radio ? (radio.closest('.form-row') || radio.closest('.question-card')) : null;
    }

    function highlightField(name) {
      const container = getRadioGroupContainer(name);
      if (container) {
        container.classList.add('error-highlight');
        return container;
      }
      return null;
    }

    closeModal.onclick = () => {
      modal.style.display = 'none';
    };
    modal.onclick = (e) => {
      if (e.target === modal) modal.style.display = 'none';
    };
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        modal.style.display = 'none';
      }
    });

    form.addEventListener('submit', function(e) {
      if (isSubmitting) return true;

      e.preventDefault();
      clearErrorHighlights();

      const profileFields = [{
          name: 'jenis_kelamin',
          label: 'Jenis Kelamin'
        },
        {
          name: 'pendidikan',
          label: 'Pendidikan'
        },
        {
          name: 'pekerjaan',
          label: 'Pekerjaan'
        },
        {
          name: 'penjamin',
          label: 'Penjamin'
        }
      ];

      for (const field of profileFields) {
        if (!checkRadioGroup(field.name)) {
          const el = highlightField(field.name);
          if (el) scrollToEl(el);
          showModal(`${field.label} wajib dipilih.`);
          return false;
        }
      }

      if (!SERVICE_LOCKED && !checkRadioGroup('pelayanan')) {
        const el = highlightField('pelayanan');
        if (el) scrollToEl(el);
        showModal('Jenis Layanan wajib dipilih.');
        return false;
      }

      const questionCards = document.querySelectorAll('.question-card');
      const isBpjsSelected = BPJS_ID !== null &&
        document.querySelector(`input[name="penjamin"]:checked`)?.value == BPJS_ID;

      for (const card of questionCards) {
        const fieldName = card.dataset.field;
        if (!fieldName) continue;

        const pid = parseInt(fieldName.replace('nilai', ''), 10);
        if (pid === Q4_ID && isBpjsSelected) continue;

        if (!checkRadioGroup(fieldName)) {
          card.classList.add('error-highlight');
          scrollToEl(card);
          showModal('Mohon jawab semua pertanyaan kepuasan.');
          return false;
        }
      }

      isSubmitting = true;
      form.submit();
      return true;
    });
  });
</script>

<?php include 'layout/footer.php'; ?>