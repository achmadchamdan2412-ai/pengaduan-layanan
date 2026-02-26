<?php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/db.php";

$_SESSION['tipe_form'] = 'keluhan';

$conn = pg_connect("host=localhost port=5432 dbname=pengaduan_layanan user=postgres password=12345678");
if (!$conn) {
  die("Koneksi ke PostgreSQL gagal!");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (
    empty($_POST['tanggal']) ||
    empty($_POST['pukul']) ||
    empty($_POST['alamat']) ||
    empty($_POST['no_hp']) ||
    empty($_POST['masukan'])
  ) {
    $error_message = "Semua field wajib diisi.";
  } else {

    $sql = "
    INSERT INTO keluhan (
      alamat, no_hp, masukan, pukul, tanggal
    ) VALUES (
      $1, $2, $3, $4, $5
    )";

    $params = [
      $_POST['alamat'],
      $_POST['no_hp'],
      $_POST['masukan'],
      $_POST['pukul'],
      $_POST['tanggal']
    ];

    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
      header("Location: thank-you2.php");
      exit;
    } else {
      $error_message = "Gagal menyimpan data ke database.";
    }
  }
}
?>

<?php
$pageTitle = "FORMULIR KELUHAN & SARAN";
include 'layout/header.php';
?>

<div class="form-container">

  <?php if (isset($error_message)): ?>
    <div class="error-message" style="background:#fee;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #c00;font-weight:500;">
      ⚠️ <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate id="keluhanForm">

    <h2 class="section-title">Identitas Pasien</h2>

    <div class="form-row">
      <div class="form-field">
        <label>Tanggal *</label>
        <input type="date" name="tanggal" id="tanggal" readonly
          style="background:#f7f9fb; cursor:not-allowed; font-weight:600;">
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Pukul *</label>
        <input type="time" name="pukul" id="pukul" readonly
          style="background:#f7f9fb; cursor:not-allowed; font-weight:600;">
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Alamat *</label>
        <input name="alamat" placeholder="Contoh: Jl. Merdeka No. 123"
          autocomplete="street-address">
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Nomor HP *</label>
        <input
          type="tel"
          name="no_hp"
          placeholder="Contoh: 081234567890"
          inputmode="numeric"
          pattern="[0-9]{12}"
          maxlength="12"
          minlength="12"
          oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)"
          autocomplete="tel"
          required>
        <small class="helper-text" style="color:#64748b; font-size:12px; margin-top:4px; display:block;">
          Masukkan 12 digit nomor HP (contoh: 081234567890)
        </small>
      </div>
    </div>

    <h2 class="section-title">Keluhan / Saran</h2>

    <div class="form-row">
      <div class="form-field">
        <label>Uraian Keluhan / Saran *</label>
        <textarea name="masukan" placeholder="Tuliskan keluhan atau saran Anda..." rows="5"></textarea>
      </div>
    </div>

    <button type="submit" class="submit-btn">
      <i class="fas fa-paper-plane"></i> Kirim & Selesai
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
    color: #1e293b;
  }

  .form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px 15px;
  }


  .section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin: 30px 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
  }

  /* ===== FORM FIELD STYLES ===== */
  .form-row {
    margin-bottom: 20px;
  }

  .form-field label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 15px;
    color: #334155;
  }

  .form-field input,
  .form-field textarea {
    width: 100%;
    padding: 14px 16px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 16px;
    box-sizing: border-box;
    background: #fff;
    transition: all 0.2s ease;
    min-height: 48px;
  }

  .form-field textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
  }

  .form-field input:focus,
  .form-field textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  }

  .form-field input[readonly] {
    background: #f7f9fb;
    cursor: not-allowed;
    font-weight: 600;
    color: #475569;
  }


  .submit-btn {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    transition: all 0.2s ease;
    margin-top: 25px;
    margin-bottom: 20px;
    min-height: 52px;
  }

  .submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
  }

  .submit-btn:active {
    transform: translateY(0);
    opacity: 0.95;
  }


  .error-highlight {
    border: 2px solid #e74c3c !important;
    border-radius: 10px;
    background: rgba(231, 76, 60, 0.08);
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
    background: linear-gradient(135deg, #0ea5e9, #0f8ac4);
    border: none;
    color: white;
    padding: 14px 28px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    width: 100%;
    transition: all 0.2s ease;
    min-height: 48px;
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

  @media (max-width: 640px) {
    .form-container {
      padding: 15px 12px;
    }

    .section-title {
      font-size: 18px;
      margin: 25px 0 15px;
    }

    .form-field label {
      font-size: 14px;
      margin-bottom: 6px;
    }

    .form-field input,
    .form-field textarea {
      padding: 14px 15px;
      font-size: 16px;
      min-height: 50px;
    }

    .form-field textarea {
      min-height: 110px;
    }

    /* ===== SUBMIT BUTTON - MATCH HEADER ===== */
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
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }

    .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .submit-btn i {
      transition: transform 0.2s ease;
    }

    .submit-btn:hover i {
      transform: translateX(3px);
    }


    @media (max-width: 640px) {
      .submit-btn {
        padding: 13px 20px;
        font-size: 14px;
        margin-top: 15px;
        margin-bottom: 12px;
      }
    }

    @media (max-width: 380px) {
      .submit-btn {
        padding: 12px 18px;
        font-size: 13px;
      }
    }


    @media (hover: none) {
      .submit-btn:active {
        opacity: 0.9;
      }

      .submit-btn::before {
        display: none;
      }
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
  }

  @media (max-width: 380px) {
    .modal-box {
      padding: 25px 18px;
    }

    .modal-icon {
      width: 60px;
      height: 60px;
      font-size: 28px;
    }
  }

  @media (min-width: 641px) {
    .form-container {
      padding: 30px 20px;
    }
  }


  @media (hover: none) {

    .form-field input:active,
    .form-field textarea:active {
      background: #f8fafc;
    }

    .submit-btn:active {
      opacity: 0.95;
    }

    input,
    textarea,
    button {
      min-height: 44px;

    }
  }


  @media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.01ms !important;
    }
  }


  @media print {

    .modal-overlay,
    .submit-btn {
      display: none !important;
    }

    .form-container {
      padding: 0;
      max-width: 100%;
    }


  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {


    const now = new Date();
    const tanggalEl = document.getElementById('tanggal');
    const pukulEl = document.getElementById('pukul');

    if (tanggalEl) {
      tanggalEl.value = now.toISOString().slice(0, 10);
    }
    if (pukulEl) {
      pukulEl.value = now.toTimeString().slice(0, 5);
    }

    const form = document.getElementById("keluhanForm");
    const modal = document.getElementById("validationModal");
    const modalMessage = document.getElementById("modalMessage");
    const closeModal = document.getElementById("closeModal");
    let isSubmitting = false;

    function showModal(msg) {
      modalMessage.textContent = msg;
      modal.style.display = "flex";
    }

    function scrollToEl(el) {

      const headerOffset = 80;
      const elementPosition = el.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }

    function clearError() {
      document.querySelectorAll('.error-highlight')
        .forEach(el => el.classList.remove('error-highlight'));
    }

    closeModal.onclick = () => {
      modal.style.display = "none";
    };

    modal.onclick = (e) => {
      if (e.target === modal) modal.style.display = "none";
    };

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        modal.style.display = 'none';
      }
    });

    form.addEventListener("submit", function(e) {
      if (isSubmitting) return true;

      e.preventDefault();
      clearError();

      const alamat = form.querySelector('[name="alamat"]');
      const nohp = form.querySelector('[name="no_hp"]');
      const masukan = form.querySelector('[name="masukan"]');


      if (!alamat.value.trim()) {
        alamat.classList.add('error-highlight');
        scrollToEl(alamat);
        alamat.focus();
        showModal("Alamat wajib diisi.");
        return false;
      }


      if (!nohp.value.trim()) {
        nohp.classList.add('error-highlight');
        scrollToEl(nohp);
        nohp.focus();
        showModal("Nomor HP wajib diisi.");
        return false;
      }

      if (nohp.value.length < 10) {
        nohp.classList.add('error-highlight');
        scrollToEl(nohp);
        nohp.focus();
        showModal("Nomor HP minimal 10 digit.");
        return false;
      }


      if (!masukan.value.trim()) {
        masukan.classList.add('error-highlight');
        scrollToEl(masukan);
        masukan.focus();
        showModal("Uraian keluhan / saran wajib diisi.");
        return false;
      }


      isSubmitting = true;
      form.submit();
      return true;

      const submitBtn = form.querySelector('.submit-btn');
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;

      isSubmitting = true;
      form.submit();
      return true;
    });


    document.querySelectorAll('input, textarea').forEach(field => {
      field.addEventListener('input', function() {
        this.classList.remove('error-highlight');
      });
    });

  });
</script>

<?php include 'layout/footer.php'; ?>