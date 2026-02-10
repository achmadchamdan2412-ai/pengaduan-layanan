<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';


$query = "
SELECT 
    pl.nama AS layanan,
    ROUND(AVG(k.nilai)::numeric, 2) AS rata_rata
FROM kuisioner k
JOIN survei s ON s.id = k.survei_id
JOIN profil pr ON pr.id = s.profil_id
JOIN pelayanan pl ON pl.id = pr.pelayanan_id
GROUP BY pl.nama
ORDER BY pl.nama;
";

$result = $pdo ->query($query);

$labels = [];
$data   = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $labels[] = $row['layanan'];
    $data[]   = $row['rata_rata'];
}
?>
<script>
const ctx = document.getElementById('chartRataLayanan');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Rata-rata Nilai',
            data: <?= json_encode($data) ?>,
            backgroundColor: '#4e73df',
            borderColor: '#4e73df',
            borderWidth: 1,
            maxBarThickness: 40
        }]
    },
    options: {
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
</script>