<?php
$photo_url = base_url() . '/uploads/';
$photo_thumb_url = base_url() . '/image/thumbnail/?file=';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';
$photo_fullset = empty($dc->photo_fullset) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_fullset;
$photo_imei_registered = empty($dc->photo_imei_registered) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_imei_registered;
$photo_device_1 = empty($dc->photo_device_1) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_1;
$photo_device_2 = empty($dc->photo_device_2) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_2;
$photo_device_3 = empty($dc->photo_device_3) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_3;
$photo_device_4 = empty($dc->photo_device_4) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_4;
$photo_device_5 = empty($dc->photo_device_5) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_5;
$photo_device_6 = empty($dc->photo_device_6) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_6;
$photo_fullset_thumb = empty($dc->photo_fullset) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_fullset;
$photo_imei_registered_thumb = empty($dc->photo_imei_registered) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_imei_registered;
$photo_device_1_thumb = empty($dc->photo_device_1) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_device_1;
$photo_device_2_thumb = empty($dc->photo_device_2) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_device_2;
$photo_device_3_thumb = empty($dc->photo_device_3) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_device_3;
$photo_device_4_thumb = empty($dc->photo_device_4) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_device_4;
$photo_device_5_thumb = empty($dc->photo_device_5) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_device_5;
$photo_device_6_thumb = empty($dc->photo_device_6) ? $default_photo : $photo_thumb_url . 'device_checks/' . $dc->photo_device_6;

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
              <span>Front Side</span>
              <br>
              <img src="<?= $photo_device_1_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_2 ?>" data-magnify="gallery" data-caption="Back Side">
              <span>Back Side</span>
              <br>
              <img src="<?= $photo_device_2_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_3 ?>" data-magnify="gallery" data-caption="Right Side">
              <span>Right Side</span>
              <br>
              <img src="<?= $photo_device_3_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_4 ?>" data-magnify="gallery" data-caption="Left Side">
              <span>Left Side</span>
              <br>
              <img src="<?= $photo_device_4_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_5 ?>" data-magnify="gallery" data-caption="Top Side">
              <span>Top Side</span>
              <br>
              <img src="<?= $photo_device_5_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_device_6 ?>" data-magnify="gallery" data-caption="Bottom Side">
              <span>Bottom Side</span>
              <br>
              <img src="<?= $photo_device_6_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_fullset ?>" data-magnify="gallery" data-caption="Fullset">
              <span>Fullset</span>
              <br>
              <img src="<?= $photo_fullset_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
          <div class="col-3 device-check-image-wrapper">
            <a href="<?= $photo_imei_registered ?>" data-magnify="gallery" data-caption="IMEI Status">
              <span>IMEI Status</span>
              <br>
              <img src="<?= $photo_imei_registered_thumb ?>" loading="lazy" alt="" class="image-fluid device-check-image">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>