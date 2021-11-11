<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<?php
$photo_url = base_url() . '/uploads/';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';

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

$status_phone = $user->phone_no_verified == 'y';
$status_email = $user->email_verified == 'y';

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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/users">Users</a></li>
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
            <div class="card-header">
              <h3 class="card-title">User Detail</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="card-footer" _style="font-size: smaller;">
                    <div class="col">
                      <div class="row pt-2">

                        <div class="col-12 font-weight-bold">
                          Name
                        </div>
                        <div class="col-12">
                          <?= $user->name; ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Phone No
                        </div>
                        <div class="col-12">
                          <?= $user->phone_no . ' <i class="fas fa-' . check2Icon($status_phone) . ' text-' . check2Color($status_phone) . '"></i>'; ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Email
                        </div>
                        <div class="col-12">
                          <?= $user->email . ' <i class="fas fa-' . check2Icon($status_email) . ' text-' . check2Color($status_phone) . '"></i>'; ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Status
                        </div>
                        <div class="col-12">
                          <?= $user->status; ?>
                        </div>

                        <div class="col-12 font-weight-bold">
                          Type
                        </div>
                        <div class="col-12">
                          <?= ucfirst($user->type); ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Merchant
                        </div>
                        <div class="col-12">
                          <?= $user->merchant_name; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="card-footer" _style="font-size: smaller;">
                    <div class="col">
                      <div class="row pt-2">
                        <div class="col-12 font-weight-bold">
                          Referral code
                        </div>
                        <div class="col-12">
                          <?= $user->ref_code; ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Number Of Referrals
                        </div>
                        <div class="col-12">
                          <?= $user->count_referral; ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Total Success Transaction
                        </div>
                        <div class="col-12">
                          <?= toPrice($other->transaction); ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Active Balances
                        </div>
                        <div class="col-12">
                          <?= number_to_currency($user->active_balance, "IDR"); ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Pending Balances
                        </div>
                        <div class="col-12">
                          <?= number_to_currency($user->pending_balance, "IDR"); ?>
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
      <div class="row">
        <div class="col">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">User Referrals</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <!-- looping referrals : start -->
                  <?php if ($other->referrals) foreach ($other->referrals as $no => $ref) : ?>
                    <div class="col">
                      <div class="row mb-2 py-2 card-footer border-top border-primary">
                        <div class="col-12 font-weight-bold text-primary"><?= ++$no ?> [<?= $ref->u2_user_id ?>]</div>
                        <div class="col-sm-6">
                          <div class="row">
                            <div class="col-12 font-weight-bold">
                              Name
                            </div>
                            <div class="col-12">
                              <?= $ref->u2_name; ?>
                            </div>
                            <div class="col-12 font-weight-bold">
                              Phone No
                            </div>
                            <div class="col-12">
                              <?= $ref->u2_phone_no . ' <i class="fas fa-' . check2Icon($status_phone) . ' text-' . check2Color($status_phone) . '"></i>'; ?>
                            </div>
                            <div class="col-12 font-weight-bold">
                              Email
                            </div>
                            <div class="col-12">
                              <?= $ref->u2_email . ' <i class="fas fa-' . check2Icon($status_email) . ' text-' . check2Color($status_phone) . '"></i>'; ?>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="row">
                            <div class="col-12 font-weight-bold">
                              Joined
                            </div>
                            <div class="col-12">
                              <?= $ref->u2_joined; ?>
                            </div>
                            <div class="col-12 font-weight-bold">
                              Status
                            </div>
                            <div class="col-12">
                              <?= $ref->u2_status; ?>
                            </div>
                            <div class="col-12 font-weight-bold">
                              Type
                            </div>
                            <div class="col-12">
                              <?= ucfirst($ref->u2_type); ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <!-- looping referrals : end -->
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