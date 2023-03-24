<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <!-- Main content -->
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Data Logs</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-select form-control" id="filter-status" aria-label="Default select example">
                                <option value="semua">Semua</option>
                                <?php foreach ($page->data as $key => $value) : ?>
                                    <option value="<?= $value['id_log_kategori'] ?>"><?= $value['kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <input type="text" id="filter-date" name="daterange" class="form-control" value="<?= $page->start_date ?> - <?= $page->end_date ?>" />
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="tombolreset" class="btn btn-warning btn-sm mb-1 mb-3" style="width:100px;height:40px">
                                <i class="fas fa-sync"></i> Reset
                            </button>
                        </div>
                    </div>
                    <table id="tabellog" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>TANGGAL</th>
                                <th>USER</th>
                                <th>KET</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
<?= $this->section('content_css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.css">
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

    #tabeluser {
        font-size: 12px;
    }
</style>
<?= $this->endSection('content_css') ?>
<?= $this->endSection('content') ?>
<?= $this->section('content_js') ?>
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
        $('input[name="daterange"]').daterangepicker();
        let datatable = $("#tabellog").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/log/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.date = $('#filter-date').val();
                    return d;
                },
            },
            // columnDefs: [{
            //     targets: [0, 1, 2, 3],
            //     className: "text-center",
            // }
            columnDefs: [{
                    "targets": 0,
                    "className": "dt-body-center dt-head-center",
                    "orderable": false,
                    "width": "50px"
                },
                {
                    "targets": 1,
                    "className": "dt-body-center dt-head-center"
                },
                {
                    "targets": 2,
                    "className": "dt-body-center dt-head-center"
                },
                {
                    "targets": 3,
                    "className": "dt-head-center"
                },
            ],
            order: [
                [1, "desc"]
            ],
            dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
            lengthMenu: [5, 10, 50, 100],
            buttons: ["reload", "export", "colvis", "pageLength"],
        });
        $('#tombolreset').on('click', function() {
            $('#filter-date').val('');
            datatable.ajax.reload();
        })
        $('#filter-status').on('change', function() {
            var date = $('#filter-date').val();
            var status = $('#filter-status option:selected').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        });
        $('#filter-date').on('change', function() {

            var date = $('#filter-date').val();
            var status = $('#filter-status option:selected').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        });

    });
</script>
<?= $this->endSection('content_js') ?>