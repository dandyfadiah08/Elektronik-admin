<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Data Harga Produk</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <button style="color: black;" class="btn btn-block btn-warning btn-sm" data-toggle="modal" data-target="#modaltambahharga" id="tombolTambah"><i class="fa fa-plus"></i> &nbsp;Tambah
                            </button>
                            <input type="hidden" id="id_pameran" value="<?= $page->idpameran ?>">
                        </div>
                        <div class="col-md-2">
                            <button style="color: black;" class="btn btn-block btn-info btn-sm modalimport" data-toggle="modal" data-target="#modalimport" id="import"><i class="fas fa-upload"></i> &nbsp; Import
                            </button>
                            <input type="hidden" id="id_pameran" value="<?= $page->idpameran ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="export" class="btn btn-block btn-success  btn-sm export" style="color: black;"><i class="fas fa-download"></i> &nbsp;Export</button>
                            <input type="hidden" id="id_pameran" value="<?= $page->idpameran ?>">
                        </div>
                        <div class="col-md-2">
                            <button style="color: black;" class="btn btn-block btn-danger btn-sm tomboldeleteall" id="tomboldeleteall"><i class="fas fa-trash-alt"></i> &nbsp;Delete All
                            </button>
                            <input type="hidden" id="id_pameran" value="<?= $page->idpameran ?>">


                        </div>
                    </div>
                    <table id="tabelharga" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Produk</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Spesifikasi</th>
                                <th>Size</th>
                                <th>Tahun</th>
                                <th>Subsidi</th>
                                <th>Subsidi Mitra 1</th>
                                <th>Subsidi Mitra 2</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="editModalharga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">EDIT DATA PRODUK</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editkode_produk">Kode Produk: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="editkode_produk" placeholder="Isi Kode Produk" autocomplete="off" name="black" value="">
                                        <input type="hidden" id="editid_product">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editspec">Spesifikasi: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="editspec" placeholder="Isi Spesifikasi" autocomplete="off" name="white" value="">
                                        <input type="hidden" id="editid_pameran">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="editkategori">Kategori <small class="text-warning"></small></label>
                                        <select style="background-color: aliceblue; color:#321" class="form-select form-control" id="editkategori" aria-label="Default select example">
                                            <option value="" disabled selected> ------------ Pilih Kategori ---------- </option>
                                            <?php foreach ($page->kategori as $key => $value) : ?>
                                                <option value="<?= $value['nama_kategori'] ?>"><?= $value['nama_kategori'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edittahun">Tahun: <small class="text-warning"></small></label>
                                        <input type="text" id="editthn" name="editthn" style="background-color: aliceblue; color:#321" class="form-control" placeholder="Isi Tahun" />
                                    </div>
                                    <div class="col-md-3">
                                        <label for="editsize">Size: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="editsize" placeholder="Isi Size" autocomplete="off" name="white" value="">

                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="editmerk">Merk: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="editmerk" placeholder="Isi Merk" autocomplete="off" name="black" value="">

                                    </div>
                                    <div class="col-md-6">
                                        <label for="editsubsidi">Subsidi: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="editsubsidi" placeholder="Isi Subsidi" autocomplete="off" name="white" value="">

                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12 " style="text-align: center;">
                                        <label for="labeleditharga">
                                            <h3 style="color:#00FF7F;">Harga Per Grading</h3> <small class="text-warning"></small>
                                        </label>
                                    </div>
                                </div>
                                <div class="row mt-3" id="listedit">
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
                <div class="modal fade" id="modaltambahharga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah Data Produk</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="kode_produk">Kode Produk: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="kode_produk" placeholder="Isi Kode Produk" autocomplete="off" name="black" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="spec">Spesifikasi: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="spec" placeholder="Isi Spesifikasi" autocomplete="off" name="white" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="kategori">Kategori <small class="text-warning"></small></label>
                                        <select style="background-color: aliceblue; color:#321" class="form-select form-control" id="kategori" aria-label="Default select example">
                                            <option value="" disabled selected> ------------ Pilih Kategori ---------- </option>
                                            <?php foreach ($page->kategori as $key => $value) : ?>
                                                <option value="<?= $value['nama_kategori'] ?>"><?= $value['nama_kategori'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tahun">Tahun: <small class="text-warning"></small></label>
                                        <input type="text" id="thn" name="thn" style="background-color: aliceblue; color:#321" class="form-control" placeholder="Isi Tahun" />
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="size">Size: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="size" placeholder="Isi Size" autocomplete="off" name="white" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="merk">Merk: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="merk" placeholder="Isi Merk" autocomplete="off" name="black" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subsidi">Subsidi: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " style="background-color: aliceblue; color:#321" id="subsidi" placeholder="Isi Subsidi" autocomplete="off" name="white" value="">
                                        <input type="hidden" id="id_admin">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12 " style="text-align: center;">
                                        <label for="labeltambahharga">
                                            <h3 style="color:#00FF7F;">Harga Per Grading</h3> <small class="text-warning"></small>
                                        </label>
                                    </div>
                                </div>
                                <div class="row mt-3" id="listtambah">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="submit" style="color: aliceblue;" id="tambahData" class="btn btn-success"><i class="fas fa-save"></i> &nbsp;Tambah Data</button>
                                <button type="button" class="btn btn-danger" id="btnBatal" data-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalimport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Import Data Produk</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="post" id="formExcel" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="container-fluid">

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <p> Pilih File : </p>
                                                <div class="box">
                                                    <input accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel*" name="fileproduk" type="file" required="required">
                                                    <a href="<?php echo site_url('MasterPameran/tamplate') ?>" title="Download Template Excel" class="download">
                                                        <i class="fas fa-cloud-download-alt"></i> Download Template
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="product_id" class="product_id">
                                    <input type="hidden" name="id_pameran" value="<?= $page->idpameran ?>">
                                    <button type="submit" style="color: aliceblue;" id="import" class="btn btn-success">&nbsp;Import</button>
                                    <button type="button" class="btn btn-danger" id="btnBatal" data-dismiss="modal">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content" style="color:#00FF7F">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Harga Data Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="viewkode_produk">Kode Produk: <small class="text-warning"></small></label>
                                        <input type="text" class="form-control " readonly style="background-color: aliceblue; color:#321" id="viewkode_produk" placeholder="Isi Kode Produk" autocomplete="off" name="black" value="">
                                        <input type="hidden" id="viewid_product">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12 " style="text-align: center;">
                                        <label for="viewharga">
                                            <h3 style="color:white;">Harga Per Grading</h3> <small class="text-warning"></small>
                                        </label>
                                    </div>
                                </div>
                                <div class="row mt-3" id="listview">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" id="btnBatal" data-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?= $this->section('content_css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/Datimepicker/css/bootstrap-datepicker.min.css">
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

    #tabelharga {
        font-size: 12px;
        color: black;
    }

    .child:hover {
        color: black;
    }

    .content-wrapper {
        background: rgb(179, 179, 179);
        background: linear-gradient(0deg, rgba(179, 179, 179, 1) 0%, rgba(255, 255, 255, 1) 77%);
    }
</style>
<?= $this->endSection('content_css') ?>
<?= $this->endSection('content') ?>
<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/Datimepicker/js/bootstrap-datepicker.min.js"></script>
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
        var editlength = [];
        const listcoba = document.querySelector(".listview");
        var idharga = [];
        $('body').on('click', '.view', function() {
            var id_produk = $(this).data('id_product');
            var kode_produk = $(this).data('kode_produk');
            $('#viewkode_produk').val(kode_produk);
            document.getElementById("listview").innerHTML = " ";
            $.ajax({
                type: "POST",
                url: '<?= base_url() ?>/MasterPameran/listharga',
                dataType: "JSON",
                data: {
                    id_produk: id_produk,
                },
                cache: false,
                success: function(data) {
                    if (data == '') {

                        document.getElementById("listview").innerHTML =
                            "<div class='col-md-12 mb-3' style='text-align: center;'>Maaf Data Harga Belum Tersedia</div>";
                        $('#view').modal('show');
                    } else {

                        listviews(data);
                    }

                }
            });
            return false;

        });
        $('#tombolTambah').on('click', function() {
            document.getElementById("listtambah").innerHTML = '';
            $.ajax({
                url: '<?= base_url() ?>/MasterPameran/listmasterharga',
                dataType: 'json',
                type: 'GET',
                success: function(res) {
                    var lengthid = 0;
                    res.forEach(element => {
                        lengthid++;
                        document.getElementById("listtambah").innerHTML +=
                            "<div class='col-md-3'><label for='viewgrading'>" + element.nama_grading + "<small class='text-warning'></small></label>" + "<div class='input-group mb-3'><span class='input-group-text' id='basic-addon1'>Rp</span><input type='number' style='background-color: aliceblue; color:#321' class='form-control' id='" + 'harga' + lengthid + "' placeholder='Isi Harga' aria-label='Username' aria-describedby='basic-addon1'></div></div>";
                        idharga.push('harga' + lengthid)
                    });
                    $('#modaltambahharga').modal('show');
                }
            });
        })

        function listviews(data) {
            data.forEach(element => {
                document.getElementById("listview").innerHTML +=
                    "<div class='col-md-3'><label for='viewgrading'>" + element.nama_grading + "<small class='text-warning'></small></label>" + "<input type='text' readonly class='form-control ' style='background-color: aliceblue; color:#321' id='viewgrading' placeholder='Isi Size' autocomplete='off' name='white' value=" + element.harga + "><input type='hidden' id='viewid_product'></div>";
                $('#view').modal('show');
            });
        }
        $('body').on('click', '.editModalharga', function() {
            document.getElementById("listedit").innerHTML = ''
            var id_produk = $(this).data('id');
            $('#editid_product').val(id_produk);
            var id_pameran = $(this).data('id_pameran');
            $('#editid_pameran').val(id_pameran);
            var kode_produk = $(this).data('kode_produk');
            $('#editkode_produk').val(kode_produk);
            var spec = $(this).data('spec');
            $('#editspec').val(spec);
            var kategori = $(this).data('kategori');
            $('#editkategori').val(kategori);
            var tahun = $(this).data('tahun');
            $('#editthn').val(tahun);
            var size = $(this).data('size');
            $('#editsize').val(size);
            var merk = $(this).data('merk');
            $('#editmerk').val(merk);
            var subsidi = $(this).data('subsidi');
            var harga = $(this).data('harga');
            var number_string = subsidi.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            $('#editsubsidi').val('Rp.' + ' ' + rupiah);
            var number_string2 = harga.toString(),
                sisa2 = number_string2.length % 3,
                rupiah2 = number_string2.substr(0, sisa2),
                ribuan2 = number_string2.substr(sisa2).match(/\d{3}/g);

            if (ribuan2) {
                separator2 = sisa2 ? '.' : '';
                rupiah2 += separator2 + ribuan2.join('.');
            }
            $('#editharga').val('Rp.' + ' ' + rupiah2);
            $.ajax({
                url: '<?= base_url() ?>/MasterPameran/showedit_dataharga',
                dataType: 'json',
                method: 'POST',
                data: {
                    aksi: 'tambahharga',
                    data: {
                        id_product: id_produk,
                    }
                },
                success: function(data) {
                    var lengthedit = 0;
                    editlength = []
                    data.forEach(element => {
                        lengthedit++
                        document.getElementById("listedit").innerHTML +=
                            "<div class='col-md-3'><label for='viewgrading'>" + element.nama_grading + "<small class='text-warning'></small></label>" + "<div class='input-group mb-3'><span class='input-group-text' id='basic-addon1'>Rp</span><input type='number' style='background-color: aliceblue; color:#321' class='form-control' id='" + 'editharga' + lengthedit + "' value='" + element.harga + "' placeholder='Isi Harga' aria-label='Username' aria-describedby='basic-addon1'></div></div>";
                        editlength.push('editharga' + lengthedit)
                    });
                    $('#editModalharga').modal('show');

                    // console.log(data);

                }
            });


        });


        let datatable = $("#tabelharga").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/MasterPameran/load_data_harga',
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
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            }, {
                targets: [0, 11],
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
        var subsidilist = ''
        var hargalist = ''
        var editsubsidilist = ''
        var edithargalist = ''
        var rupiah = document.getElementById("subsidi");
        var rupiah3 = document.getElementById("editsubsidi");
        rupiah.addEventListener("keyup", function(e) {
            rupiah.value = formatRupiah(this.value, "Rp. ");
        });
        rupiah3.addEventListener("keyup", function(e) {
            // tambahkan 'Rp.' pada saat form di ketik
            // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
            rupiah3.value = formatRupiah3(this.value, "Rp. ");
        });
        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
            subsidilist = angka.replace(/[^\d]/g, "");
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }

        function formatRupiah3(angka, prefix) {
            editsubsidilist = angka.replace(/[^\d]/g, "");
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }
        $(function() {
            $('#thn').datepicker({
                format: " yyyy",
                viewMode: "years",
                minViewMode: "years"
            });
            $('#editthn').datepicker({
                format: " yyyy",
                viewMode: "years",
                minViewMode: "years"
            });
        });
        $("#formExcel").on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "/MasterPameran/ImportDataProduct",
                method: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                success: function(data) {
                    if (data.success == true) {
                        if (data.gagal >= 0 && data.berhasil == 0) {
                            $('#modalimport').modal('hide');
                            $(document).Toasts('create', {
                                class: 'bg-danger',
                                title: 'Cant Save Data Harga',
                                position: 'topLeft',
                                subtitle: false,
                                body: 'Jumlah Data:' + ' ' + data.gagal + ' ' + 'Duplicate Kode Produk' + '<br>' + 'Kode Produk:' + ' ' + data.nama
                            });
                            datatable.ajax.reload();
                        } else if (data.berhasil >= 0 && data.gagal == 0) {
                            $('#modalimport').modal('hide');
                            toastr.success('Jumlah Data:' + ' ' + data.berhasil + ' ' + 'Save Successfully')
                            datatable.ajax.reload();
                        } else {
                            $('#modalimport').modal('hide');
                            toastr.success('Data' + ' ' + data.berhasil + ' ' + 'Save Successfully')
                            $(document).Toasts('create', {
                                class: 'bg-danger',
                                title: 'Cant Save Data Harga',
                                position: 'topLeft',
                                subtitle: false,
                                body: 'Jumlah Data:' + ' ' + data.gagal + ' ' + 'Duplicate Kode Produk' + '<br>' + 'Kode Produk:' + ' ' + data.nama
                            });
                            datatable.ajax.reload();
                        }
                    } else {
                        alert("Error: " + 'Please Import Excel Dengan Benar atau cek Connection Jaringan');

                    }
                    // console.log(data);

                }
            })
        })
        $('body').on('click', '.tombolHapus', function() {
            var id_product = $(this).data('id_product');
            var kode_produk = $(this).data('kode_produk');
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
                        url: '<?= base_url() ?>/MasterPameran/HapusdataHargaperproduct',
                        type: "post",
                        dataType: "json",
                        data: {
                            id_product: id_product,
                            kode_produk: kode_produk,
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
            var id_product = $('#editid_product').val();
            var id_pameran = $('#editid_pameran').val();
            var kode_produk = $('#editkode_produk').val();
            var spec = $('#editspec').val();
            var kategori = $('#editkategori').val();
            var tahun = $('#editthn').val();
            var size = $('#editsize').val();
            var merk = $('#editmerk').val();
            var subsidi = editsubsidilist;
            var subsidisebelum = $('#editsubsidi').val();
            var harga = edithargalist;
            var valueeditharga = [];
            editlength.forEach(element => {
                var string = String(element)
                valueeditharga.push($('#' + string).val())
            });
            var hargasebelum = $('#editharga').val();
            if (kode_produk == '') {
                $('*[for="editkode_produk"] > small').html('Harap diisi!');
                $('*[for="editspec"] > small').html('');
                $('*[for="editkategori"] > small').html('');
                $('*[for="edittahun"] > small').html('');
                $('*[for="editsize"] > small').html('');
                $('*[for="editmerk"] > small').html('');
                $('*[for="editsubsidi"] > small').html('');
                $('*[for="labeleditharga"] > small').html('');
            } else if (spec == '') {
                $('*[for="editkode_produk"] > small').html('');
                $('*[for="editspec"] > small').html('Harap diisi!');
                $('*[for="editkategori"] > small').html('');
                $('*[for="edittahun"] > small').html('');
                $('*[for="editsize"] > small').html('');
                $('*[for="editmerk"] > small').html('');
                $('*[for="editsubsidi"] > small').html('');
                $('*[for="labeleditharga"] > small').html('');
            } else if (kategori == null) {
                $('*[for="editkode_produk"] > small').html('');
                $('*[for="editspec"] > small').html('');
                $('*[for="editkategori"] > small').html('Harap Pilih Salah satu Kategori!');
                $('*[for="edittahun"] > small').html('');
                $('*[for="editsize"] > small').html('');
                $('*[for="editmerk"] > small').html('');
                $('*[for="editsubsidi"] > small').html('');
                $('*[for="labeleditharga"] > small').html('');
            } else if (tahun == '') {
                $('*[for="editkode_produk"] > small').html('');
                $('*[for="editspec"] > small').html('');
                $('*[for="editkategori"] > small').html('');
                $('*[for="edittahun"] > small').html('Harap diisi!');
                $('*[for="editsize"] > small').html('');
                $('*[for="editmerk"] > small').html('');
                $('*[for="editsubsidi"] > small').html('');
                $('*[for="labeleditharga"] > small').html('');
            } else if (size == '') {
                $('*[for="editkode_produk"] > small').html('');
                $('*[for="editspec"] > small').html('');
                $('*[for="editkategori"] > small').html('');
                $('*[for="edittahun"] > small').html('');
                $('*[for="editsize"] > small').html('Harap diisi!');
                $('*[for="editmerk"] > small').html('');
                $('*[for="editsubsidi"] > small').html('');
                $('*[for="labeleditharga"] > small').html('');
            } else if (merk == '') {
                $('*[for="editkode_produk"] > small').html('');
                $('*[for="editspec"] > small').html('');
                $('*[for="editkategori"] > small').html('');
                $('*[for="edittahun"] > small').html('');
                $('*[for="editsize"] > small').html('');
                $('*[for="editmerk"] > small').html('Harap diisi!');
                $('*[for="editsubsidi"] > small').html('');
                $('*[for="labeleditharga"] > small').html('');
            } else if (subsidi == '' && subsidisebelum == '') {
                $('*[for="editkode_produk"] > small').html('');
                $('*[for="editspec"] > small').html('');
                $('*[for="editkategori"] > small').html('');
                $('*[for="edittahun"] > small').html('');
                $('*[for="editsize"] > small').html('');
                $('*[for="editmerk"] > small').html('');
                $('*[for="editsubsidi"] > small').html('Harap diisi!');
                $('*[for="labeleditharga"] > small').html('');
            } else {
                var editempty = false;
                valueeditharga.forEach(element => {
                    if (element == '' || element <= 0) {
                        editempty = true;
                    }
                });
                if (editempty == true) {
                    $('*[for="editkode_produk"] > small').html('');
                    $('*[for="editspec"] > small').html('');
                    $('*[for="editkategori"] > small').html('');
                    $('*[for="edittahun"] > small').html('');
                    $('*[for="editsize"] > small').html('');
                    $('*[for="editmerk"] > small').html('');
                    $('*[for="editsubsidi"] > small').html('');
                    $('*[for="labeleditharga"] > small').html('Harga Produk Tidak boleh Kosong !!');
                } else {
                    $('*[for="editkode_produk"] > small').html('');
                    $('*[for="editspec"] > small').html('');
                    $('*[for="editkategori"] > small').html('');
                    $('*[for="edittahun"] > small').html('');
                    $('*[for="editsize"] > small').html('');
                    $('*[for="editmerk"] > small').html('');
                    $('*[for="editsubsidi"] > small').html('');
                    $('*[for="labeleditharga"] > small').html('');
                    var tamptahun = tahun.split(' ')
                    var editYears = '';
                    var subsidifix = '';
                    var hargafix = '';
                    if (tamptahun.length == 1) {
                        editYears = tamptahun[0]
                    }
                    if (tamptahun.length == 2) {
                        editYears = tamptahun[1]

                    }
                    if (subsidi == '') {

                        var validasi1 = subsidisebelum.split(' ')
                        var rupiah = 'Rp.';
                        var validasi2 = $.inArray(rupiah, validasi1);
                        if (~validasi2) validasi1.splice(validasi2, 1);
                        var stringdot = validasi1.toString();
                        var subsidiend = '';
                        var validasi3 = stringdot.split('.')
                        for (let index = 0; index < validasi3.length; index++) {
                            subsidiend = subsidiend + validasi3[index];

                        }
                        var subsidifixed = parseInt(subsidiend)
                        subsidifix = subsidifixed
                    } else {
                        subsidifix = subsidi
                    }
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
                                url: '<?= base_url() ?>/MasterPameran/edit_dataHarga',
                                dataType: 'json',
                                method: 'POST',
                                data: {
                                    aksi: 'editharga',
                                    data: {
                                        id_product: id_product,
                                        id_pameran: id_pameran,
                                        kode_produk: kode_produk,
                                        spec: spec,
                                        kategori: kategori,
                                        tahun: editYears,
                                        size: size,
                                        merk: merk,
                                        subsidi: subsidifix
                                    }
                                },
                                success: function(data) {
                                    if (data.success == true) {
                                        $.ajax({
                                            url: '<?= base_url() ?>/MasterPameran/hapus_datahargagrading',
                                            dataType: 'json',
                                            method: 'POST',
                                            data: {
                                                aksi: 'tambahharga',
                                                data: {
                                                    id_product: id_product,
                                                }
                                            }
                                        });
                                        $.ajax({
                                            url: '<?= base_url() ?>/MasterPameran/tambah_datahargagrading',
                                            dataType: 'json',
                                            method: 'POST',
                                            data: {
                                                aksi: 'tambahharga',
                                                data: {
                                                    id_product: id_product,
                                                    valueharga: valueeditharga,
                                                }
                                            }
                                        });
                                        $('#editModalharga').modal('hide');
                                        toastr.success('Update Data Harga Succesfully')
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
        $('#tambahData').on('click', function() {
            var valueharga = [];
            var id_pameran = $('#id_pameran').val();
            var kode_produk = $('#kode_produk').val();
            var spec = $('#spec').val();
            var kategori = $('#kategori').val();
            var tahun = $('#thn').val();
            var size = $('#size').val();
            var merk = $('#merk').val();
            var subsidi = subsidilist;
            idharga.forEach(element => {
                var string = String(element)
                valueharga.push($('#' + string).val())
            });
            var harga = hargalist;
            if (kode_produk == '') {
                $('*[for="kode_produk"] > small').html('Harap diisi!');
                $('*[for="spec"] > small').html('');
                $('*[for="kategori"] > small').html('');
                $('*[for="tahun"] > small').html('');
                $('*[for="size"] > small').html('');
                $('*[for="merk"] > small').html('');
                $('*[for="subsidi"] > small').html('');
                $('*[for="labeltambahharga"] > small').html('');
            } else if (spec == '') {
                $('*[for="kode_produk"] > small').html('');
                $('*[for="spec"] > small').html('Harap diisi!');
                $('*[for="kategori"] > small').html('');
                $('*[for="tahun"] > small').html('');
                $('*[for="size"] > small').html('');
                $('*[for="merk"] > small').html('');
                $('*[for="subsidi"] > small').html('');
                $('*[for="labeltambahharga"] > small').html('');
            } else if (kategori == null) {
                $('*[for="kode_produk"] > small').html('');
                $('*[for="spec"] > small').html('');
                $('*[for="kategori"] > small').html('Harap Pilih Salah satu Kategori!');
                $('*[for="tahun"] > small').html('');
                $('*[for="size"] > small').html('');
                $('*[for="merk"] > small').html('');
                $('*[for="subsidi"] > small').html('');
                $('*[for="labeltambahharga"] > small').html('');
            } else if (tahun == '') {
                $('*[for="kode_produk"] > small').html('');
                $('*[for="spec"] > small').html('');
                $('*[for="kategori"] > small').html('');
                $('*[for="tahun"] > small').html('Harap diisi!');
                $('*[for="size"] > small').html('');
                $('*[for="merk"] > small').html('');
                $('*[for="subsidi"] > small').html('');
                $('*[for="labeltambahharga"] > small').html('');
            } else if (size == '') {
                $('*[for="kode_produk"] > small').html('');
                $('*[for="spec"] > small').html('');
                $('*[for="kategori"] > small').html('');
                $('*[for="tahun"] > small').html('');
                $('*[for="size"] > small').html('Harap diisi!');
                $('*[for="merk"] > small').html('');
                $('*[for="subsidi"] > small').html('');
                $('*[for="labeltambahharga"] > small').html('');
            } else if (merk == '') {
                $('*[for="kode_produk"] > small').html('');
                $('*[for="spec"] > small').html('');
                $('*[for="kategori"] > small').html('');
                $('*[for="tahun"] > small').html('');
                $('*[for="size"] > small').html('');
                $('*[for="merk"] > small').html('Harap diisi!');
                $('*[for="subsidi"] > small').html('');
                $('*[for="labeltambahharga"] > small').html('');
            } else if (subsidi == '') {
                $('*[for="kode_produk"] > small').html('');
                $('*[for="spec"] > small').html('');
                $('*[for="kategori"] > small').html('');
                $('*[for="tahun"] > small').html('');
                $('*[for="size"] > small').html('');
                $('*[for="merk"] > small').html('');
                $('*[for="subsidi"] > small').html('Harap diisi!');
                $('*[for="labeltambahharga"] > small').html('');
            } else {
                var empty = false;
                valueharga.forEach(element => {
                    if (element == '' || element <= 0) {
                        empty = true;
                    }
                });
                if (empty == true) {
                    $('*[for="kode_produk"] > small').html('');
                    $('*[for="spec"] > small').html('');
                    $('*[for="kategori"] > small').html('');
                    $('*[for="tahun"] > small').html('');
                    $('*[for="size"] > small').html('');
                    $('*[for="merk"] > small').html('');
                    $('*[for="subsidi"] > small').html('');
                    $('*[for="labeltambahharga"] > small').html('Harga Tidak Boleh Kosong');
                } else {
                    var tampkode = '';
                    $.ajax({
                        url: '<?= base_url() ?>/MasterPameran/cekdatakode',
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            aksi: 'tambahharga',
                            data: {
                                kode_produk: kode_produk,
                            }
                        },
                        success: function(data) {
                            if (data > 0) {
                                $('*[for="kode_produk"] > small').html('Kode Product Tidak Boleh sama');
                                $('*[for="spec"] > small').html('');
                                $('*[for="kategori"] > small').html('');
                                $('*[for="tahun"] > small').html('');
                                $('*[for="size"] > small').html('');
                                $('*[for="merk"] > small').html('');
                                $('*[for="subsidi"] > small').html('');
                                $('*[for="labeltambahharga"] > small').html('');
                            } else {
                                $('*[for="kode_produk"] > small').html('');
                                $('*[for="spec"] > small').html('');
                                $('*[for="kategori"] > small').html('');
                                $('*[for="tahun"] > small').html('');
                                $('*[for="size"] > small').html('');
                                $('*[for="merk"] > small').html('');
                                $('*[for="subsidi"] > small').html('');
                                $('*[for="labeltambahharga"] > small').html('');
                                var tamptahun = tahun.split(' ')
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
                                            url: '<?= base_url() ?>/MasterPameran/tambah_data_harga',
                                            dataType: 'json',
                                            method: 'POST',
                                            data: {
                                                aksi: 'tambahharga',
                                                data: {
                                                    id_pameran: id_pameran,
                                                    kode_produk: kode_produk,
                                                    spec: spec,
                                                    kategori: kategori,
                                                    tahun: tamptahun[1],
                                                    size: size,
                                                    merk: merk,
                                                    subsidi: subsidi,
                                                    harga: harga
                                                }
                                            },
                                            success: function(data) {
                                                if (data.success == true) {
                                                    $.ajax({
                                                        url: '<?= base_url() ?>/MasterPameran/tambah_datahargagrading',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: {
                                                            aksi: 'tambahharga',
                                                            data: {
                                                                id_product: data.data[0]['id_product'],
                                                                valueharga: valueharga,
                                                            }
                                                        }
                                                    });
                                                    $('#modaltambahharga').modal('hide');
                                                    toastr.success('Create Data Harga Succesfully')
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
                    })


                }




            }
        });
        $('body').on('click', '.tomboldeleteall', function() {
            var id_pameran = $('#id_pameran').val();
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
                        url: '<?= base_url() ?>/MasterPameran/HapusdataHargaall',
                        type: "post",
                        dataType: "json",
                        data: {
                            id_pameran: id_pameran,

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
        $('body').on('click', '.export', function() {
            var id_pameran = $('#id_pameran').val();
            window.location.href = '<?= base_url() ?>/MasterPameran/exportDataProduct/' + id_pameran
            return false
        });
    });
</script>
<?= $this->endSection('content_js') ?>