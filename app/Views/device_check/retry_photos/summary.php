<?php
$dc = $dcs[0];
$photo_url = base_url() . '/uploads/';
$photo_thumb_url = base_url() . '/image/thumbnail/?file=';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';

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
  <div class="col-12">
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
    </div>
  </div>
</div>
