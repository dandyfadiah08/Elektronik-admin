
<?php
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

function renderCheckSoftwareResult($data)
{
  $output = '';
  foreach ($data as $key => $val) {
    $output .= '
    <div class="col-md-3 col-sm-4 col-3">
      <span class="text-' . check2Color($val) . '">
      <i class="fas fa-' . check2Icon($val) . '"></i> ' . $key . '
      </span>
    </div>
    ';
  }
  return $output;
}

?>

<div class="row">
  <div class="col">
    <div class="card card-primary collapsed-card">
      <!-- <div class="card card-primary"> -->
      <div class="card-header" data-card-widget="collapse">
        <h3 class="card-title">Software</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool">
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