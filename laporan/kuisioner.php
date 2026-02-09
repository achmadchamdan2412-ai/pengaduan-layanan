<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$sql = "
SELECT
    s.id AS survei_id,
    s.created_at::date AS tanggal,
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
ORDER BY s.created_at DESC
";


$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$layanan = $pdo->query("SELECT nama FROM pelayanan ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$penjamin = $pdo->query("SELECT nama FROM penjamin ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$jk = $pdo->query("SELECT nama FROM jenis_kelamin ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$pendidikan = $pdo->query("SELECT nama FROM pendidikan ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$pekerjaan = $pdo->query("SELECT nama FROM pekerjaan ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);

?>

<?php
include 'layout/header.php';
include 'layout/sidebar.php';
include 'layout/nav.php';
?>

<div id="layoutSidenav_content">
  <main class="container-fluid px-4 mt-4">
    <div class="card shadow mb-4">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Laporan Kepuasan</h5>
      </div>
      <div class="card-body">

        <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
          <div>
            <label class="form-label">Filter Tanggal</label>
            <input type="text" id="dateRange" class="form-control" readonly>
          </div>

          <div class="d-flex gap-2">
            <a class="btn btn-success">
              <i class="fas fa-file-excel"></i> Excel
            </a>
            <a class="btn btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
        <div class="table-responsive">
          <table id="tabelRekap" class="table table-striped">
            <thead class="table-dark sticky-top">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Layanan</th>
                <th>Penjamin</th>
                <th>Jenis Kelamin</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th>
                <th></th>

                <th>
                  <select id="filter-layanan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach ($layanan as $v): ?>
                      <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                  </select>
                </th>

                <th>
                  <select id="filter-penjamin" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach ($penjamin as $v): ?>
                      <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                  </select>
                </th>

                <th>
                  <select id="filter-jk" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach ($jk as $v): ?>
                      <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                  </select>
                </th>

                <th>
                  <select id="filter-pendidikan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach ($pendidikan as $v): ?>
                      <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                  </select>
                </th>

                <th>
                  <select id="filter-pekerjaan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach ($pekerjaan as $v): ?>
                      <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                  </select>
                </th>
              </tr>
            </tfoot>
            <tbody>
              <?php foreach ($rows as $i => $row): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= htmlspecialchars($row['tanggal']) ?></td>
                  <td><?= htmlspecialchars($row['layanan']) ?></td>
                  <td><?= htmlspecialchars($row['penjamin']) ?></td>
                  <td>
                    <?php if ($row['jenis_kelamin'] === 'L'): ?>
                      <span class="badge bg-primary">Laki-laki</span>
                    <?php elseif ($row['jenis_kelamin'] === 'P'): ?>
                      <span class="badge bg-danger">Perempuan</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">-</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['pendidikan']) ?></td>
                  <td><?= htmlspecialchars($row['pekerjaan']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </main>

  <?php include 'layout/footer.php'; ?>
</div>


<script>
  let startDate = null;
  let endDate = null;

  $(function() {

    $.fn.dataTable.ext.search.push(function(settings, data) {
      if (!startDate || !endDate) return true;

      const tanggal = data[1];

      if (!tanggal) return false;

      const rowDate = moment(tanggal, 'YYYY-MM-DD');

      return rowDate.isSameOrAfter(startDate) && rowDate.isSameOrBefore(endDate);
    });

    // Init DataTable
    table = $('#tabelRekap').DataTable({
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      responsive: true,
      fixedHeader: true,
      order: [
        [1, 'desc']
      ], // default sort tanggal terbaru
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
          previous: "‹",
          next: "›"
        },
        emptyTable: "Tidak ada data",
        zeroRecords: "Data tidak ditemukan"
      },
      columnDefs: [{
          targets: 0,
          width: "50px",
          className: "text-center"
        },
        {
          targets: 1,
          width: "120px",
          className: "text-center"
        },
        {
          targets: "_all",
          className: "align-middle"
        }
      ]
    });

    // Default hari ini
    const today = moment().startOf('day');

    $('#dateRange').daterangepicker({
      startDate: today,
      endDate: today,
      autoUpdateInput: true,
      locale: {
        format: 'YYYY-MM-DD',
        separator: ' s/d ',
        applyLabel: 'Terapkan',
        cancelLabel: 'Batal'
      },
      ranges: {
        'Hari Ini': [today, today],
        '7 Hari Terakhir': [moment().subtract(6, 'days'), today],
        '30 Hari Terakhir': [moment().subtract(29, 'days'), today],
        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')]
      }
    }, function(start, end) {
      startDate = start.startOf('day');
      endDate = end.endOf('day');
      table.draw();
    });
    startDate = today;
    endDate = today;
    table.draw();

    $('#filter-layanan').on('change', function() {
      table.column(2).search(this.value).draw();
    });

    $('#filter-penjamin').on('change', function() {
      table.column(3).search(this.value).draw();
    });

    $('#filter-jk').on('change', function() {
      table.column(4).search(this.value).draw();
    });

    $('#filter-pendidikan').on('change', function() {
      table.column(5).search(this.value).draw();
    });

    $('#filter-pekerjaan').on('change', function() {
      table.column(6).search(this.value).draw();
    });

    $('#tabelRekap tbody').on('mouseenter', 'td', function() {
      var colIdx = table.cell(this).index().column;

      $(table.cells().nodes()).removeClass('highlight');
      $(table.column(colIdx).nodes()).addClass('highlight');
    });

  });
</script>