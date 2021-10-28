<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

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
            <li class="breadcrumb-item"><a href="#">Device Check</a></li>
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
          <div class="card">
            <!-- <div class="card-header">
              <h3 class="card-title">DataTable with default features</h3>
            </div> -->
            <div class="card-body">
              <div class="row">
                <?=
                htmlSelect([
                  'id' => 'filter-status',
                  'label' => 'Status',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                  ]) . htmlSelect([
                    'id' => 'filter-merchant',
                    'label' => 'Type',
                    'class' => 'select2bs4 myfilter',
                    'form_group' => 'col-sm-4',
                    'prepend' => '<i class="fas fa-user-tag" title="Merchant Filter"></i>',
                    'attribute' => 'data-placeholder="Merchant Filter"',
                    'option' => $optionMerchant,
                  ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Check Date',
                  'class' => 'datepicker myfilter',
                  'form_group' => 'col-sm-4',
                  'append' => '<i class="fas fa-calendar" title="Check Date Filter"></i>',
                ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Check Code</th>
                    <th>IMEI</th>
                    <th>Device</th>
                    <th>Grade</th>
                    <th>User / Customer</th>
                    <th>Action</th>
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
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.css">
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>

<script>
  const path = '/device_check';
  const exportAccess = <?= hasAccess($role, 'r_export_device_check') ? 'true' : 'false' ?>;
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    initDateRangePicker();

    let datatable = $("#datatable1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
        url: '<?= base_url() ?>/device_check/load_data',
        type: "post",
        data: function(d) {
          d.reviewed = '<?= $reviewed ?>';
          d.status = $('#filter-status option:selected').val();
          d.merchant = $('#filter-merchant option:selected').val();
          d.date = $('#filter-date').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 4, 5, 6, 7],
        className: "text-center",
      }, {
        targets: [0, 7],
        orderable: false
      }],
      order: [
        [1, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: ["reload", "export", "colvis", "pageLength"],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })
    $('body').on('click', '.btnLogs', function(e) {
      window.open(`${base_url}/logs/device_check/${$(this).data('id')}`)
    });


    if(exportAccess) {
      $('.btnExport').parent().parent().removeClass('d-none');
    }
  });
  if(exportAccess) {
      function btnExportClicked() {
        $.ajax({
          url: base_url + path + '/export',
          type: "post",
          dataType: "json",
          data: {
            reviewed: '<?= $reviewed ?>',
            status: $('#filter-status option:selected').val(),
            date: $('#filter-date').val(),
          }
        }).done(function(response) {
          if (response.success) {
            let msg = noticeDefault({ message: "Downloading..", autoClose: 2000, color: 'green'});
            window.open(response.data);
          } else if (Object.keys(response.data).length > 0) {
            for (const [key, value] of Object.entries(response.data)) {
              inputError(key, value)
            }
          } else
            Swal.fire(response.message, '', class_swal)
        }).fail(function(response) {
          Swal.fire('An error occured!', '', 'error')
        }).always(function() {
          $(".btnExport").removeClass("do-animation");
        })
  
      }
    }

</script>
<?= $this->endSection('content_js') ?>