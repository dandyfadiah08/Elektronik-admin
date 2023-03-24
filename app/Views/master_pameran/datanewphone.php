<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Data New Phone</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modalnewphone" id="tombolTambah"><i class="fa fa-plus"></i>Tambah
                            </button>
                            <input type="hidden" id="id_pameran" value="<?= $page->idpameran ?>">


                        </div>
                    </div>
                    <table id="tabeladmin" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Black</th>
                                <th>White</th>
                                <th>Green</th>
                                <th>Gold</th>
                                <th>Silver</th>
                                <th>Gray</th>
                                <th>Total Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModalNewPhone" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Data New Phone</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="editbrand">Brand <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editbrand" placeholder="Isikan Brand" autocomplete="off" name="editbrand" value="">
                                    <input type="hidden" id="editid_product">
                                </div>
                                <div class="form-group">
                                    <label for="editkode">Kode <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editkode" placeholder="Isikan Kode" autocomplete="off" name="editkode" value="">
                                    <input type="hidden" id="editid_pameran">
                                </div>
                                <div class="form-group">
                                    <label for="editmodel">Model <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editmodel" placeholder="Isikan Model" autocomplete="off" name="editmodel" value="">

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editblack">Black <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control editinputStok" style="background-color: aliceblue; color:#321" id="editblack" placeholder="Isi Jumlah Warna Hitam" autocomplete="off" name="editblack" value="">
                                        <input type="hidden" id="editid_product">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editwhite">White <small class="text-warning"></small></label>
                                        <input type="text" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control editinputStok" style="background-color: aliceblue; color:#321" id="editwhite" placeholder="Isi Jumlah Warna Putih" autocomplete="off" name="editwhite" value="">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editgreen">Green <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control editinputStok" style="background-color: aliceblue; color:#321" id="editgreen" placeholder="Isi Jumlah Warna Hijau" autocomplete="off" name="editgreen" value="">

                                    </div>
                                    <div class="col-md-6">
                                        <label for="editgold">Gold <small class="text-warning"></small></label>
                                        <input type="text" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control editinputStok" style="background-color: aliceblue; color:#321" id="editgold" placeholder="Isi Jumlah Warna Emas" autocomplete="off" name="editgold" value="">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editsilver">Silver <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control editinputStok" style="background-color: aliceblue; color:#321" id="editsilver" placeholder="Isi Jumlah Warna Perak" autocomplete="off" name="editsilver" value="">

                                    </div>
                                    <div class="col-md-6">
                                        <label for="editgray">Gray <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control editinputStok" style="background-color: aliceblue; color:#321" id="editgray" placeholder="Isi Jumlah Warna Abu-Abu" autocomplete="off" name="editgray" value="">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="editstok">Total Stock <small class="text-warning"></small></label>
                                    <input readonly type="text" class="form-control" style="background-color: 	#C0C0C0; color:white" id="editstok" placeholder="Total Stok" autocomplete="off" name="editstok" value="">

                                </div>
                                <div class="form-group">
                                    <label for="editharga">Harga <small class="text-warning"></small></label>
                                    <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="editharga" placeholder="Isikan Harga Produk" autocomplete="off" name="editharga" value="">

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
                <div class="modal fade" id="modalnewphone" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah Data New Phone</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="brand">Brand <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="brand" placeholder="Isikan Brand" autocomplete="off" name="brand" value="">
                                    <input type="hidden" id="id_admin">
                                </div>
                                <div class="form-group">
                                    <label for="kode">Kode <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="kode" placeholder="Isikan Kode" autocomplete="off" name="kode" value="">
                                    <input type="hidden" id="id_admin">
                                </div>
                                <div class="form-group">
                                    <label for="model">Model <small class="text-warning"></small></label>
                                    <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="model" placeholder="Isikan Model" autocomplete="off" name="model" value="">
                                    <input type="hidden" id="id_admin">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="black">Black <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control inputStok" style="background-color: aliceblue; color:#321" id="black" placeholder="Isi Jumlah Warna Hitam" autocomplete="off" name="black" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="white">White <small class="text-warning"></small></label>
                                        <input type="text" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control inputStok" style="background-color: aliceblue; color:#321" id="white" placeholder="Isi Jumlah Warna Putih" autocomplete="off" name="white" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="green">Green <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control inputStok" style="background-color: aliceblue; color:#321" id="green" placeholder="Isi Jumlah Warna Hijau" autocomplete="off" name="green" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gold">Gold <small class="text-warning"></small></label>
                                        <input type="text" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control inputStok" style="background-color: aliceblue; color:#321" id="gold" placeholder="Isi Jumlah Warna Emas" autocomplete="off" name="gold" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="silver">Silver <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control inputStok" style="background-color: aliceblue; color:#321" id="silver" placeholder="Isi Jumlah Warna Perak" autocomplete="off" name="silver" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gray">Gray <small class="text-warning"></small></label>
                                        <input type="number" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="form-control inputStok" style="background-color: aliceblue; color:#321" id="gray" placeholder="Isi Jumlah Warna Abu-Abu" autocomplete="off" name="gray" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="stok">Total Stock <small class="text-warning"></small></label>
                                    <input readonly type="text" class="form-control" style="background-color: 	#C0C0C0; color:white" id="stok" placeholder="Total Stok" autocomplete="off" name="stok" value="">
                                    <input type="hidden" id="id_admin">
                                </div>
                                <div class="form-group">
                                    <label for="harga">Harga <small class="text-warning"></small></label>
                                    <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="harga" placeholder="Isikan Harga Produk" autocomplete="off" name="harga" value="">
                                    <input type="hidden" id="id_admin">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahData" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah Data</button>
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
    const path = '/MasterPameran'
    $(document).ready(function() {

        $('body').on('click', '.editModalNewPhone', function() {
            var id = $(this).data('id');
            var id_pameran = $(this).data('id_pameran');
            var kode = $(this).data('kode');
            var brand = $(this).data('brand');
            var model = $(this).data('model');
            var black = $(this).data('black');
            var white = $(this).data('white');
            var green = $(this).data('green');
            var gold = $(this).data('gold');
            var silver = $(this).data('silver');
            var gray = $(this).data('gray');
            var stok = $(this).data('stok');
            var harga = $(this).data('harga');
            $('#editid_product').val(id);
            $('#editid_pameran').val(id_pameran);
            $('#editbrand').val(brand);
            $('#editkode').val(kode);
            $('#editmodel').val(model);
            $('#editblack').val(black);
            $('#editwhite').val(white);
            $('#editgreen').val(green);
            $('#editgold').val(gold);
            $('#editsilver').val(silver);
            $('#editgray').val(gray);
            $('#editstok').val(stok);
            $('#editharga').val(harga);
            $('#editModalNewPhone').modal('show');

        });
        let datatable = $("#tabeladmin").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/MasterPameran/load_data_datanew',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#filter-date').val();
                    d.id_pameran = $('#id_pameran').val();
                    return d;
                },
            },
            columnDefs: [{
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
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
        $('.editinputStok').on('keyup', function() {
            var black = parseInt($('#editblack').val());
            var white = parseInt($('#editwhite').val());
            var green = parseInt($('#editgreen').val());
            var gold = parseInt($('#editgold').val());
            var silver = parseInt($('#editsilver').val());
            var gray = parseInt($('#editgray').val());

            var stok = black + white + green + gold + silver + gray;
            if (isNaN(stok) == true) {

                $('#editstok').val('Isikan jumlah stok pada setiap warna yang telah di tentukan untuk menentukan Total Stok!!');
            } else {
                $('#editstok').val(stok);
            }


        });
        $('.inputStok').on('keyup', function() {
            var black = parseInt($('#black').val());
            var white = parseInt($('#white').val());
            var green = parseInt($('#green').val());
            var gold = parseInt($('#gold').val());
            var silver = parseInt($('#silver').val());
            var gray = parseInt($('#gray').val());

            var stok = black + white + green + gold + silver + gray;
            if (isNaN(stok) == true) {

                $('#stok').val('Isikan jumlah stok pada setiap warna yang telah di tentukan untuk menentukan Total Stok!!');
            } else {
                $('#stok').val(stok);
            }


        });
        $('body').on('click', '.tombolHapus', function() {
            var id = $(this).data('id');
            var brand = $(this).data('brand');
            var nama_pameran = $(this).data('nama_pameran');
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
                        url: '<?= base_url() ?>/MasterPameran/HapusdataNewPhone',
                        type: "post",
                        dataType: "json",
                        data: {
                            id: id,
                            brand: brand,
                            nama_pameran: nama_pameran

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
            var id = $('#editid_product').val();
            var id_pameran = $('#editid_pameran').val();
            var brand = $('#editbrand').val();
            var kode = $('#editkode').val();
            var model = $('#editmodel').val();
            var black = $('#editblack').val();
            var white = $('#editwhite').val();
            var green = $('#editgreen').val();
            var gold = $('#editgold').val();
            var silver = $('#editsilver').val();
            var gray = $('#editgray').val();
            var stok = $('#editstok').val();
            var harga = $('#editharga').val();
            if (brand == '') {
                $('*[for="editbrand"] > small').html('Harap diisi!');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');

            } else if (kode == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('Harap diisi!');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (model == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('Harap diisi!');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (black == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('Harap diisi!');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (white == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('Harap diisi!');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (green == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('Harap diisi!');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (gold == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('Harap diisi!');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (silver == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('Harap diisi!');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (gray == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('Harap diisi!');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
            } else if (harga == '') {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('Harap diisi!');
            } else {
                $('*[for="editbrand"] > small').html('');
                $('*[for="editkode"] > small').html('');
                $('*[for="editmodel"] > small').html('');
                $('*[for="editblack"] > small').html('');
                $('*[for="editwhite"] > small').html('');
                $('*[for="editgreen"] > small').html('');
                $('*[for="editgold"] > small').html('');
                $('*[for="editsilver"] > small').html('');
                $('*[for="editgray"] > small').html('');
                $('*[for="editstok"] > small').html('');
                $('*[for="editharga"] > small').html('');
                Swal.fire({
                    title: 'Apakah Anda ingin menyimpan Perubahan  Data ini?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url() ?>/MasterPameran/edit_dataNewPhone',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'editPameran',
                                data: {
                                    id: id,
                                    id_pameran: id_pameran,
                                    brand: brand,
                                    kode: kode,
                                    model: model,
                                    black: black,
                                    white: white,
                                    green: green,
                                    gold: gold,
                                    silver: silver,
                                    gray: gray,
                                    stok: stok,
                                    harga: harga
                                }
                            },
                            success: function(data) {
                                if (data.success == true) {
                                    $('#editModalNewPhone').modal('hide');
                                    toastr.success('Update Data New Phones Succesfully')
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
        $('#tambahData').on('click', function() {
            var id_pameran = $('#id_pameran').val();
            var brand = $('#brand').val();
            var kode = $('#kode').val();
            var model = $('#model').val();
            var black = $('#black').val();
            var white = $('#white').val();
            var green = $('#green').val();
            var gold = $('#gold').val();
            var silver = $('#silver').val();
            var gray = $('#gray').val();
            var stok = $('#stok').val();
            var harga = $('#harga').val();

            if (brand == '') {
                $('*[for="brand"] > small').html('Harap diisi!');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');

            } else if (kode == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('Harap diisi!');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (model == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('Harap diisi!');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (black == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('Harap diisi!');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (white == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('Harap diisi!');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (green == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('Harap diisi!');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (gold == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('Harap diisi!');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (silver == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('Harap diisi!');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (gray == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('Harap diisi!');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
            } else if (harga == '') {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('Harap diisi!');
            } else {
                $('*[for="brand"] > small').html('');
                $('*[for="kode"] > small').html('');
                $('*[for="model"] > small').html('');
                $('*[for="black"] > small').html('');
                $('*[for="white"] > small').html('');
                $('*[for="green"] > small').html('');
                $('*[for="gold"] > small').html('');
                $('*[for="silver"] > small').html('');
                $('*[for="gray"] > small').html('');
                $('*[for="stok"] > small').html('');
                $('*[for="harga"] > small').html('');
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
                            url: '<?= base_url() ?>/MasterPameran/tambah_data_newphone',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'tambahnewphone',
                                data: {
                                    id_pameran: id_pameran,
                                    brand: brand,
                                    kode: kode,
                                    model: model,
                                    black: black,
                                    white: white,
                                    green: green,
                                    gold: gold,
                                    silver: silver,
                                    gray: gray,
                                    stok: stok,
                                    harga: harga


                                }
                            },
                            success: function(data) {
                                if (data.success == true) {
                                    $('#modalnewphone').modal('hide');
                                    toastr.success('Create Data New Phone Succesfully')
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