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
            <li class="breadcrumb-item"><a href="#">Home</a></li>
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
            <div class="card-body">
              <div class="row">
                <?=
                  htmlInput([
                    'id' => 'filter-date',
                    'label' => 'Bonus Date',
                    'class' => 'datepicker myfilter',
                    'form_group' => 'col-sm-4',
                    'prepend' => '<i class="fas fa-calendar" title="Bonus Date Filter"></i>',
                    'append' => '<i class="fas fa-undo-alt clearFilter" title="Click to Clear Filter" data-target="#filter-date"></i>',
                    ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Type</th>
                    <th>ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>Date</th>
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
  const path = '/tax';
  var errors = null;
  var _search = <?= $search ?>;
  const exportAccess = <?= hasAccess($role, 'r_export_tax') ? 'true' : 'false' ?>;

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
        url: base_url + path + '/load_data',
        type: "post",
        data: function(d) {
          d.user_id = $('#filter-users option:selected').val();
          d.date = $('#filter-date').val();

          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 5, 6],
        className: "text-center",
      }, {
        targets: [4],
        className: "text-right",
      }, {
        targets: [0],
        orderable: false
      }],
      order: [
        [5, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [
        "reload", "export", "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('.clearFilter').click(function() {
      clearFilter($(this).data('target'), $(this).data('select2'))
    })

    function clearFilter(target = false, select2 = false) {
      if (target) {
        if (select2) $(target).val('all').trigger('change')
        else {
          $(target).val(null)
          datatable.ajax.reload()
        }
      }
    }

    if (_search) {
      $('#isLoading').removeClass('d-none');
      setTimeout(() => {
        $('#isLoading').addClass('d-none');
        datatable.search(_search).draw();
      }, 2000);
    }

    if (exportAccess) {
      $('.btnExport').parent().parent().removeClass('d-none');
    }

  });

  if (exportAccess) {
    function btnExportClicked() {
      exportData({
        status: $('#filter-status').val(),
        status_payment: $('#filter-status_payment').val(),
        date: $('#filter-date').val(),
      })
    }
  }
</script>
<?= $this->endSection('content_js') ?>