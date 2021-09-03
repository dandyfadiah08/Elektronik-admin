<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<?php
$photo_url = base_url() . '/uploads/';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';
$photo_fullset = empty($dc->photo_fullset) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_fullset;
$photo_imei_registered = empty($dc->photo_imei_registered) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_imei_registered;
$photo_device_1 = empty($dc->photo_device_1) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_1;
$photo_device_2 = empty($dc->photo_device_2) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_2;
$photo_device_3 = empty($dc->photo_device_3) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_3;
$photo_device_4 = empty($dc->photo_device_4) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_4;
$photo_device_5 = empty($dc->photo_device_5) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_5;
$photo_device_6 = empty($dc->photo_device_6) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_6;
$check_software = [
  'Quiz 1' => $dc->quiz_1,
  'Quiz 2' => $dc->quiz_2,
  'Quiz 3' => $dc->quiz_3,
  'Quiz 4' => $dc->quiz_4,
  'SIM Card' => $dc->simcard,
  'Screen' => $dc->screen,
  'Back Camera' => $dc->camera_back,
  'Front Camera' => $dc->camera_front,
  'Button Volume' => $dc->button_volume,
  'Button Back' => $dc->button_back,
  'Button Power' => $dc->button_power,
  'Root/Jailbreak' => $dc->root,
  'CPU' => $dc->cpu,
  'Harddisk' => $dc->harddisk,
  'Battery' => $dc->battery,
  'Fullset' => $dc->fullset,
  'IMEI Terdaftar' => $dc->imei_registered,
];
function renderCheckSoftwareResult($data) {
  $output = '';
  foreach($data as $key => $val) {
    $output .= '
    <div class="col-md-3 col-sm-4 col-3">
      <span class="text-'.check2Color($val).'">
      <i class="fas fa-'.check2Icon($val).'"></i> '.$key.'
      </span>
    </div>
    ';
  }
  return $output;
}
?>
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
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check">Device Check</a></li>
            <li class="breadcrumb-item status"><?= $page->navbar ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                Detail basic information over here<br>
                Grade : <?= $dc->grade ?><br>
                Price : <?= number_to_currency($dc->price, "IDR") ?><br>
                Fullset Price : <?= number_to_currency($dc->fullset_price, "IDR") ?><br>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="card card-primary collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Software</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <?= renderCheckSoftwareResult($check_software) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Photos</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="row">
                  <div class="col-3">
                    <img src="<?= $photo_device_1 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_2 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_3 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_4 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_5 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_6 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_fullset ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_imei_registered ?>" alt="" class="image-fluid myimage">
                  </div>
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
<style>
  .myimage {
    max-height: 150px;
    margin-bottom: 1rem;
  }
</style>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>

<script>
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })
  });
</script>
<?= $this->endSection('content_js') ?>