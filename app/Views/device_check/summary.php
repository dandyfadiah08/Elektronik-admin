<?php
$photo_url = base_url() . '/uploads/';
$photo_thumb_url = base_url() . '/image/thumbnail/?file=';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';
$photo_id = empty($dc->photo_id) || !hasAccess($role, 'r_view_photo_id') ? $default_photo : $photo_url . 'photo_id/' . $dc->photo_id;
$photo_id_thumb = empty($dc->photo_id) || !hasAccess($role, 'r_view_photo_id') ? $default_photo : $photo_thumb_url . 'photo_id/' . $dc->photo_id;

$report_text = "Wowfonet\n\nCheck Code: $dc->check_code\nGrade: $dc->grade";
$btn = [
  'logs' => htmlAnchor([
    'color'	=> 'outline-primary',
    'class'	=> "btnLogs".($access_logs ? '' : ' d-none'),
    'title'	=> "View logs of $dc->check_code",
    'data'	=> 'data-id="'.$dc->check_id.'"',
    'icon'	=> 'fas fa-history',
    'text'	=> '',
  ], false),
  'merchant' => $dc->merchant_id > 0 ? htmlAnchor([
    'color'	=> 'warning',
    'title'	=> "Merchant $dc->merchant_name",
    'data'	=> 'data-id="'.$dc->check_id.'"',
    'icon'	=> 'fas fa-user-tag',
    'text'	=> $dc->merchant_name,
  ], false) : '',
];
?>
<div class="row">
  <div class="col-3">
    <div class="card card-widget widget-user shadow">
      <div class="widget-user-header bg-info">
        <h3 class="widget-user-username"><a href="<?= base_url("users/detail/$dc->user_id") ?>" class="text-white" title="Klik untuk lihat detail user"><u><?= "$dc->name - $dc->type_user" ?></u></a></h3>
        <h5 class="widget-user-desc"><?= $dc->check_code ?></h5>
      </div>
      <div class="widget-user-image" href="<?= $photo_id ?>" data-magnify="gallery" data-caption="Photo ID">
        <img class="img-circle elevation-2" src="<?= $photo_id_thumb ?>" alt="Photo ID">
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
          Device <?= $btn['logs'].$btn['merchant'] ?>
        </div>
        <div class="col-sm-6 border-right">
          <?= renderSummary('Status', getDeviceCheckStatus($dc->dc_status)) ?>
          <?= renderSummary('IMEI', $dc->imei) ?>
          <?= renderSummary('Device', "$dc->brand $dc->model $dc->storage") ?>
          <?= renderSummary('Type', $dc->type) ?>
          <?= renderSummary('Check Code', '<span class="pointer" title="Click to copy report" data-copy="'.$dc->check_code.'">'.$dc->check_code.' <small><i class="fas fa-copy"></i></small>') ?>
          <?php if ($dc->general_notes) : ?>
            <?= renderSummary('General/Cancel Notes', $dc->general_notes) ?>
          <?php endif; ?>
        </div>
        <div class="col-sm-6">
          <?= renderSummary('Status Int <small class="fa fa-info-circle" title="Status Internal"></small>', getDeviceCheckStatusInternal($dc->status_internal)) ?>
          <?= renderSummary('Check Date', formatDate($dc->check_date)) ?>
          <?= renderSummary('Finish Date', formatDate($dc->finished_date)) ?>
          <?= renderSummary('Completed Date', empty($dc->payment_date) ? '-' : formatDate($dc->payment_date)) ?>
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
            <?= renderSummary('Promo', '<a href="'.base_url("price/$dc->promo_id").'" target="_blank">'.$dc->promo_name.'</a>') ?>
            <?= renderSummary('Review By', $dc->survey_name) ?>
            <?= renderSummary('Review Log', $dc->survey_log) ?>
            <?= renderSummary('Review Date', formatDate($dc->survey_date)) ?>
          </div>
          <div class="col-sm-6">
            <?= renderSummary('Fullset Price', number_to_currency($dc->fullset_price, "IDR")) ?>
            <?= renderSummary('Unit Price', number_to_currency($dc->price - $dc->fullset_price, "IDR")) ?>
            <?= renderSummary('Price', '<a href="'.base_url("price/$dc->promo_id?s=$dc->model").'" target="_blank">'.number_to_currency($dc->price, "IDR").'</a>') ?>
            <?= renderSummary('Grade', '<span class="pointer" title="Click to copy report" data-copy="'.$report_text.'">'.$dc->grade.' <small><i class="fas fa-copy"></i></small></span>') ?>
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
                  <?= renderSummary('Name', htmlentities($dc->account_name)) ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php if (hasAccess($role, 'r_view_address')) : ?>
            <div class="col-sm-12">
              <?= renderSummary('Full Address', htmlentities($dc->full_address), [2, 10]) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-12 font-weight-bold border-top">
          Others
        </div>
        <div class="col-sm-6 border-right">
          <?= renderSummary('Retry Photo Reason', htmlentities($dc->rp_reason)) ?>
          <?= renderSummary('Retry Photo Status', ucfirst($dc->rp_status).' (<a href="'.base_url('device_check/retry_photos/'.$dc->check_id).'">Full reports</a>)') ?>
        </div>
        <div class="col-sm-6">
          <?= renderSummary('Damage', htmlentities($dc->damage)) ?>
        </div>
      </div>

    </div>
  </div>
</div>
