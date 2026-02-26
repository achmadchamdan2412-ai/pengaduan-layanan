<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


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

$where = [];
$params = [];

if ($pelayanan_id != '') {
    $where[] = "pl.id = :pelayanan_id";
    $params[':pelayanan_id'] = $pelayanan_id;
}

if ($startDate && $endDate) {
    $where[] = "DATE(s.created_at) BETWEEN :start AND :end";
    $params[':start'] = $startDate;
    $params[':end']   = $endDate;
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$namaLayanan = "Semua Layanan";
if ($pelayanan_id) {
    $stmt = $pdo->prepare("SELECT nama FROM pelayanan WHERE id=:id");
    $stmt->execute([':id' => $pelayanan_id]);
    $namaLayanan = $stmt->fetchColumn();
    $namaLayanan = ucwords(strtolower($namaLayanan));
}


$sql1 = "
SELECT pl.nama, ROUND(AVG(k.nilai)::numeric,2) as rata
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
$whereSQL
GROUP BY pl.nama
ORDER BY pl.nama
";

$stmt1 = $pdo->prepare($sql1);
$stmt1->execute($params);
$dataChart1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);


$sql2 = "
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

$stmt2 = $pdo->prepare($sql2);
$stmt2->execute($params);
$dataChart2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);


$sql3 = "
SELECT k.nilai, COUNT(*) as total
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
$whereSQL
GROUP BY k.nilai
";

$stmt3 = $pdo->prepare($sql3);
$stmt3->execute($params);
$dataKategori = $stmt3->fetchAll(PDO::FETCH_KEY_PAIR);

$total = array_sum($dataKategori);

function persen($j, $t)
{
    return $t > 0 ? round(($j / $t) * 100, 2) : 0;
}


$html = "
<style>
body { font-family: DejaVu Sans, sans-serif; font-size:12px; }
h2 { text-align:center; margin-bottom:5px; }
hr { margin-bottom:20px; }
table { border-collapse: collapse; width:100%; margin-bottom:20px; }
th { background:#2e59d9; color:white; padding:8px; }
td { border:1px solid #ccc; padding:8px; }
.section-title { 
    background:#f8f9fc; 
    padding:8px; 
    font-weight:bold; 
    margin-top:20px;
}
.footer { margin-top:30px; font-size:10px; text-align:right; }
</style>

<h2>LAPORAN HASIL SURVEY KEPUASAN</h2>
<hr>

<b>Periode:</b> {$date_range} <br>
<b>Layanan:</b> {$namaLayanan}

<div class='section-title'>Ringkasan Kepuasan</div>

<table>
<tr>
<th>Kategori</th>
<th>Jumlah</th>
<th>Persentase</th>
</tr>";

$kategoriLabel = [
    4 => "Sangat Puas",
    3 => "Puas",
    2 => "Kurang Puas",
    1 => "Tidak Puas"
];

foreach ($kategoriLabel as $nilai => $label) {
    $jumlah = $dataKategori[$nilai] ?? 0;
    $html .= "
    <tr>
        <td>{$label}</td>
        <td>{$jumlah}</td>
        <td>" . persen($jumlah, $total) . " %</td>
    </tr>";
}

$html .= "</table>";


$html .= "<div class='section-title'>Rata-rata Kepuasan Per Layanan</div>
<table>
<tr>
<th>Layanan</th>
<th>Rata-rata Nilai</th>
</tr>";

foreach ($dataChart1 as $row) {
    $nama = ucwords(strtolower($row['nama']));
    $html .= "
    <tr>
        <td>{$nama}</td>
        <td>{$row['rata']}</td>
    </tr>";
}

$html .= "</table>";


$html .= "<div class='section-title'>Rata-rata Kepuasan Per Pertanyaan</div>
<table>
<tr>
<th>No Pertanyaan</th>
<th>Rata-rata Nilai</th>
</tr>";

$no = 1;
foreach ($dataChart2 as $row) {
    $html .= "
    <tr>
        <td>No. {$no}</td>
        <td>{$row['rata']}</td>
    </tr>";
    $no++;
}

$html .= "</table>";

$html .= "
<div class='footer'>
Dicetak pada: " . date('d-m-Y H:i') . "
</div>";


$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan-Survey.pdf", ["Attachment" => false]);
exit;
