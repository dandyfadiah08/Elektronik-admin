<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="background-color: white; color:black">
    <div class="content">
        <div class="container pt-4">
            <div class="title-text mb-3">Master Kuisioner</div>
            <section class="section">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-select form-control " id="filterkategori" aria-label="Default select example">
                                <option value="ALL">All </option>
                                <?php foreach ($page->data as $key => $value) : ?>
                                    <option value="<?= $value['id_kategori'] ?>"><?= $value['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#modalKuisioner" id="tombolTambah"><i class="fa fa-plus"></i>Tambah Kuisioner
                            </button>
                        </div>
                    </div>
                    <table id="tabelkuisioner" class="table table-bordered table-striped table-hover table-bordered" style="font-size: 12px;">

                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Urutan</th>
                                <th>Kuisioner</th>
                                <th>Kategori</th>
                                <th>List Kuisioner</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="listModalKuisioner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <label for="listnotekuisioner">
                                    <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">
                                        List Quisioner
                                    </h5> <small class="text-warning"></small>
                                </label>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>

                            </div>
                            <div class="modal-body row " id="listview">


                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="editModalKuisioner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="false" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Kuisioner</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="editlistkategori">Kategori <small class="text-warning"></small></label>
                                        <select style="background-color:azure; color:black;" class="form-select form-control" id="filtereditkategori" aria-label="Default select example">
                                            <option value="" disabled selected>Kategori </option>
                                            <?php foreach ($page->data as $key => $value) : ?>
                                                <option value="<?= $value['id_kategori'] ?>"><?= $value['nama_kategori'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" id="id_kuisioner">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">Number(Level Kuisioner)</label>
                                        <label for="editnumberlevel"><small class="text-warning"></small></label>
                                        <input type="number" class="form-control" style="background-color: aliceblue; color:#321" id="editnumberlevel" placeholder="Isikan Number" autocomplete="off" name="kuisioner" value="">
                                        <input type="hidden" id="id_kuisioner">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="editkuisioner">Kuisioner <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="editkuisioner" placeholder="Isikan Kuisioner" name="editkuisioner">
                                        <input type="hidden" id="id_editkuisioner">
                                    </div>
                                    <div class="col-md-12 mt-2" id="listedittext">
                                        <label for="listeditkuisioner">List Kuisioner <small class="text-warning "></small></label>
                                        <a href="#" style="color:aliceblue;" class="addeditlist float-right mr-4 ">&plus;</a>

                                        <div class="inpedit-group mt-2">

                                        </div>
                                        <!-- <input type="text" class="form-control mt-2" style="background-color: aliceblue; color:#321" id="kuisioner" placeholder="Isikan List Kuisioner" autocomplete="off" name="listkuisioner" value=""> -->
                                        <input type="hidden" id="id_editkuisioner">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" id="edit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalKuisioner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="color: aliceblue;" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Tambah Kuisioner</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Kategori</label>
                                        <label for="filtergrading"><small class="text-warning"></small></label>
                                        <select style="background-color:azure; color:black;" class="form-select form-control " id="filtergrading" aria-label="Default select example">
                                            <option value="" disabled selected>Kategori </option>
                                            <?php foreach ($page->data as $key => $value) : ?>
                                                <option value="<?= $value['id_kategori'] ?>"><?= $value['nama_kategori'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" id="id_kuisioner">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">Number(Level Kuisioner)</label>
                                        <label for="numberlevel"><small class="text-warning"></small></label>
                                        <input type="number" class="form-control inputnumber" style="background-color: aliceblue; color:#321" id="numberlevel" placeholder="Isikan Number" autocomplete="off" name="kuisioner" value="">
                                        <input type="hidden" id="id_kuisioner">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="kuisioner">Kuisioner <small class="text-warning"></small></label>
                                        <input type="text" class="form-control" style="background-color: aliceblue; color:#321" id="tambahkuisioner" placeholder="Isikan Kuisioner" autocomplete="off" name="kuisioner" value="">
                                        <input type="hidden" id="id_kuisioner">
                                    </div>
                                    <div class="col-md-12 mt-2" id="listtext">
                                        <label for="listkuisioner">List Kuisioner <small class="text-warning "></small></label>
                                        <a href="#" style="color:aliceblue;" class="add float-right mr-4 ">&plus;</a>
                                        <hr style="background-color: white;">
                                        <div class="inp-group mt-2">

                                        </div>
                                        <!-- <input type="text" class="form-control mt-2" style="background-color: aliceblue; color:#321" id="kuisioner" placeholder="Isikan List Kuisioner" autocomplete="off" name="listkuisioner" value=""> -->
                                        <input type="hidden" id="id_kuisioner">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="product_id" class="product_id">
                                <button type="button" class="btn btn-danger" id="closecreate" data-dismiss="modal">Batal</button>
                                <button type="submit" style="color: aliceblue;" id="tambahKuisioner" class="btn btn-warning"><i class="fas fa-check"></i>&nbsp;Tambah Kuisioner</button>
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
    .list-group {
        width: 390px !important;

    }

    .list-group-item {
        margin-top: 10px;
        border-radius: none;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        background: linear-gradient(to top right, #6600ff 0%, #9999ff 100%);

    }

    .card-big-shadow {
        max-width: 320px;
        position: relative;
    }

    .coloured-cards .card {
        margin-top: 30px;
    }

    .card[data-radius="none"] {
        border-radius: 0px;
    }

    .card {
        border-radius: 8px;
        box-shadow: 0 2px 2px rgba(204, 197, 185, 0.5);
        background-color: #FFFFFF;
        color: #252422;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }


    .card[data-background="image"] .title,
    .card[data-background="image"] .stats,
    .card[data-background="image"] .category,
    .card[data-background="image"] .description,
    .card[data-background="image"] .content,
    .card[data-background="image"] .card-footer,
    .card[data-background="image"] small,
    .card[data-background="image"] .content a,
    .card[data-background="color"] .title,
    .card[data-background="color"] .stats,
    .card[data-background="color"] .category,
    .card[data-background="color"] .description,
    .card[data-background="color"] .content,
    .card[data-background="color"] .card-footer,
    .card[data-background="color"] small,
    .card[data-background="color"] .content a {
        color: #FFFFFF;
    }

    .card.card-just-text .content {
        padding: 50px 65px;
        text-align: center;
    }

    .card .content {
        padding: 20px 20px 10px 20px;
    }

    .card[data-color="blue"] .category {
        color: #7a9e9f;
    }

    .card .category,
    .card .label {
        font-size: 14px;
        margin-bottom: 0px;
    }

    .card-big-shadow:before {
        background-image: url("http://static.tumblr.com/i21wc39/coTmrkw40/shadow.png");
        background-position: center bottom;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        bottom: -12%;
        content: "";
        display: block;
        left: -12%;
        position: absolute;
        right: 0;
        top: 0;
        z-index: 0;
    }

    h4,
    .h4 {
        font-size: 1.5em;
        font-weight: 600;
        line-height: 1.2em;
    }

    h6,
    .h6 {
        font-size: 0.9em;
        font-weight: 600;
        text-transform: uppercase;
    }

    .card .description {
        font-size: 16px;
        color: #66615b;
    }

    .content-card {
        margin-top: 30px;
    }

    a:hover,
    a:focus {
        text-decoration: none;
    }

    /*======== COLORS ===========*/
    .card[data-color="blue"] {
        background: #b8d8d8;
    }

    .card[data-color="blue"] .description {
        color: #506568;
    }

    .card[data-color="green"] {
        background: #d5e5a3;
    }

    .card[data-color="green"] .description {
        color: #60773d;
    }

    .card[data-color="green"] .category {
        color: #92ac56;
    }

    .card[data-color="yellow"] {
        background: #ffe28c;
    }

    .card[data-color="yellow"] .description {
        color: #b25825;
    }

    .card[data-color="yellow"] .category {
        color: #d88715;
    }

    .card[data-color="brown"] {
        background: #d6c1ab;
    }

    .card[data-color="brown"] .description {
        color: #75442e;
    }

    .card[data-color="brown"] .category {
        color: #a47e65;
    }

    .card[data-color="purple"] {
        background: #baa9ba;
    }

    .card[data-color="purple"] .description {
        color: #3a283d;
    }

    .card[data-color="purple"] .category {
        color: #5a283d;
    }

    .card[data-color="orange"] {
        background: #ff8f5e;
    }

    .card[data-color="orange"] .description {
        color: #772510;
    }

    .card[data-color="orange"] .category {
        color: #e95e37;
    }

    .check {
        opacity: 0;
        transition: all 0.6s ease-in-out;
    }

    .list-group-item:hover .check {
        opacity: 1;

    }

    .about span {
        font-size: 12px;
        margin-right: 10px;

    }

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

    .add {
        text-decoration: none;
        display: inline-block;
        width: 30px;
        height: 30px;
        background: #8bc34a;
        font-size: 2rem;
        font-weight: bold;
        color: aqua;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .addeditlist {
        text-decoration: none;
        display: inline-block;
        width: 30px;
        height: 30px;
        background: #8bc34a;
        font-size: 2rem;
        font-weight: bold;
        color: aqua;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .addlistgambar {
        text-decoration: none;
        display: inline-block;
        width: 30px;
        height: 30px;
        background: #8bc34a;
        font-size: 2rem;
        font-weight: bold;
        color: aqua;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .flex {
        display: flex;
        gap: 1.5em;
    }

    #tabelkuisioner {
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>/assets/adminlte3/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>/assets/adminlte3/plugins/toastr/toastr.min.js"></script>
<script>
    const path = '/MasterKuisioner'
    $(document).ready(function() {
        var validasichoose = false;
        var validasifile = false;
        var validtext = 'text';
        $('#numberlevel').val(1)
        const addBtn = document.querySelector(".add");
        const addeditBtn = document.querySelector(".addeditlist");
        const input = document.querySelector(".inp-group");
        const inputedit = document.querySelector(".inpedit-group");
        const listcoba = document.querySelector(".listview");
        var coba = "gema";
        var tamidgambar = '';
        var edittamidgambar = '';
        var gambarclass = "foto";
        var editgambarclass = "editfoto";
        var cobaedit = "listedit";
        var filename = 'file';
        var keteranganname = 'keterangan';
        var labelurlgambar = "url"
        var jumlah = 0;
        var jumlahgambar = 0;
        var editjumlahgambar = 0;
        var arrid = [];
        var jumlahedit = 0;
        var arridedit = [];
        var arrgambar = [];
        var iddiv = [];
        var classnamefoto = [];
        var editiddiv = [];
        var editclassnamefoto = [];
        var editClassgambar = [];
        var arraygambar = [];
        var editarraygambar = [];
        var indexgambar = [];
        var editindexgambar = [];
        var indexketerangan = [];
        var editindexketerangan = [];
        var batasarray = [];
        var editbatasarray = [];
        var gambarketerangan = [];
        var editgambarketerangan = [];
        var fixededitgambar = [];
        var namediv = '';
        var editnamedive = '';

        $('body').on('click', '.listModalKuisioner', function() {
            var id = $(this).data('id_mkuisioner');
            var kuisioner = $(this).data('listmasterkuisioner');
            $('*[for="listnotekuisioner"] > small').html(kuisioner);
            document.getElementById("listview").innerHTML = " ";
            $.ajax({
                type: "POST",
                url: '<?= base_url() ?>/MasterKuisioner/listkuisioner',
                dataType: "JSON",
                data: {
                    id: id
                },
                cache: false,
                success: function(data) {
                    if (data == '') {

                        alert('Maaf List Kuisioner belum ada')
                    } else {


                        listviews(data, kuisioner);
                    }

                }
            });
            return false;
        });
        $('body').on('click', '.editModalKuisioner', function() {
            var id = $(this).data('id_mkuisioner');
            var kuisioner = $(this).data('kuisioner');
            var number = $(this).data('number');
            var id_kategori = $(this).data('id_kategori');
            $('#id_editkuisioner').val(id);
            $('#editkuisioner').val(kuisioner);
            $('#filtereditkategori').val(id_kategori)
            $('#editnumberlevel').val(number)
            var length_list = arridedit.length
            if (length_list > 0) {
                for (let index = 0; index < length_list; index++) {
                    const element = document.getElementById(arridedit[index]);
                    element.remove();
                }
            }
            $.ajax({
                type: "POST",
                url: '<?= base_url() ?>/MasterKuisioner/listkuisioner',
                dataType: "JSON",
                data: {
                    id: id
                },
                cache: false,
                success: function(data) {
                    if (data == '') {

                        alert('Maaf List Kuisioner belum ada')
                    } else {
                        listeditEx(data)
                    }

                }
            });
            $("#listedittext").show("slow", function() {
                // Animation complete.
            });
            $("#listeditfile").hide("slow", function() {
                // Animation complete.
            });
            return false;
        });
        let datatable = $("#tabelkuisioner").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '<?= base_url() ?>/MasterKuisioner/load_data',
                type: "post",
                data: function(d) {
                    d.status = $('#filter-status option:selected').val();
                    d.merchant = $('#filter-merchant option:selected').val();
                    d.date = $('#filter-date').val();
                    d.kategori = $('#filterkategori').val();
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
        $('#filterkategori').on('change', function() {
            var status = $('#filterkategori option:selected').val();
            datatable.ajax.reload();
            // $('#btnExport').attr('href', 'finance-export.php?id_mu='+idMU+'&status='+status);
        });
        $('body').on('click', '.tombolHapus', function() {
            var id_mkuisioner = $(this).data('id_mkuisioner');
            var kuesioner = $(this).data('kuisioner');
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
                        url: `${base_url}${path}/Hapuskuisioner`,
                        type: "post",
                        dataType: "json",
                        data: {
                            id_mkuisioner: id_mkuisioner,
                            kuesioner: kuesioner,

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
            var id_mkuisioner = $('#id_editkuisioner').val();
            var number = $('#editnumberlevel').val();
            var kuesioner = $('#editkuisioner').val();
            var kategori = $('#filtereditkategori').val();
            var listtextcek = '';
            var listfilecek = '';
            var listquisioner = $("input[name='jeniseditlist']:checked").val();
            var filevalue = $('#editfilelist').val();
            if (kuesioner == '') {
                $('*[for="editlistkategori"] > small').html('');
                $('*[for="editkuisioner"] > small').html('Harap diisi');
                $('*[for="listeditkuisioner"] > small').html('');
            } else if (kategori == null) {
                $('*[for="editlistkategori"] > small').html('Harap pilih salah satu kategori');
                $('*[for="editkuisioner"] > small').html('');
                $('*[for="listeditkuisioner"] > small').html('');
            } else if (number <= 0) {
                $('*[for="editlistkategori"] > small').html('');
                $('*[for="editkuisioner"] > small').html('');
                $('*[for="editnumberlevel"] > small').html('Harap Isi Number dengan benar');
                $('*[for="listeditkuisioner"] > small').html('');
            } else {

                var arraylist = [];
                var fixlist = [];
                arridedit.forEach(element => {
                    arraylist.push($('#' + element).val())
                });
                arraylist.forEach(element => {
                    if (element != '') {
                        fixlist.push(element)
                    }
                });
                var lengtharray = fixlist.length;

                if (lengtharray <= 0) {
                    $('*[for="editkuisioner"] > small').html('');
                    $('*[for="editlistkategori"] > small').html('');
                    $('*[for="listkuisionereditfile"] > small').html('');
                    $('*[for="listeditkuisioner"] > small').html('Harap Isi List Kuisioner Minimal 1 option');
                } else {
                    $.ajax({
                        url: '<?= base_url() ?>/MasterKuisioner/detaillevel',
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            aksi: 'tambahKuisioner',
                            data: {
                                kategori: kategori,
                                numberlevel: number,
                            }
                        },
                        success: function(datalevel) {
                            $('*[for="editkuisioner"] > small').html('');
                            $('*[for="editlistkategori"] > small').html('');
                            $('*[for="editnumberlevel"] > small').html('');
                            $('*[for="listkuisionereditfile"] > small').html('');
                            $('*[for="listeditkuisioner"] > small').html('');
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
                                        url: `${base_url}${path}/editkuisioner`,
                                        type: "post",
                                        dataType: "json",
                                        data: {
                                            id_mkuisioner: id_mkuisioner,
                                            kuesioner: kuesioner,
                                            kategori: kategori,
                                            number: $('#editnumberlevel').val(),
                                            listtextcek: fixlist,
                                            listfilecek: filevalue,

                                        },

                                        success: function(data) {
                                            if (data.success == true) {
                                                editClassgambar.forEach(gambar => {
                                                    $.ajax({
                                                        url: '<?= base_url() ?>/MasterKuisioner/hapusgambar',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: {
                                                            aksi: 'hapusgambar',
                                                            data: {
                                                                id: $('#' + gambar['id_listkuisioner']).val(),
                                                            }
                                                        },
                                                    })
                                                })
                                                editgambarketerangan.forEach(element => {
                                                    if ($('#' + element['gambar']).val() == '') {
                                                        editClassgambar.forEach(data => {
                                                            if (element['id_listkuisioner'] == data['id_listkuisioner'] && element['status'] == 'view') {
                                                                $.ajax({
                                                                    url: '<?= base_url() ?>/MasterKuisioner/detaillistkuisioner',
                                                                    dataType: 'json',
                                                                    method: 'POST',
                                                                    data: {
                                                                        aksi: 'hapusgambar',
                                                                        data: {
                                                                            id: $('#' + element['id_listkuisioner']).val(),
                                                                        }
                                                                    },
                                                                    success: function(datagambar) {
                                                                        var id = datagambar[0]['id_listkuisioner']
                                                                        $.ajax({
                                                                            url: '<?= base_url() ?>/MasterKuisioner/updated_listgambar',
                                                                            dataType: 'json',
                                                                            method: 'POST',
                                                                            data: {
                                                                                aksi: 'hapusgambar',
                                                                                data: {
                                                                                    id: id,
                                                                                    gambar: data['gambar'],
                                                                                    keterangan: $('#' + element['keterangan']).val(),
                                                                                }
                                                                            },
                                                                            success: function(data) {
                                                                                console.log(data)
                                                                            }

                                                                        })
                                                                    }
                                                                })
                                                            }
                                                        });
                                                    } else {
                                                        $.ajax({
                                                            url: '<?= base_url() ?>/MasterKuisioner/detaillistkuisioner',
                                                            dataType: 'json',
                                                            method: 'POST',
                                                            data: {
                                                                aksi: 'hapusgambar',
                                                                data: {
                                                                    id: $('#' + element['id_listkuisioner']).val(),
                                                                }
                                                            },
                                                            success: function(datagambar) {
                                                                var id = datagambar[0]['id_listkuisioner']
                                                                var photo = document.getElementById(element['gambar']);
                                                                var file = photo.files[0];
                                                                data = new FormData();
                                                                data.append('file', file);
                                                                data.append('idlist', id);
                                                                data.append('keterangan', $('#' + element['keterangan']).val())
                                                                $.ajax({
                                                                    url: '<?= base_url() ?>/MasterKuisioner/updated_listgambarfile',
                                                                    dataType: 'json',
                                                                    data: data,
                                                                    enctype: 'multipart/form-data',
                                                                    processData: false,
                                                                    contentType: false,
                                                                    type: 'POST',
                                                                })
                                                            }
                                                        })

                                                    }
                                                });
                                                $('#editModalKuisioner').modal('hide');
                                                alert("Updated Data Kuisioner Succesfully")
                                                toastr.success('Update Data Kategori Succesfully')
                                                datatable.ajax.reload();
                                                window.location.reload()

                                            } else {
                                                alert("SUCCESS: " + data.status);

                                            }
                                            // console.log(data);

                                        }
                                    })

                                }
                            })
                        }
                    })

                }

            }
        });

        function addInput() {
            jumlah = jumlah + 1;
            var namaid = coba + jumlah;
            const name = document.createElement("input");
            name.type = "text";
            name.className = " form-control mt-2";
            name.id = namaid;
            name.style = "background-color: aliceblue; color:#321"
            name.placeholder = "List Kuisioner";
            arrid.push(namaid)
            //paragraf
            const node = document.createTextNode("Foto List Kuisioner(Optional)");
            const flex = document.createElement("div");
            flex.className = "flext";
            const para = document.createElement("p");
            const bold = document.createElement("b");
            para.className = "flext";
            para.className = "mt-4"
            //button add 
            const listbutton = document.createTextNode('+');
            const btnhref = document.createElement("button");
            var gambarid = gambarclass + jumlah
            btnhref.id = gambarid;
            btnhref.className = gambarid + "button" + "  mr-4 mb-2 mt-3";
            btnhref.style = "text-decoration: none;display: inline-block; width: 30px;height: 30px;background: #8bc34a;font-size: 2rem;font-weight: bold;color: aqua;display: flex;justify-content: center;align-items: center;"
            // hr
            const batas = document.createElement("hr");
            batas.style = "background-color: white; mb-2"
            batas.id = "batas" + gambarid
            batasarray.push(batas.id)
            //div2
            const flex2 = document.createElement("div");
            flex2.className = "flext2 " + "gambar" + gambarid;
            flex2.id = gambarid;
            iddiv.push(flex2.id)
            classnamefoto.push("gambar" + gambarid)
            input.appendChild(batas);
            input.appendChild(flex);
            input.appendChild(flex2);
            input.appendChild(batas);
            flex.appendChild(name);
            flex2.appendChild(para)
            flex2.appendChild(btnhref)
            bold.appendChild(node);
            para.appendChild(bold);
            btnhref.appendChild(listbutton);
            const addBtn2 = document.querySelector("." + gambarid + "button");
            const input2 = document.querySelector("." + "gambar" + gambarid);
            tamidgambar = "gambar" + gambarid;

            addBtn2.addEventListener("click", () => {
                addgambar(btnhref.id, name.id);
            }, false);
        }

        function addgambar(id, id_listkuisioner) {
            iddiv.forEach(element => {
                if (id == element) {
                    namediv = "gambar" + id
                }
            });
            jumlahgambar = jumlahgambar + 1;
            var namafile = filename + jumlahgambar;
            var namaketerangan = keteranganname + jumlahgambar;
            const input2 = document.querySelector("." + namediv);
            const file = document.createElement("input");
            file.type = "file";
            file.accept = "image/*";
            file.className = " form-control mt-2";
            file.name = namafile;
            file.id = namafile;
            indexgambar.push(file.id)
            file.style = "background-color: aliceblue; color:#321"
            file.placeholder = "Unggah Foto";
            //katerangan
            const keterangan = document.createElement("input");
            keterangan.type = "text";
            keterangan.id = namaketerangan;
            keterangan.className = " form-control mt-2";
            keterangan.style = "background-color: aliceblue; color:#321"
            keterangan.placeholder = "Tambah Keterangan";
            indexketerangan.push(keterangan.id)
            gambarketerangan.push({
                'id_listkuisioner': id_listkuisioner,
                'gambar': file.id,
                'keterangan': keterangan.id
            })
            arraygambar.push({
                'id_listkuisioner': id_listkuisioner,
                'gambar': file.name,
                'keterangan': keterangan.id
            })
            input2.appendChild(file)
            input2.appendChild(keterangan)
        }

        function addeditInput() {
            jumlah = jumlahedit++;
            var namaid = cobaedit + jumlah;
            const name = document.createElement("input");
            name.type = "text";
            name.className = " form-control mt-2";
            name.id = namaid;
            name.style = "background-color: aliceblue; color:#321"
            name.placeholder = "List Kuisioner";
            arridedit.push(namaid)
            //paragraf
            const editnode = document.createTextNode("Foto List Kuisioner(Optional)");
            const editflex = document.createElement("div");
            editflex.className = "flext";
            const editpara = document.createElement("p");
            const editbold = document.createElement("b");
            editpara.className = "flext";
            editpara.className = "mt-4";
            //button add 
            const editlistbutton = document.createTextNode('+');
            const editbtnhref = document.createElement("button");
            var editgambarid = editgambarclass + jumlah
            editbtnhref.id = editgambarid;
            editbtnhref.className = editgambarid + "button" + "  mr-4 mb-2 mt-3";
            editbtnhref.style = "text-decoration: none;display: inline-block; width: 30px;height: 30px;background: #8bc34a;font-size: 2rem;font-weight: bold;color: aqua;display: flex;justify-content: center;align-items: center;"
            const flex = document.createElement("div");
            flex.className = "flext";
            // hr
            const editbatas = document.createElement("hr");
            editbatas.style = "background-color: white; mb-2"
            editbatas.id = "batas" + editgambarid
            editbatasarray.push(editbatas.id)
            //div2
            const editflex2 = document.createElement("div");
            editflex2.className = "flext2 " + "gambar" + editgambarid;
            editflex2.id = editgambarid;
            editiddiv.push(editflex2.id)
            editclassnamefoto.push("gambar" + editgambarid)
            inputedit.appendChild(editbatas);
            inputedit.appendChild(flex);
            inputedit.appendChild(editflex2);
            flex.appendChild(name);
            editflex2.appendChild(editpara)
            editflex2.appendChild(editbtnhref)
            editbold.appendChild(editnode);
            editpara.appendChild(editbold);
            editbtnhref.appendChild(editlistbutton);
            const editaddBtn2 = document.querySelector("." + editgambarid + "button");
            const editinput2 = document.querySelector("." + "gambar" + editgambarid);
            edittamidgambar = "gambar" + editgambarid;

            editaddBtn2.addEventListener("click", () => {
                addeditgambar(editbtnhref.id, name.id);
            }, false);
        }

        function validateForm() {

            var z = document.forms["myForm"]["num"].value;
            if (!z.match(/^\d+/)) {
                alert("Please only enter numeric characters only for your Age! (Allowed input:0-9)")
            }
        }

        function addshowInput(list) {
            jumlah = jumlahedit++;
            var namaid = cobaedit + jumlah;
            const name = document.createElement("input");
            name.type = "text";
            name.className = " form-control mt-2";
            name.id = namaid;
            name.value = list;
            name.style = "background-color: aliceblue; color:#321"
            name.placeholder = "List Kuisioner";
            arridedit.push(namaid)
            //paragraf
            const editnode = document.createTextNode("Foto List Kuisioner(Optional)");
            const editflex = document.createElement("div");
            editflex.className = "flext";
            const editpara = document.createElement("p");
            const editbold = document.createElement("b");
            editpara.className = "flext";
            editpara.className = "mt-4";
            //button add 
            const editlistbutton = document.createTextNode('+');
            const editbtnhref = document.createElement("button");
            var editgambarid = editgambarclass + jumlah
            editbtnhref.id = editgambarid;
            editbtnhref.className = editgambarid + "button" + "  mr-4 mb-2 mt-3";
            editbtnhref.style = "text-decoration: none;display: inline-block; width: 30px;height: 30px;background: #8bc34a;font-size: 2rem;font-weight: bold;color: aqua;display: flex;justify-content: center;align-items: center;"
            const flex = document.createElement("div");
            flex.className = "flext";
            // hr
            const editbatas = document.createElement("hr");
            editbatas.style = "background-color: white; mb-2"
            editbatas.id = "batas" + editgambarid
            editbatasarray.push(editbatas.id)
            //div2
            const editflex2 = document.createElement("div");
            editflex2.className = "flext2 " + "gambar" + editgambarid;
            editflex2.id = editgambarid;
            editiddiv.push(editflex2.id)
            editclassnamefoto.push("gambar" + editgambarid)
            inputedit.appendChild(editbatas);
            inputedit.appendChild(flex);
            inputedit.appendChild(editflex2);
            flex.appendChild(name);
            editflex2.appendChild(editpara)
            editflex2.appendChild(editbtnhref)
            editbold.appendChild(editnode);
            editpara.appendChild(editbold);
            editbtnhref.appendChild(editlistbutton);
            const editaddBtn2 = document.querySelector("." + editgambarid + "button");
            const editinput2 = document.querySelector("." + "gambar" + editgambarid);
            edittamidgambar = "gambar" + editgambarid;
            $.ajax({
                url: '<?= base_url() ?>/MasterKuisioner/detail_datagambar',
                dataType: 'json',
                method: 'POST',
                data: {
                    aksi: 'tambahKuisioner',
                    data: {
                        datalistgambar: list,
                    }
                },
                success: function(data) {
                    data.forEach(element => {
                        $.ajax({
                            url: '<?= base_url() ?>/MasterKuisioner/view_datagambar',
                            dataType: 'json',
                            method: 'POST',
                            data: {
                                aksi: 'tambahKuisioner',
                                data: {
                                    datagambar: element['id_listkuisioner'],
                                }
                            },
                            success: function(data) {
                                var lengthdata = data.length
                                if (lengthdata > 0) {
                                    data.forEach(element => {
                                        viewaddeditgambar(editbtnhref.id, name.id, element['gambar'], element['keterangan'])
                                    });
                                }
                            }
                        })
                    });

                }
            })
            editaddBtn2.addEventListener("click", () => {
                addeditgambar(editbtnhref.id, name.id);
            }, false);
        }

        function viewaddeditgambar(id, id_listkuisioner, gambar, listketerangan) {

            editiddiv.forEach(element => {
                if (id == element) {
                    editnamediv = "gambar" + id
                }
            });
            editjumlahgambar = editjumlahgambar + 1;
            labelurlgambar = labelurlgambar + 1
            var namafile = filename + editjumlahgambar;
            var namaketerangan = keteranganname + editjumlahgambar;
            const input2 = document.querySelector("." + editnamediv);
            const imagefile = document.createElement("img")
            imagefile.src = gambar;
            const file = document.createElement("input");
            const spanlabel = document.createElement("span");
            spanlabel.id = labelurlgambar
            file.type = "file";
            file.accept = "image/*";
            // file.value = gambar;
            file.className = " form-control mt-2";
            file.name = namafile;
            var arraylistgambar = [];
            arraygambar = gambar.split("/");
            var namefile2 = "Nama alamat Gambar:" + " " + arraygambar[6];
            spanlabel.innerText = namefile2
            file.id = namafile;
            editindexgambar.push(file.id)
            file.style = "background-color: aliceblue; color:aliceblue"
            file.placeholder = "Unggah Foto";
            //katerangan
            const keterangan = document.createElement("input");
            keterangan.type = "text";
            keterangan.id = namaketerangan;
            keterangan.value = listketerangan
            keterangan.className = " form-control mt-2 mb-5";
            keterangan.style = "background-color: aliceblue; color:#321"
            keterangan.placeholder = "Tambah Keterangan";
            editindexketerangan.push(keterangan.id)
            editClassgambar.push({
                'id_listkuisioner': id_listkuisioner,
                'gambar': gambar,
                'keterangan': keterangan.id
            })
            editgambarketerangan.push({
                'id_listkuisioner': id_listkuisioner,
                'gambar': file.id,
                'keterangan': keterangan.id,
                'status': 'view',
            })
            editarraygambar.push({
                'id_listkuisioner': id_listkuisioner,
                'gambar': file.name,
                'keterangan': keterangan.id,
                'status': 'view',
            })
            input2.appendChild(imagefile)
            input2.appendChild(spanlabel)
            input2.appendChild(file)
            input2.appendChild(keterangan)
            file.addEventListener("click", () => {
                gambarurl(file.id, spanlabel.id);
            }, false);
            // var tam = ''
            // $("input[type='file']").change(function() {
            //     tam = this.value.replace(/C:\\fakepath\\/i, '')
            //     $('#' + spanlabel.id).text('Nama Alamat Gambar:' + " " + tam)
            // })
        }

        function addeditgambar(id, editid_listkuisioner) {
            editiddiv.forEach(element => {
                if (id == element) {
                    editnamediv = "gambar" + id
                }
            });

            editjumlahgambar = editjumlahgambar + 1;
            var namafile = filename + editjumlahgambar;
            var namaketerangan = keteranganname + editjumlahgambar;
            const input2 = document.querySelector("." + editnamediv);
            const file = document.createElement("input");
            file.type = "file";
            file.accept = "image/*";
            file.className = " form-control mt-2";
            file.name = namafile;
            file.id = namafile;
            editindexgambar.push(file.id)
            file.style = "background-color: aliceblue; color:#321"
            file.placeholder = "Unggah Foto";
            //katerangan
            const keterangan = document.createElement("input");
            keterangan.type = "text";
            keterangan.id = namaketerangan;
            keterangan.className = " form-control mt-2";
            keterangan.style = "background-color: aliceblue; color:#321"
            keterangan.placeholder = "Tambah Keterangan";
            editindexketerangan.push(keterangan.id)
            editgambarketerangan.push({
                'id_listkuisioner': editid_listkuisioner,
                'gambar': file.id,
                'keterangan': keterangan.id,
                'status': 'list',
            })
            editarraygambar.push({
                'id_listkuisioner': editid_listkuisioner,
                'gambar': file.name,
                'keterangan': keterangan.id,
                'status': 'list',
            })
            input2.appendChild(file)
            input2.appendChild(keterangan)
        }

        function gambarurl(file, id) {
            $('#' + file).change(function() {
                tam = this.value.replace(/C:\\fakepath\\/i, '')
                $('#' + id).text('Nama Alamat Gambar:' + " " + tam)
            })
        }

        function removeInput() {

            inputedit.remove();
        }

        function listviews(data) {
            var uigambar = '';
            data.forEach(element => {
                $.ajax({
                    url: '<?= base_url() ?>/MasterKuisioner/detaillistgambar',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        aksi: 'tambahKuisioner',
                        data: {
                            idlist: element.id_listkuisioner,
                        }
                    },
                    success: function(datalist) {
                        var lenth = datalist.length
                        if (lenth > 0) {
                            for (let i = 0; i < lenth; i++) {
                                uigambar = uigambar + "<img class='img-fluid' src='" + datalist[i]['gambar'] + "' height='512' alt=''>" + "<p style='color:black' class='mt-2'>  Keterangan Gambar </p>" + "<p style='color:black'> <b>" + datalist[i]['keterangan'] + "</b></p>" + "<hr style='background-color:black;'>";
                            }
                        }
                        document.getElementById("listview").innerHTML +=
                            "<div  class='col-md-4 col-sm-6 content-card'>" + "<div class='card-big-shadow'>" + "<div class='card' data-background='color' data-color='yellow' data-radius='none'>" + "<div class='content'>" + "<p class='description'>List Option: " + element.list + "</p>" + "<hr style='background-color:black;'>" + uigambar + "</div>" + "</div>" + "</div>" + "</div>";
                    }
                })


            });
            $('#listModalKuisioner').modal('show');
        }

        function listeditEx(data) {

            data.forEach(element => {
                var data = element.list
                addshowInput(data);
            });
            $('#editModalKuisioner').modal('show');
        }
        addBtn.addEventListener("click", addInput);
        addeditBtn.addEventListener("click", addeditInput);
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
        $('#tambahKuisioner').on('click', function() {
            var filtergrading = $('#filtergrading').val();
            var kuisioner = $('#tambahkuisioner').val();
            var numberlevel = $('#numberlevel').val()
            var arraylist = [];
            var fixlist = [];
            var chossefile = '';
            arrid.forEach(element => {
                arraylist.push($('#' + element).val())
            });
            arraylist.forEach(element => {
                if (element != '') {
                    fixlist.push(element)
                }
            });
            var lengtharray = fixlist.length;
            if (filtergrading == null) {
                $('*[for="filtergrading"] > small').html('Harap pilih jenis grading!!');
                $('*[for="kuisioner"] > small').html('');
                $('*[for="listkuisioner"] > small').html('');
                $('*[for="numberlevel"] > small').html('');
                $('*[for="chooselist"] > small').html('');
            } else if (numberlevel <= 0) {
                $('*[for="filtergrading"] > small').html('');
                $('*[for="kuisioner"] > small').html('');
                $('*[for="numberlevel"] > small').html('Harap Isi Number dengan benar');
                $('*[for="listkuisioner"] > small').html('');
                $('*[for="chooselist"] > small').html('');
            } else if (kuisioner == '') {
                $('*[for="filtergrading"] > small').html('');
                $('*[for="numberlevel"] > small').html('');
                $('*[for="kuisioner"] > small').html('Harap Isi !!');
                $('*[for="listkuisioner"] > small').html('');
                $('*[for="chooselist"] > small').html('');
            } else {
                var inputlevel = ''
                $.ajax({
                    url: '<?= base_url() ?>/MasterKuisioner/detaillevel',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        aksi: 'tambahKuisioner',
                        data: {
                            kategori: filtergrading,
                            numberlevel: numberlevel,
                        }
                    },
                    success: function(datalevel) {
                        if (datalevel > 0) {
                            $('*[for="filtergrading"] > small').html('');
                            $('*[for="kuisioner"] > small').html('');
                            $('*[for="numberlevel"] > small').html('Number Level Telah Terisi Please Cek Kategori dan Number Tidak Boleh sama!!!');
                            $('*[for="listkuisioner"] > small').html('');
                            $('*[for="chooselist"] > small').html('');
                        } else {
                            if (lengtharray <= 0) {
                                $('*[for="filtergrading"] > small').html('');
                                $('*[for="kuisioner"] > small').html('');
                                $('*[for="numberlevel"] > small').html('');
                                $('*[for="listkuisioner"] > small').html('harap isi minimal 1');
                                $('*[for="chooselist"] > small').html('');
                            } else {
                                var fixedpicture = [];
                                $('*[for="filtergrading"] > small').html('');
                                $('*[for="kuisioner"] > small').html('');
                                $('*[for="numberlevel"] > small').html('');
                                $('*[for="listkuisioner"] > small').html('');
                                $('*[for="chooselist"] > small').html('');

                                var lengthgambar = gambarketerangan.length
                                if (lengthgambar > 0) {
                                    if (lengthgambar == 1) {
                                        if ($('#' + gambarketerangan[0]['keterangan']).val() != '' && $('#' + gambarketerangan[0]['gambar']).val() != '') {
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
                                                        url: '<?= base_url() ?>/MasterKuisioner/tambah_data',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: {
                                                            aksi: 'tambahKuisioner',
                                                            data: {
                                                                kuisioner: kuisioner,
                                                                id_jgrading: filtergrading,
                                                                lisrtkuisioner: fixlist,
                                                                numberlevel: numberlevel,
                                                                validtext: validtext,
                                                            }
                                                        },
                                                        success: function(data) {
                                                            if (data.success == true) {
                                                                var photo = document.getElementById(gambarketerangan[0]['gambar']);
                                                                var file = photo.files[0];
                                                                data = new FormData();
                                                                data.append('file', file);
                                                                data.append('idlist', $('#' + gambarketerangan[0]['id_listkuisioner']).val());
                                                                data.append('keterangan', $('#' + gambarketerangan[0]['keterangan']).val())
                                                                $.ajax({
                                                                    url: '<?= base_url() ?>/MasterKuisioner/upload_foto',
                                                                    data: data,
                                                                    enctype: 'multipart/form-data',
                                                                    processData: false,
                                                                    contentType: false,
                                                                    type: 'POST',
                                                                });
                                                                $('#modalKuisioner').modal('hide');
                                                                alert("Create Data Kuisioner Succesfully")
                                                                window.location.reload()
                                                                toastr.success('Create Data Kuisioner Succesfully')
                                                                datatable.ajax.reload();
                                                                $('#text').prop('checked', false)
                                                                $('#filtergrading').val('');
                                                                $('#tambahkuisioner').val('');
                                                            } else {
                                                                alert("SUCCESS: " + data.status);

                                                            }
                                                            // console.log(data);

                                                        }
                                                    })

                                                }
                                            })
                                        } else {
                                            fixedpicture = ['kosong']
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
                                                        url: '<?= base_url() ?>/MasterKuisioner/tambah_data',
                                                        dataType: 'json',
                                                        method: 'POST',
                                                        data: {
                                                            aksi: 'tambahKuisioner',
                                                            data: {
                                                                kuisioner: kuisioner,
                                                                id_jgrading: filtergrading,
                                                                lisrtkuisioner: fixlist,
                                                                numberlevel: numberlevel,
                                                                validtext: validtext,
                                                            }
                                                        },
                                                        success: function(data) {
                                                            if (data.success == true) {
                                                                $('#modalKuisioner').modal('hide');
                                                                alert("Create Data Kuisioner Succesfully")
                                                                window.location.reload()
                                                                toastr.success('Create Data Kuisioner Succesfully')
                                                                datatable.ajax.reload();
                                                                $('#text').prop('checked', false)
                                                                $('#filtergrading').val('');
                                                                $('#tambahkuisioner').val('');
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
                                    if (lengthgambar > 1) {
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
                                                    url: '<?= base_url() ?>/MasterKuisioner/tambah_data',
                                                    dataType: 'json',
                                                    method: 'POST',
                                                    data: {
                                                        aksi: 'tambahKuisioner',
                                                        data: {
                                                            kuisioner: kuisioner,
                                                            id_jgrading: filtergrading,
                                                            numberlevel: numberlevel,
                                                            lisrtkuisioner: fixlist,
                                                            validtext: validtext,
                                                        }
                                                    },
                                                    success: function(data) {
                                                        if (data.success == true) {
                                                            for (let index = 0; index < lengthgambar; index++) {
                                                                if ($('#' + gambarketerangan[index]['keterangan']).val() != '' && $('#' + gambarketerangan[index]['gambar']).val() != '') {
                                                                    var photo = document.getElementById(gambarketerangan[index]['gambar']);
                                                                    var file = photo.files[0];
                                                                    data = new FormData();
                                                                    data.append('file', file);
                                                                    data.append('idlist', $('#' + gambarketerangan[index]['id_listkuisioner']).val());
                                                                    data.append('keterangan', $('#' + gambarketerangan[index]['keterangan']).val())
                                                                    $.ajax({
                                                                        url: '<?= base_url() ?>/MasterKuisioner/upload_foto',
                                                                        data: data,
                                                                        enctype: 'multipart/form-data',
                                                                        processData: false,
                                                                        contentType: false,
                                                                        type: 'POST',
                                                                    });

                                                                } else {
                                                                    continue;
                                                                }
                                                            }
                                                            $('#modalKuisioner').modal('hide');
                                                            alert("Create Data Kuisioner Succesfully")
                                                            window.location.reload()
                                                            toastr.success('Create Data Kuisioner Succesfully')
                                                            datatable.ajax.reload();
                                                            $('#text').prop('checked', false)
                                                            $('#filtergrading').val('');
                                                            $('#tambahkuisioner').val('');
                                                        } else {
                                                            alert("SUCCESS: " + data.status);

                                                        }
                                                        // console.log(data);

                                                    }

                                                })

                                            }
                                        })

                                    }
                                } else {
                                    fixedpicture = ['kosong']
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
                                                url: '<?= base_url() ?>/MasterKuisioner/tambah_data',
                                                dataType: 'json',
                                                method: 'POST',
                                                data: {
                                                    aksi: 'tambahKuisioner',
                                                    data: {
                                                        kuisioner: kuisioner,
                                                        id_jgrading: filtergrading,
                                                        numberlevel: numberlevel,
                                                        lisrtkuisioner: fixlist,
                                                        validtext: validtext,
                                                    }
                                                },
                                                success: function(data) {
                                                    if (data.success == true) {
                                                        $('#modalKuisioner').modal('hide');
                                                        alert("Create Data Kuisioner Succesfully")
                                                        window.location.reload()
                                                        toastr.success('Create Data Kuisioner Succesfully')
                                                        datatable.ajax.reload();
                                                        $('#text').prop('checked', false)
                                                        $('#filtergrading').val('');
                                                        $('#tambahkuisioner').val('');
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
                        }

                    }
                })

            }
        });
        $('#text').on('click', function() {
            var text = $('#text').val();
            validquisioner(text)
        });
        $('#file').on('click', function() {
            var file = $('#file').val();
            validquisioner(file)
        });

        function validquisioner(valid) {
            validasichoose = true;
            if (valid == 'text') {
                validasifile = false;
                $("#listtext").show("slow", function() {
                    // Animation complete.
                });
                $("#listfile").hide("slow", function() {
                    // Animation complete.
                });
            }
            if (valid == 'file') {
                validasifile = true;
                $("#listtext").hide("slow", function() {
                    // Animation complete.
                });
                $("#listfile").show("slow", function() {
                    // Animation complete.
                });
            }
        }
        //edit choose
        $('#edittext').on('click', function() {
            var text = $('#edittext').val();

            editvalidquisioner(text)
        });
        $('#editfile').on('click', function() {
            var file = $('#editfile').val();
            editvalidquisioner(file)
        });

        function editvalidquisioner(valid) {
            //validasichoose = true;
            if (valid == 'text') {
                //validasifile = false;
                $("#listedittext").show("slow", function() {
                    // Animation complete.
                });
                $("#listeditfile").hide("slow", function() {
                    // Animation complete.
                });
            }
            if (valid == 'file') {
                //validasifile = true;
                $("#listedittext").hide("slow", function() {
                    // Animation complete.
                });
                $("#listeditfile").show("slow", function() {
                    // Animation complete.
                });
            }
        }
        //close   
        $('#close').on('click', function() {
            window.location.reload()
        });
        $('#closecreate').on('click', function() {
            $('#filtergrading').val('');
            $('#tambahkuisioner').val('');
            window.location.reload()
        });
    });
</script>
<?= $this->endSection('content_js') ?>