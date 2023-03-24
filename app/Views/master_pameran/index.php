<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <!-- Main content -->
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Master Pameran</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modaltambahpemaran" id="tombolTambah"><i class="fa fa-plus"></i>Tambah Pameran
                            </button>
                        </div>
                    </div>
                    <table id="tabeladmin" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Pameran</th>
                                <th>Mitra</th>
                                <th>Subsidi</th>
                                <th>Bulan</th>
                                <th>Aksi</th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModalPameran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">EDIT MASTER PAMERAN</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editnama_pameran">Nama Pameran <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editnama_pameran" placeholder="Nama Pameran" autocomplete="off" name="username" value="">
                                    <input type="hidden" id="editid_pameran" name="id_pameran" />
                                </div>
                                <div class="mb-3">
                                    <label for="editjenis_subsidi">Jenis Subsidi <small class="text-warning"></small></label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editjenis_subsidi" id="editjenis_subsidi0" value="0" checked>
                                                <label class="form-check-label" for="jenis_subsidi1">
                                                    Subsidi Pameran
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editjenis_subsidi" id="editjenis_subsidi1" value="1">
                                                <label class="form-check-label" for="editjenis_subsidi2">
                                                    Subsidi Unit
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="mb-3">
                                    <label for="editsubsidi">Subsidi Pameran <small class="text-warning"></small></label>
                                    <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="editinputSubsidiPameran" placeholder="Subsidi Pameran" autocomplete="off" name="editsubsidi" value="">

                                </div>
                                <div class="mb-3">
                                    <label for="editvoucher">Masa Berlaku Vocher (Hari) <small class="text-warning"></small></label>
                                    <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="editinputwaktuvoucher" placeholder="Masa Expired Vocher(Kosong bila tidak Menggunakan Vocher)" autocomplete="off" name="subsidi" value="">

                                </div>
                                <div class="form-group">

                                    <label for="editmitra">Mitra Utama <small class="text-warning"></small></label>
                                    <select class="duallistbox" multiple="multiple" id="inputEditIdMU">
                                        <?php foreach ($page->data as $key => $value) : ?>
                                            <option value="<?= $value['id_mitra'] ?>"><?= $value['nama_mitra'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editbulan">bulan <small class="text-warning"></small></label>
                                    <input type="date" class="form-control" style="background-color: aliceblue; color:#321" id="editbulan" placeholder="bulan" autocomplete="off" name="bulan" value="">
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
                <div class="modal fade" id="modaltambahpemaran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">TAMBAH MASTER PAMERAN</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama_pameran">Nama Pameran <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="nama_pameran" placeholder="Nama Pameran" autocomplete="off" name="username" value="">
                                    <input type="hidden" id="id_pameran" name="id_pameran" />
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_subsidi">Jenis Subsidi <small class="text-warning"></small></label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jenis_subsidi" id="jenis_subsidi0" value="0" checked>
                                                <label class="form-check-label" for="jenis_subsidi1">
                                                    Subsidi Pameran
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jenis_subsidi" id="jenis_subsidi1" value="1">
                                                <label class="form-check-label" for="jenis_subsidi2">
                                                    Subsidi Unit
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="mb-3">
                                    <label for="subsidi">Subsidi Pameran <small class="text-warning"></small></label>
                                    <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="inputSubsidiPameran" placeholder="Subsidi Pameran" autocomplete="off" name="subsidi" value="">

                                </div>
                                <div class="mb-3">
                                    <label for="voucher">Masa Berlaku Vocher (Hari) <small class="text-warning"></small></label>
                                    <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="inputwaktuvoucher" placeholder="Masa Expired Vocher(Kosong bila tidak Menggunakan Vocher)" autocomplete="off" name="subsidi" value="">

                                </div>
                                <div class="form-group">
                                    <label for="mitra">Mitra Utama <small class="text-warning"></small></label>
                                    <select class="duallistbox" multiple="multiple" id="inputTambahIdMU">
                                        <?php foreach ($page->data as $key => $value) : ?>
                                            <option value="<?= $value['id_mitra'] ?>"><?= $value['nama_mitra'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="bulan">bulan <small class="text-warning"></small></label>
                                    <input type="date" class="form-control" style="background-color: aliceblue; color:#321" id="bulan" placeholder="bulan" autocomplete="off" name="bulan" value="">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahPameran" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah Data Pameran</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?= $this->section('content_css') ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- Toastr -->

    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/toastr/toastr.min.css">
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
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
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
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="<?= base_url() ?>/assets/adminlte3/plugins/toastr/toastr.min.js"></script>
    <script>
        const path = '/MasterAdmin'
        $(document).ready(function() {
            $('body').on('click', '.editModalPameran', function() {
                var id_pameran = $(this).data('id_pameran');
                var nama_pameran = $(this).data('nama_pameran');
                var subsidi = $(this).data('subsidi');
                var vocher = $(this).data('vocher');
                var bulan = $(this).data('bulan');
                var jenis_subsidi = $(this).data('jenis_subsidi');
                $('#editid_pameran').val(id_pameran);
                $('#editnama_pameran').val(nama_pameran);
                $('#editinputSubsidiPameran').val(subsidi);
                $('#editinputwaktuvoucher').val(vocher);
                $('#editbulan').val(bulan);
                if (jenis_subsidi == "0") $('#editjenis_subsidi0').prop('checked', true);
                if (jenis_subsidi == "1") $('#editjenis_subsidi1').prop('checked', true);
                $('#editModalPameran').modal('show');

            });
            $('.select2').select2()
            $('.duallistbox').bootstrapDualListbox()
            let datatable = $("#tabeladmin").DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: '<?= base_url() ?>/MasterPameran/load_data',
                    type: "post",
                    data: function(d) {
                        d.status = $('#filter-status option:selected').val();
                        d.merchant = $('#filter-merchant option:selected').val();
                        d.date = $('#filter-date').val();
                        return d;
                    },
                },
                columnDefs: [{
                    targets: [0, 1, 2, 3, 4, 5],
                }, {
                    targets: [0, 5],
                    orderable: false
                }],
                order: [
                    [1, "desc"]
                ],
                dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
                lengthMenu: [5, 10, 50, 100],
                buttons: ["reload", "export", "colvis", "pageLength"],
            });
            $('body').on('click', '.tombolHapus', function() {
                var id_pameran = $(this).data('id_pameran');
                var nama_pameran = $(this).data('nama_pameran');
                console.log(id_pameran, nama_pameran)
                Swal.fire({
                    title: 'Apakah Anda ingin Hapus  Data ini?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url() ?>/MasterPameran/HapusPameran',
                            type: "post",
                            dataType: "json",
                            data: {
                                id_pameran: id_pameran,
                                nama_pameran: nama_pameran,

                            },

                            success: function(data) {
                                if (data.success == true) {
                                    toastr.success('Delete Data  Succesfully')
                                    datatable.ajax.reload();
                                } else {
                                    alert("SUCCESS: " + data.status);

                                }
                                // console.log(data);

                            }
                        })

                    }
                })

            });

            $('#edit').on('click', function() {
                var nama_pameran = $('#editnama_pameran').val();
                var jenis_subsidi = $("input[name='editjenis_subsidi']:checked").val();
                var subsidi = $('#editinputSubsidiPameran').val();
                var voucher = $('#editinputwaktuvoucher').val();
                var mitra = $('#inputEditIdMU').val();
                var id_pameran = $('#editid_pameran').val();
                var bulan = $('#editbulan').val();
                if (nama_pameran == '') {
                    $('*[for="editnama_pameran"] > small').html('Harap diisi!');
                    $('*[for="editsubsidi"] > small').html('');
                    $('*[for="editmitra"] > small').html('');
                    $('*[for="editvoucher"] > small').html('');
                    $('*[for="editbulan"] > small').html('');
                } else if (subsidi == '') {
                    $('*[for="editnama_pameran"] > small').html('');
                    $('*[for="editsubsidi"] > small').html('Harap diisi!');
                    $('*[for="editmitra"] > small').html('');
                    $('*[for="editvoucher"] > small').html('');
                    $('*[for="editbulan"] > small').html('');
                } else if (mitra == '') {
                    $('*[for="editnama_pameran"] > small').html('');
                    $('*[for="editsubsidi"] > small').html('');
                    $('*[for="editmitra"] > small').html('Harap pilih minimal satu mitra!');
                    $('*[for="editvoucher"] > small').html('');
                    $('*[for="editbulan"] > small').html('');
                } else if (bulan == '') {
                    $('*[for="editnama_pameran"] > small').html('');
                    $('*[for="editsubsidi"] > small').html('');
                    $('*[for="editmitra"] > small').html('');
                    $('*[for="editvoucher"] > small').html('');
                    $('*[for="editbulan"] > small').html('Harap diisi!');
                } else {
                    $('*[for="editnama_pameran"] > small').html('');
                    $('*[for="editsubsidi"] > small').html('');
                    $('*[for="editmitra"] > small').html('');
                    $('*[for="editvoucher"] > small').html('');
                    $('*[for="editbulan"] > small').html('');
                    Swal.fire({
                        title: 'Apakah Anda ingin menyimpn Perubahan  Data ini?',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        denyButtonText: `Don't save`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '<?= base_url() ?>/MasterPameran/edit_data',
                                dataType: 'json',
                                method: 'POST',
                                data: {
                                    aksi: 'editPameran',
                                    data: {
                                        id_pameran: id_pameran,
                                        nama_pameran: nama_pameran,
                                        jenis_subsidi: jenis_subsidi,
                                        subsidi: subsidi,
                                        voucher: voucher,
                                        mitra: mitra,
                                        bulan: bulan,
                                    }
                                },
                                success: function(data) {
                                    if (data.success == true) {
                                        $('#editModalPameran').modal('hide');
                                        toastr.success('Update Data Pameran Succesfully')
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
            datatable.buttons().container()
                .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
            // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))
            var toggle = false;
            $('body').on('click', '#editshowPassword', function() {
                toggle = !toggle;
                // console.log(toggle);
                if (toggle) {
                    $(this)
                        .removeClass('fa-eye-slash')
                        .addClass('fa-eye');
                    $('#editpassword').attr('type', 'text');
                } else {
                    $(this)
                        .removeClass('fa-eye')
                        .addClass('fa-eye-slash');
                    $('#editpassword').attr('type', 'password');
                }
            });
            $('body').on('click', '#showPassword', function() {
                toggle = !toggle;
                // console.log(toggle);
                if (toggle) {
                    $(this)
                        .removeClass('fa-eye-slash')
                        .addClass('fa-eye');
                    $('#password').attr('type', 'text');
                } else {
                    $(this)
                        .removeClass('fa-eye')
                        .addClass('fa-eye-slash');
                    $('#password').attr('type', 'password');
                }
            });
            $('#username').val('');
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
            $('#tambahPameran').on('click', function() {
                var nama_pameran = $('#nama_pameran').val();
                var jenis_subsidi = $("input[name='jenis_subsidi']:checked").val();
                var subsidi = $('#inputSubsidiPameran').val();
                var voucher = $('#inputwaktuvoucher').val();
                var mitra = $('#inputTambahIdMU').val();
                var id_pameran = $('#id_pameran').val();
                var bulan = $('#bulan').val();
                if (nama_pameran == '') {
                    $('*[for="nama_pameran"] > small').html('Harap diisi!');
                    $('*[for="subsidi"] > small').html('');
                    $('*[for="mitra"] > small').html('');
                    $('*[for="voucher"] > small').html('');
                    $('*[for="bulan"] > small').html('');
                } else if (subsidi == '') {
                    $('*[for="nama_pameran"] > small').html('');
                    $('*[for="subsidi"] > small').html('Harap diisi!');
                    $('*[for="mitra"] > small').html('');
                    $('*[for="voucher"] > small').html('');
                    $('*[for="bulan"] > small').html('');
                } else if (mitra == '') {
                    $('*[for="nama_pameran"] > small').html('');
                    $('*[for="subsidi"] > small').html('');
                    $('*[for="mitra"] > small').html('Harap pilih minimal satu mitra!');
                    $('*[for="voucher"] > small').html('');
                    $('*[for="bulan"] > small').html('');
                } else if (bulan == '') {
                    $('*[for="nama_pameran"] > small').html('');
                    $('*[for="subsidi"] > small').html('');
                    $('*[for="mitra"] > small').html('');
                    $('*[for="voucher"] > small').html('');
                    $('*[for="bulan"] > small').html('Harap di isi!');
                } else {
                    $('*[for="nama_pameran"] > small').html('');
                    $('*[for="subsidi"] > small').html('');
                    $('*[for="mitra"] > small').html('');
                    $('*[for="voucher"] > small').html('');
                    $('*[for="bulan"] > small').html('');
                    Swal.fire({
                        title: 'Apakah Anda ingin menyimpan  Data ini?',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        denyButtonText: `Don't save`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '<?= base_url() ?>/MasterPameran/tambah_data',
                                dataType: 'json',
                                method: 'POST',
                                data: {
                                    aksi: 'tambahPameran',
                                    data: {
                                        id_pameran: id_pameran,
                                        nama_pameran: nama_pameran,
                                        jenis_subsidi: jenis_subsidi,
                                        subsidi: subsidi,
                                        voucher: voucher,
                                        mitra: mitra,
                                        bulan: bulan,
                                    }
                                },
                                success: function(data) {
                                    if (data.success == true) {
                                        $('#modaltambahpemaran').modal('hide');
                                        toastr.success('Create Data Pameran Succesfully')
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
        });
    </script>
    <?= $this->endSection('content_js') ?>