<?php
require_once 'auth.php';
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
    pk.nama AS pekerjaan,
    ROUND(AVG(k.nilai)::numeric, 2) AS rata_rata_nilai
FROM survei s
JOIN profil p ON p.id = s.profil_id
LEFT JOIN kuisioner k ON k.survei_id = s.id
LEFT JOIN pelayanan pl ON pl.id = p.pelayanan_id
LEFT JOIN penjamin pj ON pj.id = p.penjamin_id
LEFT JOIN jenis_kelamin jk ON jk.id = p.jenis_kelamin_id
LEFT JOIN pendidikan pd ON pd.id = p.pendidikan_id
LEFT JOIN pekerjaan pk ON pk.id = p.pekerjaan_id
GROUP BY
    s.id,
    s.created_at,
    pl.nama,
    pj.nama,
    jk.nama,
    pd.nama,
    pk.nama
ORDER BY s.created_at DESC;
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$layanan = $pdo->query("SELECT nama FROM pelayanan ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$penjamin = $pdo->query("SELECT nama FROM penjamin ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$jk = $pdo->query("SELECT nama FROM jenis_kelamin ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$pendidikan = $pdo->query("SELECT nama FROM pendidikan ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
$pekerjaan = $pdo->query("SELECT nama FROM pekerjaan ORDER BY nama")->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include 'layout/header.php'; ?>

<style>
  .dataTables_wrapper .dataTables_paginate .paginate_button,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
    background: #ffffff !important;
    background-color: #ffffff !important;
    color: #4e73df !important;
    border: 1px solid #d1d3e2 !important;
    box-shadow: none !important;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8f9fc !important;
    background-color: #f8f9fc !important;
    color: #2e59d9 !important;
    border: 1px solid #d1d3e2 !important;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
    background-color: #4e73df !important;
    color: #ffffff !important;
    border-color: #4e73df !important;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
    background: #ffffff !important;
    background-color: #ffffff !important;
    color: #858796 !important;
    cursor: default;
  }

  .dataTables_wrapper .dataTables_paginate ul.pagination li {
    background: transparent !important;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button:focus {
    outline: none !important;
    box-shadow: none !important;
  }

  .btn-detail {
    background: linear-gradient(135deg, #0ea5e9 0%, #14b8a6 100%);
    border: none;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.2s ease;
    white-space: nowrap;
    min-width: 36px;
  }

  .btn-detail:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
    color: white;
  }

  .btn-detail:active {
    transform: translateY(0);
  }

  @media (max-width: 768px) {
    .btn-detail {
      padding: 5px 10px;
      font-size: 11px;
    }
  }
</style>

<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Kuisioner</h1>
  </div>
  <div class="mt-4">
    <div class="card shadow mb-4 mt-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
          Data Laporan Kuisioner
        </h6>
      </div>

      <div class="card-body">
        <div class="row mb-3">
          <div class="col-3">
            <label class="form-label">Filter Tanggal</label>
            <input type="text" id="dateRange" class="form-control" readonly>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-md-2">
            <label>Layanan</label>
            <select id="filter-layanan" class="form-control">
              <option value="">Semua</option>
              <?php foreach ($layanan as $l): ?>
                <option value="<?= htmlspecialchars($l) ?>"><?= htmlspecialchars($l) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label>Penjamin</label>
            <select id="filter-penjamin" class="form-control">
              <option value="">Semua</option>
              <?php foreach ($penjamin as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label>Jenis Kelamin</label>
            <select id="filter-jk" class="form-control">
              <option value="">Semua</option>
              <?php foreach ($jk as $j): ?>
                <option value="<?= htmlspecialchars($j) ?>"><?= htmlspecialchars($j) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label>Pendidikan</label>
            <select id="filter-pendidikan" class="form-control">
              <option value="">Semua</option>
              <?php foreach ($pendidikan as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label>Pekerjaan</label>
            <select id="filter-pekerjaan" class="form-control">
              <option value="">Semua</option>
              <?php foreach ($pekerjaan as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table id="tabelRekap" class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Layanan</th>
                <th>Penjamin</th>
                <th>Jenis Kelamin</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th>Rata-rata Nilai</th>
                <th class="text-center" style="min-width: 100px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td></td>
                  <td><?= htmlspecialchars($row['tanggal']) ?></td>
                  <td><?= htmlspecialchars($row['layanan']) ?></td>
                  <td>
                    <?php if ($row['penjamin'] === 'BPJS'): ?>
                      <span class="badge badge-success">BPJS</span>
                    <?php else: ?>
                      <span class="badge badge-dark">UMUM</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($row['jenis_kelamin'] === 'L'): ?>
                      <span class="badge badge-primary">Laki-laki</span>
                    <?php elseif ($row['jenis_kelamin'] === 'P'): ?>
                      <span class="badge badge-danger">Perempuan</span>
                    <?php else: ?>
                      <span class="badge badge-secondary">-</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['pendidikan']) ?></td>
                  <td><?= htmlspecialchars($row['pekerjaan']) ?></td>
                  <td class="text-center"><?= number_format($row['rata_rata_nilai'], 2) ?></td>
                  <td class="text-center">
                    <a href="kuisioner_detail.php?id=<?= $row['survei_id'] ?>" class="btn-detail">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
  let table;
  let startDate = null;
  let endDate = null;

  $(function() {

    table = $('#tabelRekap').DataTable({
      pageLength: 10,
      responsive: true,
      fixedHeader: true,
      order: [
        [1, 'desc']
      ],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
          previous: "‹",
          next: "›"
        }
      },
      columnDefs: [{
          targets: 0,
          orderable: false,
          searchable: false,
          className: 'text-center'
        },
        {
          targets: [1, 7],
          className: 'text-center'
        },
        {
          targets: 8,
          orderable: false,
          searchable: false,
          className: 'text-center align-middle'
        }
      ],
      dom: 'ltip'
    });


    $.fn.dataTable.ext.search.push(function(settings, data) {
      if (!startDate || !endDate) return true;

      const tanggal = data[1];
      if (!tanggal) return false;

      const rowDate = moment(tanggal.trim(), 'YYYY-MM-DD', true);
      if (!rowDate.isValid()) return false;

      return rowDate.isBetween(startDate, endDate, null, '[]');
    });

    const today = moment().startOf('day');

    $('#dateRange').daterangepicker({
      startDate: today,
      endDate: today,
      locale: {
        format: 'YYYY-MM-DD'
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


    table.on('order.dt search.dt draw.dt', function() {
      table.column(0, {
          search: 'applied',
          order: 'applied'
        })
        .nodes()
        .each(function(cell, i) {
          cell.innerHTML = i + 1;
        });
    });
    table.draw();

  });
</script>

<?php include 'layout/footer.php'; ?>