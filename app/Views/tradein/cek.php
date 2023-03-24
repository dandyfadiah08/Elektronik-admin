<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="modal fade" id="notes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">Notes : <?= $datatable2["kode_tradein"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="nonotes" id="nonotes" value="<?= $page->kode_kuis ?>">
                <label for="nama_deskripsi">Descriptions notes : <small class="text-warning"></small></label>
                <textarea id="isiNote" name="notes" class="form-control"><?= $page->notes ?></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" name="id_mitra" onclick="edit()" class="btn btn-primary">Save Notes</button>
            </div>
        </div>
    </div>
</div>
<div class="content-wrapper" style="background-color: white; color:black;">
    <!-- Main content -->
    <section class="section">
        <div>
            <div class="title-text mb-3">Data Cek Elektronik</div>
            <div class="row">
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Tanggal Tradein</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["date_save"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Serial Number</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["sn"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Kode Tradein</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["kode_tradein"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Kategori</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["kategori"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Toko</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["toko"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Merk dan Tahun</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["merk_tahun"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Nama Device Checker</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["device_checker"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Spesifikasi dan Ukuran</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["spec_size"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Telp/Email</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["no_telp"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Harga dan Subsidi</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["harga_subsidi"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Alamat Toko</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["alamat"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Harga Total</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["harga_total"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">Pameran</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["nama_pameran"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
                <div class="col-lg-2 col-md-8 ">
                    <b class="lblTeks">New Phone</b>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-4 ">
                    <?php foreach ($page as $key => $datatable2) : ?>
                        <?php if ($key == 'tradein') : ?>
                            <p class="lblTeks">: <?= $datatable2["kode_product"] ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="container mt-5 mb-3">
                <button type="button" id="tombolNotes" class="btn btn-success btn-sm mb-1 mb-3" style="width:140px;height:40px">
                    <i class="fas fa-comment-dots"></i> NOTES
                </button>
                <div class="row">
                    <?php foreach ($page as $key => $datatable) : ?>
                        <?php if ($key == 'kuisioner') : ?>
                            <?php foreach ($datatable as $key => $value) : ?>
                                <div class="col-md-4">
                                    <div class="card p-3 mb-2 ">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex flex-row align-items-center">
                                                <div class="ms-2 c-details ml-3">
                                                    <h6 class="mb-0"><?= $value['kuisioner'] ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <?php foreach ($value['list_kuisioner'] as $key => $data5) : ?>
                                            <div class="ml-3 mt-3 mb-1">
                                                <i class="far fa-plus-square">
                                                </i>
                                                <?= $data5['list'] ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">


        </div>
    </section>
</div>
<?= $this->section('content_css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3//plugins/toastr/toastr.min.css">
<style>
    /* Data Text-title */
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

    .card {
        border: none;
        border-radius: 10px;
        background: linear-gradient(to top right, #6600ff 0%, #9999ff 100%);
    }

    .c-details span {
        font-weight: 300;
        font-size: 13px
    }

    .icon {
        width: 50px;
        height: 50px;
        background-color: #eee;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 39px
    }

    .badge span {
        background-color: #fffbec;
        width: 60px;
        height: 25px;
        padding-bottom: 3px;
        border-radius: 5px;
        display: flex;
        color: black;
        justify-content: center;
        align-items: center
    }

    .card:hover {
        background: linear-gradient(to top right, #6600ff 0%, #66ff99 100%);
    }

    .progress {
        height: 10px;
        border-radius: 10px
    }

    .progress div {
        background-color: red
    }

    .text1 {
        font-size: 14px;
        font-weight: 600
    }

    .text2 {
        color: #a5aec0
    }
</style>
<?= $this->endSection('content_css') ?>
<?= $this->endSection('content') ?>
<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/toastr/toastr.min.js"></script>
<script>
    const path = '/tradein'
    $('#tombolNotes').on('click', function() {

        $('#notes').modal('show');

    });

    function edit() {
        var nonotes = $('#nonotes').val();
        const notes = $('#isiNote').val();
        if (notes == "") {
            $('*[for="nama_deskripsi"] > small').html('Harap diisi!');
        } else {
            $('*[for="nama_deskripsi"] > small').html('');
            $.ajax({
                    url: `${base_url}${path}/notescek`,
                    type: "post",
                    dataType: "json",
                    data: {
                        no: nonotes,
                        notes: notes,
                    }
                })
                .always(function() {
                    $('#notes').modal('hide');
                    toastr.success('Notes Cek Data Tradein Update Succesfully')
                });
            return false;
        }
    }
</script>
<?= $this->endSection('content_js') ?>