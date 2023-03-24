<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Jenis Grading</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modalGrading" id="tombolTambah"><i class="fa fa-plus"></i>Tambah Jenis Grading
                            </button>
                        </div>
                    </div>
                    <table id="tabeljenisgrading" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Grading</th>
                                <th>Nama Grading</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModalGrading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Jenis Grading</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editkode_grading">Kode Grading <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editkode_grading" placeholder="Isikan Kode Grading" autocomplete="off" name="editkode_grading" value="">
                                        <input type="hidden" id="editid_jgrading">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editnama_grading">Nama Grading <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editnama_grading" placeholder="Isikan Nama Grading" autocomplete="off" name="editnama_grading" value="">
                                        <input type="hidden" id="editid_jgrading">
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
                <div class="modal fade" id="modalGrading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah Jenis Grading</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="kode_grading">Kode Grading <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="kode_grading" placeholder="Isikan Kode Grading" autocomplete="off" name="kode_grading" value="">
                                        <input type="hidden" id="id_jgrading">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama_grading">Nama Grading <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="nama_grading" placeholder="Isikan Nama Grading" autocomplete="off" name="nama_grading" value="">
                                        <input type="hidden" id="id_jgrading">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahgrading" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah Jenis Grading</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="listModalGrading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Data List Kuisioner</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <input type="hidden" id="id_listjgrading">
                            </div>
                            <div class="modal-body">
                                <div class="form-group " id="listkuisioner">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" id="editlist" class="btn btn-primary">Update</button>
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

    #tabelkategori {
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
    const path = '/Jenis_grading'
    $(document).ready(function() {
        $('body').on('click', '.editModalGrading', function() {

            var id = $(this).data('id_jgrading');
            var nama_grading = $(this).data('nama_grading');
            var kode_grading = $(this).data('kode_grading');
            $('#editid_jgrading').val(id);
            $('#editkode_grading').val(kode_grading);
            $('#editnama_grading').val(nama_grading);
            $('#editModalGrading').modal('show');

        });
        $('body').on('click', '.listModalGrading', function() {
            document.getElementById("listkuisioner").innerHTML = ''
            var id = $(this).data('id_jgrading');
            $('#id_listjgrading').val(id);
            $.ajax({
                url: `${base_url}${path}/datakuisioner`,
                type: "post",
                dataType: "json",
                data: {
                    id: id,

                },
                success: function(data) {
                    datafixed = [];
                    data['datakuisioner'].forEach(element => {
                        document.getElementById("listkuisioner").innerHTML +=
                            "<b>Kuisioner</b><br><small>" + element['kuisioner'] + "<br><br></small><b>Option Kuisioner Syarat Grading</b><br><br>";
                        var tamp = ''
                        dataidlist = [];
                        data['datalistkuisioner'].forEach(elementdatalist => {
                            if (elementdatalist['id_mkuisioner'] == element['id_mkuisioner']) {
                                tamp = tamp + "<div class='form-check col-3'><input class='form-check-input myCheckbox' type='checkbox' value='" + elementdatalist['id_listkuisioner'] + "' id='role-tambah" + elementdatalist['id_listkuisioner'] + "'><label class='form-check-label' for='role-statistik'>" + elementdatalist['list'] + "</label></div>";
                                dataidlist.push({
                                    'id_mkuisioner': element['id_mkuisioner'],
                                    'div_id': "role-tambah" + elementdatalist['id_listkuisioner']
                                })
                            }
                        });
                        datafixed.push({
                            'id_mkusiioner': element['id_mkuisioner'],
                            'data_list': dataidlist
                        })
                        if (tamp == '') {
                            document.getElementById("listkuisioner").innerHTML +=
                                "<div align='center' >List Check Kuisioner ini Sudah  di Gunakan</div><hr>";
                        } else {

                            document.getElementById("listkuisioner").innerHTML +=
                                "<div class='form-group row ml-3'>" + tamp + "</div><hr>";
                        }

                    });
                    var jumlahdata = data['datachecked'].length
                    if (jumlahdata > 0) {
                        data['datachecked'].forEach(element => {
                            $('#role-tambah' + element['id_listkuisioner']).prop('checked', true)
                        });
                    }
                    $('#listModalGrading').modal('show');

                }
            })


        });
        let datatable = $("#tabeljenisgrading").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/Jenis_grading/load_data',
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
            var id = $(this).data('id_jgrading');
            var nama_grading = $(this).data('nama_grading');
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
                        url: `${base_url}${path}/Hapusgrading`,
                        type: "post",
                        dataType: "json",
                        data: {
                            id: id,
                            nama_grading: nama_grading,

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

            var id = $('#editid_jgrading').val();
            var kode_grading = $('#editkode_grading').val();
            var nama_grading = $('#editnama_grading').val();
            if (kode_grading == '') {
                $('*[for="editkode_grading"] > small').html('Harap diisi');
                $('*[for="editnama_grading"] > small').html('');
            } else if (nama_grading == '') {
                $('*[for="editkode_grading"] > small').html('');
                $('*[for="editnama_grading"] > small').html('Harap diisi');
            } else {
                $('*[for="editkode_grading"] > small').html('');
                $('*[for="editnama_grading"] > small').html('');
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
                            url: `${base_url}${path}/editgrading`,
                            type: "post",
                            dataType: "json",
                            data: {
                                id: id,
                                kode_grading: kode_grading,
                                nama_grading: nama_grading,

                            },

                            success: function(data) {
                                if (data.success == true) {
                                    $('#editModalGrading').modal('hide');
                                    toastr.success('Update Data Grading Succesfully')
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
        $('#tambahgrading').on('click', function() {
            var kodegrading = $('#kode_grading').val();
            var namagrading = $('#nama_grading').val();
            if (kodegrading == '') {
                $('*[for="kode_grading"] > small').html('Harap diisi!');
                $('*[for="nama_grading"] > small').html('');
            } else if (namagrading == '') {
                $('*[for="kode_grading"] > small').html('');
                $('*[for="nama_grading"] > small').html('Harap diisi!');
            } else {
                $('*[for="kode_grading"] > small').html('');
                $('*[for="nama_grading"] > small').html('');
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
                            url: '<?= base_url() ?>/Jenis_grading/tambah_data',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'tambahGrading',
                                data: {
                                    kodegrading: kodegrading,
                                    namagrading: namagrading,
                                }
                            },
                            success: function(data) {
                                if (data.success == true) {
                                    $('#modalGrading').modal('hide');
                                    toastr.success('Create Data Grading Succesfully')
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
        $('#editlist').on('click', function() {
            var valuelist = []
            var id_mkuisionerarray = [];
            var id_lgrading = $('#id_listjgrading').val();
            datafixed.forEach(element => {
                element['data_list'].forEach(elementlist => {
                    if ($('#' + elementlist['div_id']).prop('checked') == true) {
                        valuelist.push({
                            'id_mkuisioner': element['id_mkusiioner'],
                            'value': $('#' + elementlist['div_id']).val()
                        })
                    }
                });
            });
            var lengthvalue = valuelist.length;
            if (lengthvalue <= 0) {
                toastr.error('Data List Kuisioner Kosong')
                $('#listModalGrading').modal('hide');
            } else {
                $.ajax({
                    url: '<?= base_url() ?>/Jenis_grading/kuisionergrading',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        aksi: 'kuisionerGrading',
                        data: {
                            id_lgrading: id_lgrading,
                            valuelist: valuelist,
                        }
                    },
                    success: function(data) {
                        console.log(data)
                        toastr.success('Data List Kuisioner Telah Tersimpan')
                        $('#listModalGrading').modal('hide');
                    }
                })
            }
        });
    });
</script>
<?= $this->endSection('content_js') ?>