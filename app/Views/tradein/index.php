<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="background-color: white; color:black">
    <!-- Main content -->
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Data Tradein</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-select form-control" id="status" aria-label="Default select example">
                                <option value="All">Semua</option>
                                <option value="0">Gagal</option>
                                <option value="1">Berhasil</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" id="exportDataTradein" class="btn btn-warning btn-sm" style="width:100px;height:40px">
                                <i class="fas fa-save"></i> Export
                            </button>
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
                    <table id="tabeluser" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Transaksi</th>
                                <th>Status Transaksi</th>
                                <th>Mitra dan Toko</th>
                                <th>kode Tradein dan Serial Number</th>
                                <th>Kategori</th>
                                <th>Harga Device</th>
                                <th>Subsidi</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?= $this->section('content_css') ?>
<!-- DataTables -->
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
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

</script>
<script>
    const path = '/tradein'
    $(document).ready(function() {
        let datatable = $("#tabeluser").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/tradein/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#datepicker').val();
                    d.status = $('#status').val();
                    return d;
                },
            },
            columnDefs: [{
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                className: "text-center",
            }, {
                targets: [0, 9],
                orderable: false
            }],
            order: [
                [1, "desc"]
            ],
            dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
            lengthMenu: [5, 10, 50, 100],
            buttons: ["export", "colvis", "pageLength"],
        });
        datatable.buttons().container()
            .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
        // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

        $('.myfilter').change(function() {
            datatable.ajax.reload();
        })
        $('#selectFilter').on('change', function() {
            datatable.ajax.reload();

            var status = $('#export option:selected').val();
            $('#btnExport').attr('href', 'finance-export.php?status=' + status);
        });
        $('#exportDataTradein').on('click', function() {
            var date = $('#datepicker').val();
            var status = $('#status option:selected').val();
            if (date == '') {
                window.location.href = '<?= base_url() ?>/Tradein/exportDataTradein/' + status
                return false
            } else {
                var SplitDate = date.split("-")
                var dateStart = SplitDate[0]
                var SplitDateStart = dateStart.split("/")
                var yearStart = SplitDateStart[2].split(" ")
                var DateStartfixed = yearStart[0] + '-' + SplitDateStart[0] + '-' + SplitDateStart[1]
                var dateend = SplitDate[1]
                var SplitDateEnd = dateend.split("/")
                var mount = SplitDateEnd[0].split(" ")
                var DateEndfixed = SplitDateEnd[2] + '-' + mount[1] + '-' + SplitDateEnd[1]
                var DateStatus = DateStartfixed + ' ' + DateEndfixed + '/' + status
                window.location.href = '<?= base_url() ?>/Tradein/exportDataTradeinDate/' + DateStatus
                return false
            }

        });
        $('body').on('click', '.btnLogs', function(e) {
            window.open(`${base_url}/logs/device_check/${$(this).data('id')}`)
        });
        $('#datepicker').on('change', function() {

            var date = $('#datepicker').val();
            datatable.ajax.reload();
        })
        $('#status').on('change', function() {
            var date = $('#datepicker').val();
            var status = $('#status option:selected').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        });
        $('#resetDate').on('click', function() {
            $('#datepicker').val('');
            datatable.ajax.reload();

        })
        $('input[name="daterange"]').daterangepicker();
        $('#btnExport').on('click', function() {
            var url = 'export-kuesioner.php?';
            url += '&status=' + $('#selectFilter > option:selected').val();
            url += '&selectDate=' + $('#datepicker > option:selected').val();
            url += '&date=' + $('#datepicker').val();
            console.log(url);
            window.location = url;
            return false;
        });
    });

    function exportData() {
        const cetakData = $('#export').val();
        $.ajax({
            url: `${base_url}${path}/exporttradein`,
            type: "post",
            dataType: "json",
            data: {
                cetakData: cetakData,
            }
        })
    }
</script>
<?= $this->endSection('content_js') ?>