<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <!-- Main content -->
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">STATISTIK TRADEIN ELEKTRONIK</div>
            <section class="section">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <select class="form-select form-control" id="filterMitra" aria-label="Default select example">
                            <option value="ALL">Filter Mitra</option>
                            <?php foreach ($page->datamitra as $key => $value) : ?>
                                <option value="<?= $value['id_mitra'] ?>"><?= $value['nama_mitra'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <input type="text" id="datepicker" name="daterange" class="form-control" value="<?= $page->start_date ?> - <?= $page->end_date ?>" />
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="resetDate" class="btn btn-warning btn-sm mb-1 mb-3" style="width:100px;height:40px">
                            <i class="fas fa-sync"></i> Reset
                        </button>
                    </div>
                </div>
                <table id="tabelstatik" class="table table-bordered table-striped table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Berhasi/Gagal</th>
                            <th class="text-center">Presentasi Tradein Berhasil</th>
                            <th class="text-center">Jumlah Nominal Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!-- <div class="PrintArea" class="table-responsive table-hover px-4" style="width: 100%; margin-left: -10px!important;">
                </div> -->
                <div class="row mb-6">
                    <div class="col-md-6">
                        <button type="button" id="print" class="btn btn-success btn-sm mb-1 mb-3" style="width:190px;height:40px">
                            <i class="fas fa-print"></i> Print Table
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?= $this->section('content_css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/css/PrintArea.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<style>
    .content-wrapper {
        background: rgb(179, 179, 179);
        background: linear-gradient(0deg, rgba(179, 179, 179, 1) 0%, rgba(255, 255, 255, 1) 77%);
    }

    .title-text {
        font-size: 2rem;
        color: black;
        text-align: center;
        margin-top: 0.25rem;
        border: 1px solid #321;
    }

    .dataTables_filter {
        display: none;
    }

    #tabelstatik {
        font-size: 12px;
    }
</style>
<?= $this->endSection('content_css') ?>
<?= $this->endSection('content') ?>
<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/js/jquery.PrintArea.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
<script>
    const path = '/statistik'
    $(document).ready(function() {
        let datatable = $("#tabelstatik").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/statistik/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#datepicker').val();
                    d.status = $('#filterMitra').val();
                    return d;
                },
            },
            columnDefs: [{
                targets: [0, 1, 2, 3],
                className: "text-center",
            }, {
                targets: [0, 3],
                orderable: false
            }],
            order: [
                [1, "desc"]
            ],
            dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
            lengthMenu: [5, 10, 50, 100],
            buttons: ["export", "pdf", "colvis", "pageLength"],
        });
        $('input[name="daterange"]').daterangepicker();
        $('#resetDate').on('click', function() {
            $('#datepicker').val('');
            datatable.ajax.reload();

        })
        $('#print').on('click', function() {
            var date = $('#datepicker').val();
            var fixeddate = ''
            var status = $('#filterMitra').val();
            if (date != '') {
                var arrayDate = date.split(" ")
                var stratDate = arrayDate[0].split("/")
                var startform = stratDate[0] + '-' + stratDate[1] + '-' + stratDate[2]
                var endDate = arrayDate[2].split("/")
                var endform = endDate[0] + '-' + endDate[1] + '-' + endDate[2]
                var fixeddate = startform + ' ' + endform
                window.open(`${base_url}/statistik/printData/${status}/${fixeddate}`)
            } else {
                window.open(`${base_url}/statistik/printDatanotdate/${status}`)
            }
        })
        $('#datepicker').on('change', function() {

            var date = $('#datepicker').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        })
        $('#filterMitra').on('change', function() {
            var date = $('#datepicker').val();
            var status = $('#filterMitra option:selected').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        });
        $('table.table').on('change', function() {

            var date = $('#datepicker').val();
            var status = $('#filterMitra option:selected').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        });

    });
</script>
<?= $this->endSection('content_js') ?>