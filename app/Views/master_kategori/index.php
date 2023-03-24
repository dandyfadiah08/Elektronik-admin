<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Master Kategori</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modalKategori" id="tombolTambah"><i class="fa fa-plus"></i>Tambah Kategori
                            </button>
                        </div>
                    </div>
                    <table id="tabelkategori" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModalKategori" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Kategori</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="editkategori">Kategori <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editkategori" placeholder="Isikan Kategori" name="editkategori">
                                        <input type="hidden" id="id_editkategori">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kategori">Deskripsi Gambar <small class="text-warning"></small></label>
                                        <input type="file" onchange="getImg(event)" class="form-control" style="background-color: aliceblue; color:#321" id="editdeskripsi" placeholder="Isikan Kategori" autocomplete="off" name="editdeskripsi" value="">
                                        <input type="hidden" id="id_kategori">
                                    </div>
                                    <div class="card col-md-6 mt-2" style="width: 10rem; background-color:white;color:black">
                                        <img src="" class="card-img-top mt-2" id="img" alt="Sunset Over the Sea" />
                                        <div class="card-body">
                                            <p class="card-text" id="urltext"></p>
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
                <div class="modal fade" id="modalKategori" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah Kategori</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kategori">Kategori <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="kategori" placeholder="Isikan Kategori" autocomplete="off" name="kategori" value="">
                                        <input type="hidden" id="id_kategori">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="tambahdeskripsi">Deskripsi Gambar <small class="text-warning"></small></label>
                                        <input type="file" accept="image/*" class="form-control" style="background-color: aliceblue; color:#321" id="tambahdeskripsi" placeholder="Isikan Kategori" autocomplete="off" name="tambahdeskripsi" value="">
                                        <input type="hidden" id="id_kategori">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahKategori" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah Kategori</button>
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
    const path = '/MasterKategori'

    function getImg(evt) {
        var files = evt.target.files;
        var file = files[0];
        var files2 = URL.createObjectURL(file);
        document.getElementById("img").src = files2;
        document.getElementById("urltext").innerHTML = 'Identitas foto: ' + file.name;
    }
    $(document).ready(function() {
        $('body').on('click', '.editModalKategori', function() {
            var id = $(this).data('id_kategori');
            var nama_kategori = $(this).data('nama_kategori');
            var deskripsi = $(this).data('deskripsi');
            $('#id_editkategori').val(id);
            $('#editkategori').val(nama_kategori);
            // $("#img").src = deskripsi;
            document.getElementById("img").src = deskripsi;
            var name = deskripsi.split("http://localhost:8080/assets/images/datakategori/")
            document.getElementById("urltext").innerHTML = 'Identitas foto: ' + name[1];
            // var photo = document.getElementById('editdeskripsi');
            // var file = photo.files;
            $('#editModalKategori').modal('show');

        });
        let datatable = $("#tabelkategori").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/MasterKategori/load_data',
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
            var id = $(this).data('id_kategori');
            var kategori = $(this).data('nama_kategori');
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
                        url: `${base_url}${path}/Hapuskategori`,
                        type: "post",
                        dataType: "json",
                        data: {
                            id: id,
                            kategori: kategori,

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

            var id = $('#id_editkategori').val();
            var kategori = $('#editkategori').val();
            if (kategori == '') {
                $('*[for="editkategori"] > small').html('Harap diisi');
            } else {
                $('*[for="editkategori"] > small').html('');
                var editdeskripsi = $('#editdeskripsi').val();
                if (editdeskripsi == '') {
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
                                url: `${base_url}${path}/editkategoriempty`,
                                dataType: 'json',
                                method: 'POST',
                                data: {
                                    id: id,
                                    kategori: kategori,

                                },

                                success: function(data) {
                                    if (data.success == true) {
                                        $('#editModalKategori').modal('hide');
                                        toastr.success('Update Data Kategori Succesfully')
                                        datatable.ajax.reload();
                                    } else {
                                        alert("SUCCESS: " + data.status);

                                    }
                                    // console.log(data);

                                }
                            })

                        }
                    })
                } else {
                    var photo = document.getElementById('editdeskripsi');
                    var file = photo.files[0];
                    datakategori = new FormData();
                    datakategori.append('file', file);
                    datakategori.append('kategori', kategori);
                    datakategori.append('id', id);
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
                                url: `${base_url}${path}/editkategori`,
                                dataType: 'json',
                                method: 'POST',
                                enctype: 'multipart/form-data',
                                processData: false,
                                contentType: false,
                                data: datakategori,
                                success: function(data) {
                                    if (data.success == true) {
                                        $('#editModalKategori').modal('hide');
                                        toastr.success('Update Data Kategori Succesfully')
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
        $('#tambahKategori').on('click', function() {
            var kategori = $('#kategori').val();
            var deskripsi = $('#tambahdeskripsi').val();
            if (kategori == '') {
                $('*[for="kategori"] > small').html('Harap diisi!');
                $('*[for="tambahdeskripsi"] > small').html('');
            } else if (deskripsi == '') {
                $('*[for="kategori"] > small').html('');
                $('*[for="tambahdeskripsi"] > small').html('Harap uoload gambar kategori!');
            } else {
                $('*[for="kategori"] > small').html('');
                $('*[for="tambahdeskripsi"] > small').html('');
                Swal.fire({
                    title: 'Apakah Anda ingin menyimpan  Data ini?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        var photo = document.getElementById('tambahdeskripsi');
                        var file = photo.files[0];
                        datakategori = new FormData();
                        datakategori.append('file', file);
                        datakategori.append('kategori', $('#kategori').val());
                        $.ajax({
                            url: '<?= base_url() ?>/MasterKategori/tambah_data',
                            dataType: 'json',
                            method: 'POST',
                            enctype: 'multipart/form-data',
                            processData: false,
                            contentType: false,
                            data: datakategori,
                            success: function(data) {
                                if (data.success == true) {
                                    $('#modalKategori').modal('hide');
                                    toastr.success('Create Data Kategori Succesfully')
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