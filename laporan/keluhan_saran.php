<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$sql = "SELECT * FROM keluhan ORDER BY tanggal DESC, pukul DESC";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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

    .dataTables_wrapper .dataTables_length select {
        background: #fff !important;
        background-color: #fff !important;
        color: #1e293b !important;
        border: 1px solid #d1d3e2 !important;
        border-radius: 6px !important;
        padding: 6px 28px 6px 12px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 8px center !important;
        background-size: 12px !important;
        min-width: 60px !important;
        text-align: center !important;
    }

    .dataTables_wrapper .dataTables_length select:hover {
        border-color: #4e73df !important;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.15) !important;
    }

    .dataTables_wrapper .dataTables_length select:focus {
        outline: none !important;
        border-color: #4e73df !important;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important;
    }

    .dataTables_wrapper .dataTables_length {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b !important;
        font-size: 14px !important;
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
        gap: 5px;
        transition: all 0.2s ease;
        white-space: nowrap;
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

<div id="layoutSidenav_content">
    <main class="container-fluid px-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Laporan Keluhan & Saran</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Data Keluhan & Saran Pasien
                </h6>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter Tanggal</label>
                        <input type="text" id="dateRange" class="form-control" readonly>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelRekap" class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pukul</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th>Masukan</th>
                                <th class="text-center" style="min-width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td></td>
                                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['pukul']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td>
                                        <span style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                            <?= nl2br(htmlspecialchars($row['masukan'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="keluhan_detail.php?id=<?= $row['id'] ?>" class="btn-detail">
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
    </main>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
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
            lengthMenu: [10, 25, 50, 100],
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
                },
                emptyTable: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan"
            },
            columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
                    targets: [1, 2, 4],
                    className: "text-center"
                },
                {
                    targets: 5,
                    width: "35%"
                },
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    className: "text-center align-middle"
                }
            ]
        });


        table.on('order.dt search.dt draw.dt', function() {
            table.column(0, {
                    search: 'applied',
                    order: 'applied'
                })
                .nodes()
                .each((cell, i) => cell.innerHTML = i + 1);
        });


        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (!startDate || !endDate) return true;

            const rowDate = moment(data[1], 'YYYY-MM-DD', true);
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

    });
</script>

<?php include 'layout/footer.php'; ?>