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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check">Unreviewed</a></li>
            <li class="breadcrumb-item status"><?= $page->navbar ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <?= $this->include('device_check/summary') ?>
      <?= $this->include('device_check/software_check') ?>
      <?= $this->include('device_check/photos') ?>
      <?php if (hasAccess($role, 'r_review') && $dc->dc_status == 4) : ?>
        <div class="row">
          <div class="col">
            <div class="card card-primary">
              <div class="card-header" data-card-widget="collapse">
                <h3 class="card-title">Action</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <button class="btn btn-sm btn-success m-1" data-toggle="modal" data-target="#modalManualGrade"><i class="fas fa-poll-h"></i> Manual Grade</button>
                    <button class="btn btn-sm btn-warning m-1" data-toggle="modal" data-target="#modalRetryPhoto"><i class="fas fa-poll-h"></i> Retry Photo</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?= $this->include('device_check/manual_grade') ?>
      <?php endif; ?>

    </div>
  </div>

</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/custom.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.css">
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>

<script>
  $(document).ready(function() {
    $('[data-magnify]').magnify({
      resizable: false,
      initMaximized: true,
      headerToolbar: [
        'close'
      ],
    });

    $('.btnLogs').click(function() {
      window.open(`${base_url}/logs/device_check/${$(this).data('id')}`)
    });
  });
</script>
<?= $this->endSection('content_js') ?>