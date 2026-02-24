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
   CHART 1 (Average per layanan - GLOBAL)
   Tidak ikut filter pelayanan
================================= */
$whereChart1 = [];
$paramsChart1 = [];

// HANYA filter tanggal
if ($date_range != '') {
    $dates = explode(" - ", $date_range);
    if (count($dates) == 2) {
        $whereChart1[] = "DATE(s.created_at) BETWEEN :start AND :end";
        $paramsChart1[':start'] = $dates[0];
        $paramsChart1[':end']   = $dates[1];
    }
}

$whereSQLChart1 = $whereChart1 ? "WHERE " . implode(" AND ", $whereChart1) : "";

$sqlChart1 = "
SELECT pl.nama, ROUND(AVG(k.nilai)::numeric,2) as rata
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
$whereSQLChart1
GROUP BY pl.nama
ORDER BY pl.nama
";

$stmt1 = $pdo->prepare($sqlChart1);
$stmt1->execute($paramsChart1);
$dataChart1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$labelsChart1 = [];
$valuesChart1 = [];

foreach ($dataChart1 as $row) {
    $labelsChart1[] = $row['nama'];
    $valuesChart1[] = (float)$row['rata'];
}

/* ===============================
   CHART 2 (Per soal)
================================= */
$sqlSoal = "
SELECT q.id, ROUND(AVG(k.nilai)::numeric,2) as rata
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
JOIN pertanyaan q ON q.id = k.pertanyaan_id
$whereSQL
GROUP BY q.id
ORDER BY q.id
";

$stmt2 = $pdo->prepare($sqlSoal);
$stmt2->execute($params);
$dataSoal = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];
$no = 1;

foreach ($dataSoal as $d) {
    $labels[] = "No. " . $no;
    $values[] = $d['rata'];
    $no++;
}

/* ===============================
   KATEGORI KEPUASAN
================================= */
$sqlKategori = "
SELECT k.nilai, COUNT(*) as total
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
$whereSQL
GROUP BY k.nilai
";

$stmt3 = $pdo->prepare($sqlKategori);
$stmt3->execute($params);
$dataKategori = $stmt3->fetchAll(PDO::FETCH_KEY_PAIR);

$totalSemua = array_sum($dataKategori);

function persen($jumlah, $total)
{
    return $total > 0 ? round(($jumlah / $total) * 100, 2) : 0;
}

$sangatPuas = $dataKategori[4] ?? 0;
$puas       = $dataKategori[3] ?? 0;
$kurang     = $dataKategori[2] ?? 0;
$tidak      = $dataKategori[1] ?? 0;

/* ===============================
   TOTAL RESPONDEN FILTER
================================= */
$sqlTotalFilter = "
SELECT COUNT(DISTINCT s.id)
FROM survei s
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
$whereSQL
";

$stmtTotal = $pdo->prepare($sqlTotalFilter);
$stmtTotal->execute($params);
$totalRespondenFilter = $stmtTotal->fetchColumn();

$listPelayanan = $pdo->query("SELECT id,nama FROM pelayanan ORDER BY nama")
    ->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'layout/header.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <!-- CARD -->
    <div class="row mb-4">
        <div class="col-md-6 text-center">
            <div class="card shadow p-3">
                <b>Total Responden</b>
                <h4><?= $totalResponden ?></h4>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="card shadow p-3">
                <b>Total Keluhan</b>
                <h4><?= $totalKeluhan ?></h4>
            </div>
        </div>
    </div>

    <!-- CHART 1 -->
    <div class="card shadow mb-4">
        <div class="card-header">Average Kepuasan Pasien</div>
        <div class="card-body" style="height:400px">
            <canvas id="chartRataLayanan"></canvas>
        </div>
    </div>

    <!-- CHART 2 -->
    <div class="card shadow" id="hasilSurvey">
        <div class="card-header">Detail Kepuasan Per Pertanyaan</div>
        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row mb-4" id="formFilter">
                <div class="col-md-4">
                    <label>Layanan</label>
                    <select name="pelayanan_id" class="form-control">
                        <option value="">Semua</option>
                        <?php foreach ($listPelayanan as $pl): ?>
                            <option value="<?= $pl['id'] ?>" <?= $pelayanan_id == $pl['id'] ? 'selected' : '' ?>>
                                <?= $pl['nama'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Periode</label>
                    <input type="text" id="date_range" name="date_range"
                        value="<?= htmlspecialchars($date_range) ?>"
                        class="form-control">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <div style="height:400px">
                <canvas id="chartPerSoal"></canvas>
            </div>

            <hr>

            <h5><b>Hasil Survey Kepuasan</b></h5>

            <div class="alert alert-secondary py-2">
                Jumlah Responden: <b><?= $totalRespondenFilter ?></b>
            </div>

            <p>Sangat Puas (<?= $sangatPuas ?>) - <?= persen($sangatPuas, $totalSemua) ?>%</p>
            <div class="progress mb-3">
                <div class="progress-bar bg-success" style="width:<?= persen($sangatPuas, $totalSemua) ?>%"></div>
            </div>

            <p>Puas (<?= $puas ?>) - <?= persen($puas, $totalSemua) ?>%</p>
            <div class="progress mb-3">
                <div class="progress-bar bg-info" style="width:<?= persen($puas, $totalSemua) ?>%"></div>
            </div>

            <p>Kurang Puas (<?= $kurang ?>) - <?= persen($kurang, $totalSemua) ?>%</p>
            <div class="progress mb-3">
                <div class="progress-bar bg-warning" style="width:<?= persen($kurang, $totalSemua) ?>%"></div>
            </div>

            <p>Tidak Puas (<?= $tidak ?>) - <?= persen($tidak, $totalSemua) ?>%</p>
            <div class="progress mb-3">
                <div class="progress-bar bg-danger" style="width:<?= persen($tidak, $totalSemua) ?>%"></div>
            </div>

        </div>
    </div>

</div>

<!-- LIBRARY -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function() {
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
                label: 'Rata-rata Nilai',
                data: <?= json_encode($values) ?>,
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1,
                maxBarThickness: 30,
                categoryPercentage: 0.6,
                barPercentage: 0.7
            }]
        },
        options: {
            layout: {
                padding: {
                    top: 20,
                    bottom: 20
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return "Rata-rata: " + context.parsed.y;
                        }
                    }
                }
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("formFilter");

        if (form) {
            form.addEventListener("submit", function() {
                sessionStorage.setItem("afterFilter", "true");
            });
        }

        if (sessionStorage.getItem("afterFilter") === "true") {
            const target = document.getElementById("hasilSurvey");
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: "smooth"
                });
            }
            sessionStorage.removeItem("afterFilter");
        }
    });
</script>

<?php include 'layout/footer.php'; ?>
<?php include './js/demo/chart-kepuasan.php'; ?>