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
                          <?= "$user->count_referral ($other->active_referral aktif, $other->pending_referral pending)" ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Total Success Transaction
                        </div>
                        <div class="col-12">
                          <?= toPrice($other->total_transaction); ?>
                        </div>
                        <div class="col-12 font-weight-bold">
                          Total Success Transaction (Rp)
                        </div>
                        <div class="col-12">
                          <?= number_to_currency($other->total_transaction_price ?? 0, "IDR"); ?>
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
                <?=
                htmlSelect([
                  'id' => 'filter-status',
                  'label' => 'Status',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ]) . htmlSelect([
                  'id' => 'filter-type',
                  'label' => 'Type',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-user" title="Type Filter"></i>',
                  'attribute' => 'data-placeholder="Type Filter"',
                  'option' => $optionType,
                ]) . htmlSelect([
                  'id' => 'filter-merchant',
                  'label' => 'Merchant',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-user-tag" title="Merchant Filter"></i>',
                  'attribute' => 'data-placeholder="Merchant Filter"',
                  'option' => $optionMerchant,
                ]) . htmlSelect([
                  'id' => 'filter-level',
                  'label' => 'Level',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-users" title="Level Filter"></i>',
                  'attribute' => 'data-placeholder="Level Filter"',
                  'option' => $optionLevel,
                ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Register Date',
                  'class' => 'datepicker myfilter',
                  'form_group' => 'col-sm-3',
                  'append' => '<i class="fas fa-calendar" title="Register Date Filter"></i>',
                  'prepend' => '<i class="fas fa-undo-alt" title="Clear Date Filter" id="clearDate"></i>',
                  'attribute' => 'title="Tidak berpengaruh jika filter Submission Request aktif"',
                ])
                ?>
              </div>
              <div class="row">
                <?= htmlIcheckbox([
                  'id' => 'filter-submission',
                  'class' => 'myfilter',
                  'title' => 'show only submission request / need review',
                  'label' => 'Submission Request',
                  'color' => 'danger',
                  'form_group' => 'col-sm-3',
                ]) ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Register Date</th>
                    <th>Level</th>
                    <th>Name</th>
                    <th>Merchant</th>
                    <th>Phone No</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Submission</th>
                  </tr>
                </thead>
              </table>
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
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.css">
<style>
  .myimage {
    max-height: 150px;
    margin-bottom: 1rem;
  }
</style>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>

<script>
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    initDateRangePicker();
    $('#clearDate').click(function() {
      $('.datepicker').val('')
      datatable.ajax.reload()
    })

    let datatable = $("#datatable1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
        url: '<?= base_url() ?>/users/load_referrals',
        type: "post",
        data: function(d) {
          d.user_id = <?= $user->user_id ?>;
          d.status = $('#filter-status option:selected').val();
          d.submission = $('#filter-submission').prop('checked');
          d.type = $('#filter-type option:selected').val();
          d.merchant = $('#filter-merchant option:selected').val();
          d.level = $('#filter-level option:selected').val();
          d.date = $('#filter-date').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 4, 5, 6, 7, 8, 9],
        className: "text-center",
      }, {
        targets: [0],
        orderable: false
      }],
      order: [
        [3, "asc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [
        "reload", <?= $access['export'] ? '"excel"' : '' ?>, "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })
    $('body').on('click', '.btnLogs', function(e) {
      window.open(`${base_url}/logs/user/${$(this).data('id')}`)
    });

  });
</script>
<?= $this->endSection('content_js') ?>