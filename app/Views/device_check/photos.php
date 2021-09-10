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

?>
<div class="row">
  <div class="col">
    <div class="card card-primary">
      <div class="card-header" data-card-widget="collapse">
        <h3 class="card-title">Photos</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_1 ?>" data-magnify="gallery" data-caption="Front Side">
              <img src="<?= $photo_device_1 ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_2 ?>" data-magnify="gallery" data-caption="Back Side">
              <img src="<?= $photo_device_2 ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_3 ?>" data-magnify="gallery" data-caption="Right Side">
              <img src="<?= $photo_device_3 ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_4 ?>" data-magnify="gallery" data-caption="Left Side">
              <img src="<?= $photo_device_4 ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_5 ?>" data-magnify="gallery" data-caption="Top Side">
              <img src="<?= $photo_device_5 ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_6 ?>" data-magnify="gallery" data-caption="Bottom Side">
              <img src="<?= $photo_device_6 ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_fullset ?>" data-magnify="gallery" data-caption="Fullset Photo">
              <img src="<?= $photo_fullset ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_imei_registered ?>" data-magnify="gallery" data-caption="IMEI Status Photo">
              <img src="<?= $photo_imei_registered ?>" alt="" class="image-fluid device-check-image">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>