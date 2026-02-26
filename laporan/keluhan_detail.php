l<?php
    require_once 'auth.php';
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../config/db.php';

    $id = $_GET['id'] ?? null;

    if (!$id) {
        header("Location: keluhan_saran.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM keluhan WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header("Location: keluhan_saran.php");
        exit;
    }

    $sql = "SELECT id FROM keluhan ORDER BY tanggal DESC, pukul DESC";
    $allRows = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    $rowNumber = array_search($id, $allRows) + 1;
    ?>

<?php include 'layout/header.php'; ?>

<style>
    .detail-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .detail-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #14b8a6 100%);
        padding: 20px 25px;
        color: white;
    }

    .detail-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-body {
        padding: 25px;
    }

    .detail-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        flex: 0 0 140px;
        font-weight: 600;
        color: #64748b;
        font-size: 14px;
    }

    .detail-value {
        flex: 1;
        color: #1e293b;
        font-size: 15px;
        line-height: 1.6;
    }

    .detail-value.masukan {
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #0ea5e9;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .badge-time {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-back-bottom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0ea5e9 0%, #14b8a6 100%);
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }

    .btn-back-bottom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
        color: white;
    }

    .btn-back-bottom:active {
        transform: translateY(0);
    }

    .back-container {
        text-align: center;
        margin-top: 24px;
        padding-bottom: 40px;
    }

    @media (max-width: 640px) {
        .detail-row {
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            flex: none;
            font-size: 13px;
        }

        .detail-header {
            text-align: center;
        }

        .detail-header h5 {
            justify-content: center;
        }
    }
</style>

<div id="layoutSidenav_content">
    <main class="container-fluid px-4">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Detail Keluhan</h1>
        </div>

        <div class="detail-card">
            <div class="detail-header">
                <h5>
                    <i class="fas fa-comment-alt"></i>
                    Keluhan #<?= $rowNumber ?>
                </h5>
            </div>

            <div class="detail-body">
                <div class="detail-row">
                    <div class="detail-label">Tanggal</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(date('d F Y', strtotime($row['tanggal']))) ?>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Pukul</div>
                    <div class="detail-value">
                        <span class="badge-time">
                            <i class="far fa-clock"></i>
                            <?= htmlspecialchars($row['pukul']) ?> WIB
                        </span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-value">
                        <?= nl2br(htmlspecialchars($row['alamat'])) ?>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Nomor HP</div>
                    <div class="detail-value">
                        <a href="https://wa.me/62<?= ltrim($row['no_hp'], '0') ?>" target="_blank" class="text-decoration-none">
                            <i class="fab fa-whatsapp text-success"></i>
                            <?= htmlspecialchars($row['no_hp']) ?>
                        </a>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Masukan</div>
                    <div class="detail-value masukan">
                        <?= nl2br(htmlspecialchars($row['masukan'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="back-container">
            <a href="keluhan_saran.php" class="btn-back-bottom">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Daftar Keluhan
            </a>
        </div>

    </main>
</div>

<?php include 'layout/footer.php'; ?>