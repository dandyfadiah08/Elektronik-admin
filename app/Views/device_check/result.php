<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><?= $page->title ?> - <?= $page->subtitle ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check">Device Check</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check/reviewed">Reviewed</a></li>
            <li class="breadcrumb-item status"><?= $page->navbar ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <!-- <div class="card card-primary">
        <div class="card-body">
          <div class="row">
            Detail basic information over here<br>
            Grade : <?= $dc->grade ?><br>
            Price : <?= number_to_currency($dc->price, "IDR") ?><br>
            Fullset Price : <?= number_to_currency($dc->fullset_price, "IDR") ?><br>
          </div>
        </div>
      </div> -->
      <?= $this->include('device_check/summary') ?>
      <?= $this->include('device_check/software_check') ?>
      <?= $this->include('device_check/photos') ?>
      <div class="row">
        <div class="col">
          <div class="card card-primary">
            <div class="card-header" data-card-widget="collapse">
              <h3 class="card-title">Review Detail</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <table>

                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/custom.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.css">
</style>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>

<script>
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $('[data-magnify]').magnify({
      resizable: false,
      initMaximized: true,
      headerToolbar: [
        'close'
      ],
    });
  });
</script>
<?= $this->endSection('content_js') ?>