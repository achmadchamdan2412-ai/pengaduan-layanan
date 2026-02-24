<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

/* ===============================
   CARD DATA
================================= */
$totalResponden = $pdo->query("SELECT COUNT(*) FROM survei")->fetchColumn();
$totalKeluhan   = $pdo->query("SELECT COUNT(*) FROM keluhan")->fetchColumn();

/* ===============================
   FILTER
================================= */
$pelayanan_id = $_GET['pelayanan_id'] ?? '';
$date_range   = $_GET['date_range'] ?? '';

$where = [];
$params = [];

if ($pelayanan_id != '') {
    $where[] = "pl.id = :pelayanan_id";
    $params[':pelayanan_id'] = $pelayanan_id;
}

if ($date_range != '') {
    $dates = explode(" - ", $date_range);
    if (count($dates) == 2) {
        $where[] = "DATE(s.created_at) BETWEEN :start AND :end";
        $params[':start'] = $dates[0];
        $params[':end']   = $dates[1];
    }
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

/* ===============================
   AVG GLOBAL (TANPA FILTER)
================================= */
$avgGlobal = $pdo->query("
    SELECT ROUND(AVG(nilai)::numeric,2) 
    FROM kuisioner
")->fetchColumn();

/* ===============================
   DATA PER SOAL (PAKAI FILTER)
================================= */
$sqlSoal = "
SELECT 
    q.id,
    q.deskripsi,
    ROUND(AVG(k.nilai)::numeric,2) as rata
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
JOIN pertanyaan q ON q.id = k.pertanyaan_id
$whereSQL
GROUP BY q.id, q.deskripsi
ORDER BY q.id
";

$stmt = $pdo->prepare($sqlSoal);
$stmt->execute($params);
$dataSoal = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];

foreach ($dataSoal as $d) {
    $labels[] = $d['deskripsi'];
    $values[] = $d['rata'];
}

$listPelayanan = $pdo->query("SELECT id,nama FROM pelayanan ORDER BY nama")
    ->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'layout/header.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card shadow p-3">
                <b>Total Responden</b>
                <h4><?= $totalResponden ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow p-3">
                <b>Total Keluhan</b>
                <h4><?= $totalKeluhan ?></h4>
            </div>
        </div>
    </div>

    <!-- CHART LAMA -->
    <div class="row mt-4">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header">
                    Average Kepuasan Pasien
                </div>
                <div style="height: 450px;" class="card-body">
                    <canvas id="chartRataLayanan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTAINER BARU -->
    <div class="row mt-5">
        <div class="col-md-10 mx-auto">
            <div class="card shadow">

                <div class="card-header">
                    Detail Kepuasan Per Pertanyaan
                </div>

                <div class="card-body">

                    <div class="alert alert-primary text-center">
                        AVG Kepuasan Global : <b><?= $avgGlobal ?></b>
                    </div>

                    <form method="GET" class="row mb-4">

                        <div class="col-md-4">
                            <label>Layanan</label>
                            <select name="pelayanan_id" class="form-control">
                                <option value="">Semua</option>
                                <?php foreach ($listPelayanan as $pl): ?>
                                    <option value="<?= $pl['id'] ?>"
                                        <?= $pelayanan_id == $pl['id'] ? 'selected' : '' ?>>
                                        <?= $pl['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Date Range</label>
                            <input type="text"
                                id="date_range"
                                name="date_range"
                                value="<?= htmlspecialchars($date_range) ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                Filter
                            </button>
                        </div>

                    </form>

                    <canvas id="chartPerSoal"></canvas>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- ================= JS ================= -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {

        $('#date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );
        });

    });

    new Chart(document.getElementById('chartPerSoal'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Rata-rata Kepuasan',
                data: <?= json_encode($values) ?>,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
                }
            }
        }
    });
</script>

<?php include 'layout/footer.php'; ?>
<?php include './js/demo/chart-kepuasan.php'; ?>