<?php
$photo_url = base_url() . '/uploads/';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';
$photo_id = empty($dc->photo_id) ? $default_photo : $photo_url . 'photo_id/' . $dc->photo_id;
?>
<div class="row">
  <div class="col">
    <div class="card card-widget widget-user shadow">
      <div class="widget-user-header bg-info">
        <h3 class="widget-user-username"><?= $dc->name ?></h3>
        <h5 class="widget-user-desc"><?= $dc->check_code ?></h5>
      </div>
      <div class="widget-user-image">
        <img class="img-circle elevation-2" src="<?= $photo_id ?>" alt="Photo ID">
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-sm-4 border-right">
            <div class="row">
              <div class="col-4">IMEI</div>
              <div class="col-8">: <?= $dc->imei ?></div>
            </div>
            <div class="row">
              <div class="col-4">Device</div>
              <div class="col-8">: <?= "$dc->brand $dc->model $dc->storage"; ?></div>
            </div>
            <div class="row">
              <div class="col-4">Type</div>
              <div class="col-8">: <?= $dc->type; ?></div>
            </div>
          </div>
          <div class="col-sm-4 border-right">
            <div class="row">
              <div class="col-4">IMEI</div>
              <div class="col-8">: <?= $dc->imei ?></div>
            </div>
            <div class="row">
              <div class="col-4">Device</div>
              <div class="col-8">: <?= "$dc->brand $dc->model $dc->storage"; ?></div>
            </div>
            <div class="row">
              <div class="col-4">Type</div>
              <div class="col-8">: <?= $dc->type; ?></div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="row">
              <div class="col-4">IMEI</div>
              <div class="col-8">: <?= $dc->imei ?></div>
            </div>
            <div class="row">
              <div class="col-4">Device</div>
              <div class="col-8">: <?= "$dc->brand $dc->model $dc->storage"; ?></div>
            </div>
            <div class="row">
              <div class="col-4">Type</div>
              <div class="col-8">: <?= $dc->type; ?></div>
            </div>
          </div>
        </div>
        <?php if($isResultPage): ?>
        <div class="row">
          <div class="col-sm-4 border-right">
            <div class="row">
              <div class="col-4">Grade</div>
              <div class="col-8">: <?= $dc->grade ?></div>
            </div>
          </div>
          <div class="col-sm-4 border-right">
            <div class="row">
              <div class="col-4">Price</div>
              <div class="col-8">: <?= number_to_currency($dc->price, "IDR") ?></div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="row">
              <div class="col-4">Fullset Price</div>
              <div class="col-8">: <?= number_to_currency($dc->fullset_price, "IDR") ?></div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>