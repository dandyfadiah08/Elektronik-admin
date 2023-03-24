<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <!-- Main content -->
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Data Potong</div>
            <section class="section">
                <div class="container">
                    <table id="tabelpotongan" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7</th>
                                <th>8</th>
                                <th>9</th>
                                <th>10</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Data Potong</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="kategori">Kategori <small class="text-warning"></small></label>
                                    <input disabled type="text" id="kategori" class="form-control product_name" name="product_name">
                                    <input type="hidden" id="id_potong">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="validque1">Pertanyaan 1 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan1" class="form-control product_name" placeholder="Pertanyaan1" name="product_name">
                                        <input type="number" id="que_1" class="form-control product_name" name="product_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="validque2">Pertanyaan 2 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan2" placeholder="Pertanyaan2" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_2" class="form-control product_name" name="product_name">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <label for="validque3">Pertanyaan 3 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan3" placeholder="Pertanyaan3" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_3" class="form-control product_name" name="product_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="validque4">Pertanyaan 4 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan4" placeholder="Pertanyaan4" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_4" class="form-control product_name" name="product_name">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <label for="validque5">Pertanyaan 5 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan5" placeholder="Pertanyaan5" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_5" class="form-control product_name" name="product_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="validque6">Pertanyaan 6<small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan6" placeholder="Pertanyaan6" id="textPertanyaan6" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_6" id="que_6" class="form-control product_name" name="product_name">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <label for="validque7">Pertanyaan 7 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan7" placeholder="Pertanyaan7" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_7" class="form-control product_name" name="product_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="validque8">Pertanyaan 8 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan8" placeholder="Pertanyaan8" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_8" class="form-control product_name" name="product_name">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <label for="validque9">Pertanyaan 9 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan9" placeholder="Pertanyaan9" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_9" class="form-control product_name" name="product_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="validque10">Pertanyaan 10 <small class="text-warning"></small></label>
                                        <input type="text" id="textPertanyaan10" placeholder="Pertanyaan10" class="form-control product_name" name="product_name">
                                        <input type="number" id="que_10" class="form-control product_name" name="product_name">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" id="edit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal Edit Product-->

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
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3//plugins/toastr/toastr.min.css">
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
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>/assets/adminlte3/plugins/toastr/toastr.min.js"></script>
<script>

</script>
<script>
    const path = '/potongan'
    $(document).ready(function() {
        $('body').on('click', '.editModal', function() {
            var id = $(this).data('id');
            var kategori = $(this).data('kategori');
            var que_1 = $(this).data('que_1');
            var que_2 = $(this).data('que_2');
            var que_3 = $(this).data('que_3');
            var que_4 = $(this).data('que_4');
            var que_5 = $(this).data('que_5');
            var que_6 = $(this).data('que_6');
            var que_7 = $(this).data('que_7');
            var que_8 = $(this).data('que_8');
            var que_9 = $(this).data('que_9');
            var que_10 = $(this).data('que_10');
            var pertanyaan1 = $(this).data('pertanyaan1');
            var pertanyaan2 = $(this).data('pertanyaan2');
            var pertanyaan3 = $(this).data('pertanyaan3');
            var pertanyaan4 = $(this).data('pertanyaan4');
            var pertanyaan5 = $(this).data('pertanyaan5');
            var pertanyaan6 = $(this).data('pertanyaan6');
            var pertanyaan7 = $(this).data('pertanyaan7');
            var pertanyaan8 = $(this).data('pertanyaan8');
            var pertanyaan9 = $(this).data('pertanyaan9');
            var pertanyaan10 = $(this).data('pertanyaan10');
            // console.log(pertanyaan1)
            $('#id_potong').val(id);
            $('#kategori').val(kategori);
            $('#que_1').val(que_1);
            $('#que_2').val(que_2);
            $('#que_3').val(que_3);
            $('#que_4').val(que_4);
            $('#que_5').val(que_5);
            $('#que_6').val(que_6);
            $('#que_7').val(que_7);
            $('#que_8').val(que_8);
            $('#que_9').val(que_9);
            $('#que_10').val(que_10);
            $('#textPertanyaan1').val(pertanyaan1);
            $('#textPertanyaan2').val(pertanyaan2);
            $('#textPertanyaan3').val(pertanyaan3);
            $('#textPertanyaan4').val(pertanyaan4);
            $('#textPertanyaan5').val(pertanyaan5);
            $('#textPertanyaan6').val(pertanyaan6);
            $('#textPertanyaan7').val(pertanyaan7);
            $('#textPertanyaan8').val(pertanyaan8);
            $('#textPertanyaan9').val(pertanyaan9);
            $('#textPertanyaan10').val(pertanyaan10);
            $('#editModal').modal('show');
        });
        let datatable = $("#tabelpotongan").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: ({
                url: '<?= base_url() ?>/potongan/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#filter-date').val();
                    return d;
                },
            }),
            columnDefs: [{
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                className: "text-center",
            }, {
                targets: [0, 12],
                orderable: false
            }],
            order: [
                [1, "desc"]
            ],
            dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
            lengthMenu: [5, 10, 50, 100],
            buttons: ["reload", "export", "colvis", "pageLength"],
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
        $('body').on('click', '.btnLogs', function(e) {
            window.open(`${base_url}/logs/device_check/${$(this).data('id')}`)
        });
        $('#edit').on('click', function() {
            var id = $('#id_potong').val();
            var kategori = $('#kategori').val();
            var que_1 = $('#que_1').val();
            var que_2 = $('#que_2').val();
            var que_3 = $('#que_3').val();
            var que_4 = $('#que_4').val();
            var que_5 = $('#que_5').val();
            var que_6 = $('#que_6').val();
            var que_7 = $('#que_7').val();
            var que_8 = $('#que_8').val();
            var que_9 = $('#que_9').val();
            var que_10 = $('#que_10').val();

            var Pertanyaan1 = $('#textPertanyaan1').val();
            var Pertanyaan2 = $('#textPertanyaan2').val();
            var Pertanyaan3 = $('#textPertanyaan3').val();
            var Pertanyaan4 = $('#textPertanyaan4').val();
            var Pertanyaan5 = $('#textPertanyaan5').val();
            var Pertanyaan6 = $('#textPertanyaan6').val();
            var Pertanyaan7 = $('#textPertanyaan7').val();
            var Pertanyaan8 = $('#textPertanyaan8').val();
            var Pertanyaan9 = $('#textPertanyaan9').val();
            var Pertanyaan10 = $('#textPertanyaan10').val();
            if (kategori == '') {
                $('*[for="kategori"] > small').html('Harap diisi!');
            } else if (que_1 == '' || Pertanyaan1 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('Harap diisi !');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_2 == '' || Pertanyaan2 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('Harap diisi !');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_3 == '' || Pertanyaan3 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('Harap diisi !');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_4 == '' || Pertanyaan4 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('Harap diisi !');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_5 == '' || Pertanyaan5 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('Harap diisi !');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_6 == '' || Pertanyaan6 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('Harap diisi !');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_7 == '' || Pertanyaan7 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('Harap diisi !');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_8 == '' || Pertanyaan8 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('Harap diisi !');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('');
            } else if (que_9 == '' || Pertanyaan9 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('Harap diisi !');
                $('*[for="validque10"] > small').html('');
            } else if (que_10 == '' || Pertanyaan10 == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="validque1"] > small').html('');
                $('*[for="validque2"] > small').html('');
                $('*[for="validque3"] > small').html('');
                $('*[for="validque4"] > small').html('');
                $('*[for="validque5"] > small').html('');
                $('*[for="validque6"] > small').html('');
                $('*[for="validque7"] > small').html('');
                $('*[for="validque8"] > small').html('');
                $('*[for="validque9"] > small').html('');
                $('*[for="validque10"] > small').html('Harap diisi !');
            } else {
                Swal.fire({
                    title: 'Apakah Anda ingin menyimpan perubahan Data ini?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${base_url}${path}/editPotongan`,
                            type: "post",
                            dataType: "json",
                            data: {
                                id: id,
                                kategori: kategori,
                                que_1: que_1,
                                que_2: que_2,
                                que_3: que_3,
                                que_4: que_4,
                                que_5: que_5,
                                que_6: que_6,
                                que_7: que_7,
                                que_8: que_8,
                                que_9: que_9,
                                que_10: que_10,
                                Pertanyaan1: Pertanyaan1,
                                Pertanyaan2: Pertanyaan2,
                                Pertanyaan3: Pertanyaan3,
                                Pertanyaan4: Pertanyaan4,
                                Pertanyaan5: Pertanyaan5,
                                Pertanyaan6: Pertanyaan6,
                                Pertanyaan7: Pertanyaan7,
                                Pertanyaan8: Pertanyaan8,
                                Pertanyaan9: Pertanyaan9,
                                Pertanyaan10: Pertanyaan10,
                            },

                            success: function(data) {
                                if (data.success == true) {
                                    $('#editModal').modal('hide');
                                    toastr.success('Update Data Potong Succesfully')
                                    datatable.ajax.reload();
                                } else {
                                    alert("SUCCESS: " + data.status);

                                }
                                // console.log(data);

                            }
                        })

                    }
                })


            }
        });
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