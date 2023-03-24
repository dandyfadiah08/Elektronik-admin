<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Master Admin</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modalAdmin" id="tombolTambah"><i class="fa fa-plus"></i>Tambah User Admin
                            </button>
                        </div>
                    </div>
                    <table id="tabeladmin" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Role</th>
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
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit User Admin</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editusername">Username <small class="text-warning"></small></label>
                                        <input type="text" id="editusername" class="form-control" style="background-color: aliceblue; color:#321" id="username" placeholder="Isikan username (case sensitive)" autocomplete="off" name="username" value="">
                                        <input type="hidden" id="editid_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editpassword">Password <small class="text-warning"></small></label>
                                        <div class="input-group">
                                            <input type="password" style="background-color: aliceblue; color:#321" class="form-control" id="editpassword" placeholder="Isikan password (case sensitive)" autocomplete="off" name="password" value="">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor: pointer">
                                                    <span id="editshowPassword" class="fas fa-eye-slash"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="editrole">Role <small class="text-warning"></small></label>
                                    <select style="background-color: aliceblue; color:#321" class=" form-select form-control" id="editfilterrole" aria-label="Default select example">

                                        <option value="" disabled selected>Role</option>
                                        <?php foreach ($page->role as $key => $value) : ?>
                                            <option value="<?= $value['id_role'] ?>"><?= $value['nama_role'] ?></option>
                                        <?php endforeach; ?>

                                    </select>
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
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah User Admin</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="username">Username <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="username" placeholder="Isikan username (case sensitive)" autocomplete="off" name="username" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password">Password <small class="text-warning"></small></label>
                                        <div class="input-group">
                                            <input type="password" style="background-color: aliceblue; color:#321" class="form-control" id="password" placeholder="Isikan password (case sensitive)" autocomplete="off" name="password" value="">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor: pointer">
                                                    <span id="showPassword" class="fas fa-eye-slash"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="role">Role <small class="text-warning"></small></label>
                                    <select style="background-color: aliceblue; color:#321" class=" form-select form-control" id="filterrole" aria-label="Default select example">
                                        <option value="" disabled selected>Role</option>
                                        <?php foreach ($page->role as $key => $value) : ?>
                                            <option value="<?= $value['id_role'] ?>"><?= $value['nama_role'] ?></option>
                                        <?php endforeach; ?>

                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahAdmin" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah User Admin</button>
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
    const path = '/MasterAdmin'
    $(document).ready(function() {
        $('body').on('click', '.editModalAdmin', function() {
            var id = $(this).data('id_admin');
            var username = $(this).data('username');
            var password = $(this).data('password');
            var id_role = $(this).data('role');
            $('#editid_admin').val(id);
            $('#editusername').val(username);
            $('#editpassword').val(password);
            $("#editfilterrole").val(id_role);
            $('#editModalAdmin').modal('show');

        });
        let datatable = $("#tabeladmin").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/MasterAdmin/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#filter-date').val();
                    return d;
                },
            },
            columnDefs: [{
                targets: [0, 1, 2, 3],
            }, {
                targets: [0, 3],
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
            var id = $(this).data('id_admin');
            var username = $(this).data('username');
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
                        url: `${base_url}${path}/HapusAdmin`,
                        type: "post",
                        dataType: "json",
                        data: {
                            id: id,
                            username: username,

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

            var id = $('#editid_admin').val();
            var username = $('#editusername').val();
            var password = $('#editpassword').val();
            var role = $('#editfilterrole').val();
            if (username == '') {
                $('*[for="editusername"] > small').html('Harap diisi');
                $('*[for="editrole"] > small').html('');
                $('*[for="editpassword"] > small').html('');
            } else if (password == '') {
                $('*[for="editusername"] > small').html('');
                $('*[for="editrole"] > small').html('');
                $('*[for="editpassword"] > small').html('Harap diisi');
            } else if (role == '') {
                $('*[for="editusername"] > small').html('');
                $('*[for="editrole"] > small').html('Harap dipilih');
                $('*[for="editpassword"] > small').html('');
            } else {
                $('*[for="editusername"] > small').html('');
                $('*[for="editrole"] > small').html('');
                $('*[for="editpassword"] > small').html('');
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
                            url: `${base_url}${path}/editAdmin`,
                            type: "post",
                            dataType: "json",
                            data: {
                                id: id,
                                username: username,
                                password: password,
                                id_role: role,

                            },

                            success: function(data) {
                                if (data.success == true) {
                                    $('#editModalAdmin').modal('hide');
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
        $('#tambahAdmin').on('click', function() {
            var username = $('#username').val();
            var password = $('#password').val();
            var role = $('#filterrole').val();

            if (username == '') {
                $('*[for="username"] > small').html('Harap diisi!');
                $('*[for="password"] > small').html('');
                $('*[for="role"] > small').html('');
            } else if (password == '') {
                $('*[for="username"] > small').html('');
                $('*[for="password"] > small').html('Harap diisi!');
                $('*[for="role"] > small').html('');
            } else if (role == null) {
                $('*[for="username"] > small').html('');
                $('*[for="password"] > small').html('');
                $('*[for="role"] > small').html('Harap pilih Role!');
            } else {
                $('*[for="username"] > small').html('');
                $('*[for="password"] > small').html('');
                $('*[for="role"] > small').html('');
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
                            url: '<?= base_url() ?>/MasterAdmin/tambah_data',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'tambahUserAdmin',
                                data: {
                                    username: username,
                                    password: password,
                                    role: role,
                                }
                            },
                            success: function(data) {
                                if (data.success == true) {
                                    $('#modalAdmin').modal('hide');
                                    toastr.success('Create Data Admin Succesfully')
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