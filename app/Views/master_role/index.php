<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Master Role</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modalAdmin" id="tombolTambah"><i class="fa fa-plus"></i>Tambah Role
                            </button>
                        </div>
                    </div>
                    <table id="tablerole" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModalAdmin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Role Admin</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="nama_editrole">nama_role <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editusername" placeholder="Isikan nama_role (case sensitive)" autocomplete="off" name="username" value="">
                                    <input type="hidden" id="id_role">

                                </div>
                                <div class="form-group ">
                                    <label for="inputeditRole" class="mt-3">Role <small class="text-warning"></small></label>
                                    <hr>
                                    <small>Elektronik</small>
                                    <div class="form-group row">
                                        <div class="col-1"></div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-edittradein">
                                            <label class="form-check-label" for="role-tradein">
                                                Data Tradein
                                            </label>
                                        </div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editstatistik">
                                            <label class="form-check-label" for="role-statistik">
                                                Statistik Tradein
                                            </label>
                                        </div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editpotongan">
                                            <label class="form-check-label" for="role-potongan">
                                                Data Potongan
                                            </label>
                                        </div>
                                        <div class="col-2"></div>
                                    </div>
                                    <hr>
                                    <small>Master</small>
                                    <div class="form-group row">
                                        <div class="col-1"></div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editadmin">
                                            <label class="form-check-label" for="role-admin">
                                                <span class="text-danger">Master Admin</span>
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editrole">
                                            <label class="form-check-label" for="role-role">
                                                <span class="text-danger">Master Role</span>
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editkategori">
                                            <label class="form-check-label" for="role-kategori">
                                                <span class="text-danger">Master Kategori</span>
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editpameran">
                                            <label class="form-check-label" for="role-pameran">
                                                Master Pameran
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-1"></div>

                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editproduk">
                                            <label class="form-check-label" for="role-produk">
                                                Master Produk
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-new_editdevice">
                                            <label class="form-check-label" for="role-new_device">
                                                Master New Device
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editjenisgrading">
                                            <label class="form-check-label" for="role-editjenisgrading">
                                                Jenis Grading
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-editkuisioner">
                                            <label class="form-check-label" for="role-editkuisioner">
                                                Master Kuisioner
                                            </label>
                                        </div>
                                    </div>
                                    <hr>
                                    <small>Others</small>
                                    <div class="form-group row">
                                        <div class="col-1"></div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-edithistory">
                                            <label class="form-check-label" for="role-history">
                                                History
                                            </label>
                                        </div>
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
                <div class="modal fade" id="modalAdmin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah Role Admin</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="nama_tambahrole">nama_role <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="nama_role" placeholder="Isikan nama_role (case sensitive)" autocomplete="off" name="username" value="">
                                </div>
                                <div class="form-group ">
                                    <label for="inputtambahRole" class="mt-3">Role <small class="text-warning"></small></label>
                                    <hr>
                                    <small>Elektronik</small>
                                    <div class="form-group row">
                                        <div class="col-1"></div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahtradein">
                                            <label class="form-check-label" for="role-tradein">
                                                Data Tradein
                                            </label>
                                        </div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahstatistik">
                                            <label class="form-check-label" for="role-statistik">
                                                Statistik Tradein
                                            </label>
                                        </div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahpotongan">
                                            <label class="form-check-label" for="role-potongan">
                                                Data Potongan
                                            </label>
                                        </div>
                                        <div class="col-2"></div>
                                    </div>
                                    <hr>
                                    <small>Master</small>
                                    <div class="form-group row">
                                        <div class="col-1"></div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahadmin">
                                            <label class="form-check-label" for="role-admin">
                                                <span class="text-danger">Master Admin</span>
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahrole">
                                            <label class="form-check-label" for="role-role">
                                                <span class="text-danger">Master Role</span>
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahkategori">
                                            <label class="form-check-label" for="role-kategori">
                                                <span class="text-danger">Master Kategori</span>
                                            </label>
                                        </div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahpameran">
                                            <label class="form-check-label" for="role-pameran">
                                                Master Pameran
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-1"></div>

                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahproduk">
                                            <label class="form-check-label" for="role-produk">
                                                Master Produk
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-new_tambahdevice">
                                            <label class="form-check-label" for="role-new_device">
                                                Master New Device
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahjenisgrading">
                                            <label class="form-check-label" for="role-jenisgrading">
                                                Jenis Grading
                                            </label>
                                        </div>
                                        <div class="form-check col-2">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahkuisioner">
                                            <label class="form-check-label" for="role-kuisioner">
                                                Master Kuisioner
                                            </label>
                                        </div>
                                    </div>
                                    <hr>
                                    <small>Other</small>
                                    <div class="form-group row">
                                        <div class="col-1"></div>
                                        <div class="form-check col-3">
                                            <input class="form-check-input myCheckbox" type="checkbox" value="" id="role-tambahhistory">
                                            <label class="form-check-label" for="role-history">
                                                History
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahrole" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah Role Admin</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?= $this->section('content_css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3//plugins/toastr/toastr.min.css">
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

    #tabeladmin {
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
    const path = '/MasterRole'
    $(document).ready(function() {
        $('body').on('click', '.editModalAdmin', function() {
            var id = $(this).data('id_role');
            var nama_role = $(this).data('nama_role');
            var tradein = $(this).data('tradein');
            var statistik = $(this).data('statistik');
            var kategori = $(this).data('kategori');
            var produk = $(this).data('produk');
            var jenisgrading = $(this).data('grading');
            var potongan = $(this).data('potongan');
            var kuisioner = $(this).data('kuisioner');
            var new_device = $(this).data('newdevice');
            var pameran = $(this).data('pameran');
            var admin = $(this).data('admin');
            var role = $(this).data('role');


            var log = $(this).data('log');

            $('#id_role').val(id);
            $('#editusername').val(nama_role);
            if (tradein == "1") $('#role-edittradein').prop('checked', true);
            if (statistik == "1") $('#role-editstatistik').prop('checked', true);


            if (potongan == "1") $('#role-editpotongan').prop('checked', true);
            if (produk == "1") $('#role-editproduk').prop('checked', true);
            if (new_device == "1") $('#role-new_editdevice').prop('checked', true);
            if (pameran == "1") $('#role-editpameran').prop('checked', true);
            if (jenisgrading == "1") $('#role-editjenisgrading').prop('checked', true);
            if (kuisioner == "1") $('#role-editkuisioner').prop('checked', true);
            if (admin == "1") $('#role-editadmin').prop('checked', true);
            if (kategori == "1") $('#role-editkategori').prop('checked', true);
            if (log == "1") $('#role-edithistory').prop('checked', true);
            if (role == "1") $('#role-editrole').prop('checked', true);


            $('#editModalAdmin').modal('show');

        });
        let datatable = $("#tablerole").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/MasterRole/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#filter-date').val();
                    return d;
                },
            },
            columnDefs: [{
                targets: [0, 1, 2],
            }, {
                targets: [0, 2],
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
            var id = $(this).data('id_role');
            var username = $(this).data('username');
            //     var username = $(this).data('username');
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
                        url: `${base_url}${path}/HapusRole`,
                        type: "post",
                        dataType: "json",
                        data: {
                            id: id,

                        },

                        success: function(data) {
                            if (data.success == true) {
                                $('#editModalAdmin').modal('hide');
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

            var id_role = $('#id_role').val();
            var nama_role = $('#editusername').val();
            var tradein = $('#role-edittradein').prop('checked');
            var statistik = $('#role-editstatistik').prop('checked');
            var potongan = $('#role-editpotongan').prop('checked');
            var new_device = $('#role-new_editdevice').prop('checked');
            var produk = $('#role-editproduk').prop('checked');
            var kuisioner = $('#role-editkuisioner').prop('checked');
            var grading = $('#role-editjenisgrading').prop('checked');
            var pameran = $('#role-editpameran').prop('checked');
            var admin = $('#role-editadmin').prop('checked');
            var kategori = $('#role-editkategori').prop('checked');
            var role = $('#role-editrole').prop('checked');
            var log = $('#role-edithistory').prop('checked');
            if (tradein == false) {
                tradein = 0
            } else {
                tradein = 1
            }
            if (statistik == false) {
                statistik = 0
            } else {
                statistik = 1
            }
            if (kuisioner == false) {
                kuisioner = 0
            } else {
                kuisioner = 1
            }
            if (potongan == false) {
                potongan = 0
            } else {
                potongan = 1
            }
            if (new_device == false) {
                new_device = 0
            } else {
                new_device = 1
            }
            if (grading == false) {
                grading = 0
            } else {
                grading = 1
            }
            if (produk == false) {
                produk = 0
            } else {
                produk = 1
            }
            if (pameran == false) {
                pameran = 0
            } else {
                pameran = 1
            }
            if (admin == false) {
                admin = 0
            } else {
                admin = 1
            }
            if (kategori == false) {
                kategori = 0
            } else {
                kategori = 1
            }
            if (role == false) {
                role = 0
            } else {
                role = 1
            }
            if (log == false) {
                log = 0
            } else {
                log = 1
            }
            var testing = true;
            if (tradein == 0 && kuisioner == 0 && statistik == 0 && kategori == 0 && grading == 0 && potongan == 0 && new_device == 0 && produk == 0 && pameran == 0 && admin == 0 && role == 0 && log == 0) {
                testing = false;
            }

            if (nama_role == '') {
                $('*[for="nama_editrole"] > small').html('Harap diisi!');
                $('*[for="inputeditRole"] > small').html('');
            } else if (testing == false) {
                $('*[for="nama_editrole"] > small').html('');
                $('*[for="inputeditRole"] > small').html('Harap pilih minimal 1 Role!');
            } else {
                $('*[for="nama_editrole"] > small').html('');
                $('*[for="inputeditRole"] > small').html('');
                Swal.fire({
                    title: 'Apakah Anda ingin menyimpan  Perubahan Data ini?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url() ?>/MasterRole/update_data',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'tambahUserAdmin',
                                data: {
                                    id_role: id_role,
                                    nama_role: nama_role,
                                    tradein: tradein,
                                    statistik: statistik,
                                    potongan: potongan,
                                    new_device: new_device,
                                    kuisioner: kuisioner,
                                    produk: produk,
                                    pameran: pameran,
                                    admin: admin,
                                    kategori: kategori,
                                    grading: grading,
                                    role: role,
                                    log: log,
                                }

                            },

                            success: function(data) {
                                if (data.success == true) {
                                    $('#editModalAdmin').modal('hide');
                                    toastr.success('Update Data Role Succesfully')
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
        // $('body').on('click', '#editshowPassword', function() {
        //     toggle = !toggle;
        //     // console.log(toggle);
        //     if (toggle) {
        //         $(this)
        //             .removeClass('fa-eye-slash')
        //             .addClass('fa-eye');
        //         $('#editpassword').attr('type', 'text');
        //     } else {
        //         $(this)
        //             .removeClass('fa-eye')
        //             .addClass('fa-eye-slash');
        //         $('#editpassword').attr('type', 'password');
        //     }
        // });
        // $('body').on('click', '#showPassword', function() {
        //     toggle = !toggle;
        //     // console.log(toggle);
        //     if (toggle) {
        //         $(this)
        //             .removeClass('fa-eye-slash')
        //             .addClass('fa-eye');
        //         $('#password').attr('type', 'text');
        //     } else {
        //         $(this)
        //             .removeClass('fa-eye')
        //             .addClass('fa-eye-slash');
        //         $('#password').attr('type', 'password');
        //     }
        // });
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
        $('#tambahrole').on('click', function() {
            var nama_role = $('#nama_role').val();
            var tradein = $('#role-tambahtradein').prop('checked');
            var statistik = $('#role-tambahstatistik').prop('checked');
            var potongan = $('#role-tambahpotongan').prop('checked');
            var kategori = $('#role-tambahkategori').prop('checked');
            var grading = $('#role-tambahjenisgrading').prop('checked');
            var new_device = $('#role-new_tambahdevice').prop('checked');
            var produk = $('#role-tambahproduk').prop('checked');
            var pameran = $('#role-tambahpameran').prop('checked');
            var kuisioner = $('#role-tambahkuisioner').prop('checked');
            var admin = $('#role-tambahadmin').prop('checked');
            var role = $('#role-tambahrole').prop('checked');
            var log = $('#role-tambahhistory').prop('checked');
            if (tradein == false) {
                tradein = 0
            } else {
                tradein = 1
            }
            if (statistik == false) {
                statistik = 0
            } else {
                statistik = 1
            }
            if (kuisioner == false) {
                kuisioner = 0
            } else {
                kuisioner = 1
            }
            if (potongan == false) {
                potongan = 0
            } else {
                potongan = 1
            }
            if (new_device == false) {
                new_device = 0
            } else {
                new_device = 1
            }
            if (grading == false) {
                grading = 0
            } else {
                grading = 1
            }
            if (produk == false) {
                produk = 0
            } else {
                produk = 1
            }
            if (pameran == false) {
                pameran = 0
            } else {
                pameran = 1
            }
            if (admin == false) {
                admin = 0
            } else {
                admin = 1
            }
            if (kategori == false) {
                kategori = 0
            } else {
                kategori = 1
            }
            if (role == false) {
                role = 0
            } else {
                role = 1
            }
            if (log == false) {
                log = 0
            } else {
                log = 1
            }
            var noRoleSelected = true;
            if (
                tradein || statistik

                ||
                role ||
                admin || log || pameran ||
                produk || new_device || potongan || kategori || grading || kuisioner

            ) noRoleSelected = false;
            if (nama_role == '') {
                $('*[for="nama_tambahrole"] > small').html('Harap diisi!');
                $('*[for="inputtambahRole"] > small').html('');
            } else if (noRoleSelected) {
                $('*[for="nama_tambahrole"] > small').html('');
                $('*[for="inputtambahRole"] > small').html('Harap pilih minimal 1 Role!');
            } else {
                $('*[for="nama_tambahrole"] > small').html('');
                $('*[for="inputtambahRole"] > small').html('');
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
                            url: '<?= base_url() ?>/MasterRole/tambah_data',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'tambahUserAdmin',
                                data: {
                                    nama_role: nama_role,
                                    tradein: tradein,
                                    statistik: statistik,
                                    potongan: potongan,
                                    new_device: new_device,
                                    produk: produk,
                                    kuisioner: kuisioner,
                                    pameran: pameran,
                                    kategori: kategori,
                                    grading: grading,
                                    admin: admin,
                                    role: role,
                                    log: log,
                                }

                            },

                            success: function(data) {
                                if (data.success == true) {
                                    $('#modalAdmin').modal('hide');
                                    toastr.success('Create Data Role Succesfully')
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