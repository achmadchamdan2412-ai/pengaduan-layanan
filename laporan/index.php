<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';


$totalResponden = $pdo->query("SELECT COUNT(*) FROM survei")->fetchColumn();
$totalKeluhan   = $pdo->query("SELECT COUNT(*) FROM keluhan")->fetchColumn();


$pelayanan_id = $_GET['pelayanan_id'] ?? '';
$date_range   = $_GET['date_range'] ?? '';

$startDate = '';
$endDate   = '';

if ($date_range != '') {
    $dates = explode(" - ", $date_range);
    if (count($dates) == 2) {
        $startDate = trim($dates[0]);
        $endDate   = trim($dates[1]);
    }
}

$namaLayanan = '';
if ($pelayanan_id != '') {
    $stmtPel = $pdo->prepare("SELECT nama FROM pelayanan WHERE id = :id LIMIT 1");
    $stmtPel->execute([':id' => $pelayanan_id]);
    $namaLayanan = $stmtPel->fetchColumn() ?: '';
}


$whereChart1 = [];
$paramsChart1 = [];

if ($startDate != '' && $endDate != '') {
    $whereChart1[] = "DATE(s.created_at) BETWEEN :start AND :end";
    $paramsChart1[':start'] = $startDate;
    $paramsChart1[':end']   = $endDate;
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


$where = [];
$params = [];

if ($pelayanan_id != '') {
    $where[] = "pl.id = :pelayanan_id";
    $params[':pelayanan_id'] = $pelayanan_id;
}

if ($startDate != '' && $endDate != '') {
    $where[] = "DATE(s.created_at) BETWEEN :start AND :end";
    $params[':start'] = $startDate;
    $params[':end']   = $endDate;
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";


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


    <div class="row mb-4">

        <div class="col-md-6">
            <div class="card shadow border-left-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Responden
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                <?= $totalResponden ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Keluhan
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                <?= $totalKeluhan ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-warning text-white shadow">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


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
                value="<?= htmlspecialchars($date_range) ?>" class="form-control">
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- CHART 1 -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                Average Kepuasan Pasien Semua Layanan
                <?php if ($startDate && $endDate): ?>
                    <br>
                    <small class="text-muted">
                        Periode <?= date('d M Y', strtotime($startDate)) ?>
                        - <?= date('d M Y', strtotime($endDate)) ?>
                    </small>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body" style="height:400px">
            <canvas id="chartRataLayanan"></canvas>
        </div>
    </div>


    <div class="card shadow" id="hasilSurvey">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                Detail Kepuasan Per Pertanyaan<?= $namaLayanan ? " untuk Layanan " . ucwords(strtolower(htmlspecialchars($namaLayanan))) : "" ?>
            </h6>
        </div>
        <div class="card-body">

            <div style="height:400px">
                <canvas id="chartPerSoal"></canvas>
            </div>

            <hr>

            <h6>
                <b class="m-0 font-weight-bold text-primary">
                    Hasil Survey Kepuasan<?= $namaLayanan ? " untuk Layanan " . ucwords(strtolower(htmlspecialchars($namaLayanan))) : "" ?>
                </b>
            </h6>

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
            <div class="text-right mt-4">
                <a href="cetak_laporan.php?pelayanan_id=<?= $pelayanan_id ?>&date_range=<?= urlencode($date_range) ?>"
                    target="_blank"
                    class="btn btn-success">
                    Cetak Laporan PDF
                </a>
            </div>
        </div>
    </div>

</div>


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


    new Chart(document.getElementById('chartRataLayanan'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsChart1) ?>,
            datasets: [{
                label: 'Rata-rata Kepuasan',
                data: <?= json_encode($valuesChart1) ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.5)',
                borderColor: 'rgb(255, 159, 64)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4
                }
            }
        }
    });


    new Chart(document.getElementById('chartPerSoal'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Rata-rata Kepuasan',
                data: <?= json_encode($values) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4
                }
            }
        }
    });
</script>

<?php include 'layout/footer.php'; ?>