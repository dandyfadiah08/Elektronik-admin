<?php
$photo_url = base_url() . '/uploads/';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';
$photo_id = empty($dc->photo_id) || !hasAccess($role, 'r_view_photo_id') ? $default_photo : $photo_url . 'photo_id/' . $dc->photo_id;
function renderSummary($title, $value, $col = [], $dots = ': ')
{
  $col1 = 4;
  $col2 = 8;
  if (count($col) == 2) {
    $col1 = $col[0];
    $col2 = $col[1];
  }
  return '<div class="row">
    <div class="col-' . $col1 . '">' . $title . '</div>
    <div class="col-' . $col2 . '">' . $dots . $value . '</div>
  </div>';
}
?>
<div class="row">
  <div class="col-3">
    <div class="card card-widget widget-user shadow">
      <div class="widget-user-header bg-info">
        <h3 class="widget-user-username"><?= "$dc->name - $dc->type_user" ?></h3>
        <h5 class="widget-user-desc"><?= $dc->check_code ?></h5>
      </div>
      <div class="widget-user-image" href="<?= $photo_id ?>" data-magnify="gallery" data-caption="Photo ID">
        <img class="img-circle elevation-2" src="<?= $photo_id ?>" alt="Photo ID">
        <!-- </a> -->
      </div>
    </div>
    <div class="card-footer" _style="font-size: smaller;">
      <?php if ($dc->status_internal > 1) : ?>
        <div class="row pt-2">
          <div class="col-12 font-weight-bold">
            Customer Name
          </div>
          <div class="col-12">
            <?= $dc->customer_name ?>
          </div>
          <?php if (hasAccess($role, 'r_view_phone_no')) : ?>
            <div class="col-12 font-weight-bold">
              Customer Phone
            </div>
            <div class="col-12">
              <?= $dc->customer_phone ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-9">
    <div class="card-footer">
      <div class="row">
        <div class="col-12 font-weight-bold">
          Device
        </div>
        <div class="col-sm-6 border-right">
          <?= renderSummary('Status', getDeviceCheckStatus($dc->dc_status)) ?>
          <?= renderSummary('IMEI', $dc->imei) ?>
          <?= renderSummary('Device', "$dc->brand $dc->model $dc->storage") ?>
          <?= renderSummary('Type', $dc->type) ?>
        </div>
        <div class="col-sm-6">
          <?= renderSummary('Status Int <small class="fa fa-info-circle" title="Status Internal"></small>', getDeviceCheckStatusInternal($dc->status_internal)) ?>
          <?= renderSummary('Check Date', formatDate($dc->created_at)) ?>
          <?= renderSummary('Finish Date', formatDate($dc->finished_date)) ?>
          <?= renderSummary('Fullset A <small class="fa fa-info-circle" title="Fullset by app"></small>', $dc->fullset == 1 ? 'Yes' : 'No') ?>
          <?= renderSummary('Fullset R <small class="fa fa-info-circle" title="Fullset by reviewer"></small>', $dc->survey_fullset == 1 ? 'Yes' : 'No') ?>
        </div>
      </div>
      <?php if ($isResultPage) : ?>
        <div class="row">
          <div class="col-12 font-weight-bold border-top">
            Result
          </div>
          <div class="col-sm-6 border-right">
            <?= renderSummary('Promo', $dc->promo_name) ?>
            <?= renderSummary('Grade', $dc->grade) ?>
            <?= renderSummary('Review By', $dc->survey_name) ?>
            <?= renderSummary('Review Date', formatDate($dc->survey_date)) ?>
          </div>
          <div class="col-sm-6">
            <?= renderSummary('Fullset Price', number_to_currency($dc->fullset_price, "IDR")) ?>
            <?= renderSummary('Unit Price', number_to_currency($dc->price - $dc->fullset_price, "IDR")) ?>
            <?= renderSummary('Price', number_to_currency($dc->price, "IDR")) ?>
          </div>
        </div>
      <?php endif; ?>
      <?php if ($dc->status_internal > 2) : ?>
        <div class="row">
          <?php if (hasAccess($role, 'r_view_address')) : ?>
            <div class="col-6 border-top">
              <div class="row">
                <div class="col-12 font-weight-bold">
                  Address
                </div>
                <div class="col-12">
                  <?= renderSummary('Province', $dc->province_name) ?>
                  <?= renderSummary('City', $dc->city_name) ?>
                  <?= renderSummary('District', $dc->district_name) ?>
                  <?= renderSummary('Postal', $dc->postal_code) ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php if (hasAccess($role, 'r_view_payment_detail')) : ?>
            <div class="col-6 border-top">
              <div class="row">
                <div class="col-12 font-weight-bold">
                  Payment Detail
                </div>
                <div class="col-12">
                  <?= renderSummary('Type', strtoupper($dc->pm_type)) ?>
                  <?= renderSummary('Method', $dc->pm_name) ?>
                  <?= renderSummary('Number', $dc->account_number) ?>
                  <?= renderSummary('Name', $dc->account_name) ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php if (hasAccess($role, 'r_view_address')) : ?>
            <div class="col-sm-12">
              <?= renderSummary('Full Address', $dc->full_address, [2, 10]) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</div>