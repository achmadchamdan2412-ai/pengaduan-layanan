<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid");
}

$survei_id = (int) $_GET['id'];


$sqlHeader = "
SELECT
    s.id,
    s.created_at::date AS tanggal,
    s.created_at::time AS waktu,

    pl.nama AS layanan,
    pj.nama AS penjamin,
    jk.nama AS jenis_kelamin,
    pd.nama AS pendidikan,
    pk.nama AS pekerjaan
FROM survei s
JOIN profil p ON p.id = s.profil_id

LEFT JOIN pelayanan pl ON pl.id = p.pelayanan_id
LEFT JOIN penjamin pj ON pj.id = p.penjamin_id
LEFT JOIN jenis_kelamin jk ON jk.id = p.jenis_kelamin_id
LEFT JOIN pendidikan pd ON pd.id = p.pendidikan_id
LEFT JOIN pekerjaan pk ON pk.id = p.pekerjaan_id

WHERE s.id = :id
";

$stmtHeader = $pdo->prepare($sqlHeader);
$stmtHeader->execute(['id' => $survei_id]);
$header = $stmtHeader->fetch(PDO::FETCH_ASSOC);

if (!$header) {
    die("Data tidak ditemukan");
}


$sqlDetail = "
SELECT
    pr.deskripsi AS pertanyaan,
    k.nilai
FROM kuisioner k
JOIN pertanyaan pr ON pr.id = k.pertanyaan_id
WHERE k.survei_id = :id
ORDER BY pr.id ASC
";

$stmtDetail = $pdo->prepare($sqlDetail);
$stmtDetail->execute(['id' => $survei_id]);
$details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);


$sqlAvg = "
SELECT ROUND(AVG(nilai)::numeric,2) AS rata_rata
FROM kuisioner
WHERE survei_id = :id
";

$stmtAvg = $pdo->prepare($sqlAvg);
$stmtAvg->execute(['id' => $survei_id]);
$rata = $stmtAvg->fetch(PDO::FETCH_ASSOC);
?>

<?php include 'layout/header.php'; ?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Detail Kuisioner
        </h1>
        <a href="kuisioner.php" class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Informasi Responden
            </h6>
        </div>
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-3"><strong>Tanggal</strong></div>
                <div class="col-md-9"><?= htmlspecialchars($header['tanggal']) ?> (<?= htmlspecialchars($header['waktu']) ?>)</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3"><strong>Layanan</strong></div>
                <div class="col-md-9"><?= htmlspecialchars($header['layanan']) ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3"><strong>Penjamin</strong></div>
                <div class="col-md-9">
                    <?php if ($header['penjamin'] === 'BPJS'): ?>
                        <span class="badge badge-success">BPJS</span>
                    <?php else: ?>
                        <span class="badge badge-dark">UMUM</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3"><strong>Jenis Kelamin</strong></div>
                <div class="col-md-9">
                    <?php if ($header['jenis_kelamin'] === 'L'): ?>
                        <span class="badge badge-primary">Laki-laki</span>
                    <?php elseif ($header['jenis_kelamin'] === 'P'): ?>
                        <span class="badge badge-danger">Perempuan</span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3"><strong>Pendidikan</strong></div>
                <div class="col-md-9"><?= htmlspecialchars($header['pendidikan']) ?></div>
            </div>

            <div class="row">
                <div class="col-md-3"><strong>Pekerjaan</strong></div>
                <div class="col-md-9"><?= htmlspecialchars($header['pekerjaan']) ?></div>
            </div>

        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Detail Jawaban Kuisioner
            </h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Pertanyaan</th>
                            <th width="15%" class="text-center">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $i => $d): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($d['pertanyaan']) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-info">
                                        <?= $d['nilai'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Rata-rata</th>
                            <th class="text-center">
                                <span class="badge badge-primary">
                                    <?= number_format($rata['rata_rata'], 2) ?>
                                </span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include 'layout/footer.php'; ?>