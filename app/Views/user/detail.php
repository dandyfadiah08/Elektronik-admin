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
                <div class="col-6">
                  <div class="card-footer" _style="font-size: smaller;">
                    <div class="col">
                    <div class="row pt-2">

                      <div class="col-12 font-weight-bold">
                        Name
                      </div>
                      <div class="col-12">
                        <?= $u->name; ?>
                      </div>
                      <div class="col-12 font-weight-bold">
                        User Phone
                      </div>
                      <div class="col-12">
                        <?php
                        $status_phone = $u->phone_no_verified == 'y' ? true : false;
                        echo $u->phone_no . ' <i class="fas fa-' . check2Icon($status_phone) . '"></i>';
                        ?>
                      </div>
                      <div class="col-12 font-weight-bold">
                        User Email
                      </div>
                      <div class="col-12">
                        <?php
                        $status_email = $u->email_verified == 'y' ? true : false;
                        echo $u->email . ' <i class="fas fa-' . check2Icon($status_email) . '"></i>';
                        ?>
                      </div>
                      <div class="col-12 font-weight-bold">
                        Status User
                      </div>
                      <div class="col-12">
                        <?= $u->status; ?>
                      </div>

                      <div class="col-12 font-weight-bold">
                        User Type
                      </div>
                      <div class="col-12">
                        <?= ucfirst($u->type); ?>
                      </div>

                    </div>
                    </div>
                  </div>
                </div>

                <div class="col-6">
                  <div class="card-footer" _style="font-size: smaller;">
                    <div class="col">
                    <div class="row pt-2">

                    <div class="col-12 font-weight-bold">
                        Referral code
                      </div>
                      <div class="col-12">
                        <?= $u->ref_code; ?>
                      </div>

                      <div class="col-12 font-weight-bold">
                        Number Of Referrals
                      </div>
                      <div class="col-12">
                        <?= $u->count_referral; ?>
                      </div>

                      <div class="col-12 font-weight-bold">
                        Active Balances
                      </div>
                      <div class="col-12">
                        <?= number_to_currency($u->active_balance, "IDR"); ?>
                      </div>

                      <div class="col-12 font-weight-bold">
                        Pending Balances
                      </div>
                      <div class="col-12">
                        <?= number_to_currency($u->pending_balance, "IDR"); ?>
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