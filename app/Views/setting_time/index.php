<<<<<<< HEAD
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
            <li class="breadcrumb-item"><a href="#">Setting</a></li>
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
                htmlSelect([
                  'id' => 'filter-status',
                  'label' => 'Status',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => ' data-placeholder="Status Filters"',
                  'option' => $optionStatus,
                ]) .
                  htmlSelect([
                    'id' => 'filter-days',
                    'label' => 'Days',
                    'class' => 'select2bs4 myfilterDays',
                    'form_group' => 'col-sm-4',
                    'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                    'attribute' => ' data-placeholder="Status Filters"',
                    'option' => $optionDays,
                  ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <!-- <th>ID</th> -->
                    <th>Type</th>
                    <!-- <th>Status</th> -->
                    <th>Days</th>
                    <th>Value</th>
                    <th>Last Updated</th>
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
  const path = '/setting_time';
  var errors = null;

  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })



    $('.datetimepicker').daterangepicker({
      "showDropdowns": true,
      "minYear": 2021,
      "maxYear": <?= date('Y') ?>,
      "maxSpan": {
        "days": 60
      },
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "startDate": "<?= date('Y-m-01') ?>",
      locale: {
        format: 'YYYY-MM-DD'
      }
    });

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
          d.status = $('#filter-status option:selected').val();
          d.days = $('#filter-days option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 4, 5],
        className: "text-center",
      }, {
        targets: [0, 5],
        orderable: false
      }],
      order: [
        [3, "asc"]
      ],
      "fnDrawCallback": function() {
        $("input[data-bootstrap-switch]").bootstrapSwitch();
        console.log("reload aja");
      },
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [20, 50, 100],
      buttons: [
        "reload", "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))


    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('.myfilterDays').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('switchChange.bootstrapSwitch', '.saveInputCheck', function(e) {
      var id_widget = this.id;
      const myArr = id_widget.split("-");
      const id_time = myArr[myArr.length - 1];
      const active_time = $('#'+id_widget).prop('checked') ? 'active' : 'inactive';

      let data = {
        id_time: id_time,
        active_time: active_time,
      };
      console.log(data);

      $.ajax({
        url: `${base_url}${path}/save_time`,
        type: "post",
        dataType: "json",
        data: data,
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          Swal.fire(response.message, '', class_swal)
          datatable.ajax.reload();
          $('#modalAddEdit').modal('hide');
        } else if (Object.keys(response.data).length > 0) {
          for (const [key, value] of Object.entries(response.data)) {
            inputError(key, value)
          }
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(e);
      })

      alert("The element with idsa " + id + " changed. " + myArr.length + " - " + myArr[myArr.length - 1]);
    })

    // $('.saveInputCheck').change(function() {
    //   console.log("adas");
    // })

    // $("#FormId").change(function () {
    //       aleart('Done some change on form');
    //   }); 

    // $('.saveInputCheck').on('switch-change', function(e) {
    //   console.log(e.target.checked);
    // })


  })
</script>
=======
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
            <li class="breadcrumb-item"><a href="#">Setting</a></li>
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
                htmlSelect([
                  'id' => 'filter-status',
                  'label' => 'Status',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => ' data-placeholder="Status Filters"',
                  'option' => $optionStatus,
                ]) .
                  htmlSelect([
                    'id' => 'filter-days',
                    'label' => 'Days',
                    'class' => 'select2bs4 myfilterDays',
                    'form_group' => 'col-sm-4',
                    'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                    'attribute' => ' data-placeholder="Status Filters"',
                    'option' => $optionDays,
                  ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <!-- <th>ID</th> -->
                    <th>Type</th>
                    <!-- <th>Status</th> -->
                    <th>Days</th>
                    <th>Value</th>
                    <th>Last Updated</th>
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
  const path = '/setting_time';
  var errors = null;

  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })



    $('.datetimepicker').daterangepicker({
      "showDropdowns": true,
      "minYear": 2021,
      "maxYear": <?= date('Y') ?>,
      "maxSpan": {
        "days": 60
      },
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "startDate": "<?= date('Y-m-01') ?>",
      locale: {
        format: 'YYYY-MM-DD'
      }
    });

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
          d.status = $('#filter-status option:selected').val();
          d.days = $('#filter-days option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 4, 5],
        className: "text-center",
      }, {
        targets: [0, 5],
        orderable: false
      }],
      order: [
        [3, "asc"]
      ],
      "fnDrawCallback": function() {
        $("input[data-bootstrap-switch]").bootstrapSwitch();
        console.log("reload aja");
      },
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [20, 50, 100],
      buttons: [
        "reload", "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))


    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('.myfilterDays').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('switchChange.bootstrapSwitch', '.saveInputCheck', function(e) {
      var id_widget = this.id;
      const myArr = id_widget.split("-");
      const id_time = myArr[myArr.length - 1];
      const active_time = $('#'+id_widget).prop('checked') ? 'active' : 'inactive';

      let data = {
        id_time: id_time,
        active_time: active_time,
      };
      console.log(data);

      $.ajax({
        url: `${base_url}${path}/save_time`,
        type: "post",
        dataType: "json",
        data: data,
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          Swal.fire(response.message, '', class_swal)
          datatable.ajax.reload();
          $('#modalAddEdit').modal('hide');
        } else if (Object.keys(response.data).length > 0) {
          for (const [key, value] of Object.entries(response.data)) {
            inputError(key, value)
          }
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(e);
      })

      alert("The element with idsa " + id + " changed. " + myArr.length + " - " + myArr[myArr.length - 1]);
    })

    // $('.saveInputCheck').change(function() {
    //   console.log("adas");
    // })

    // $("#FormId").change(function () {
    //       aleart('Done some change on form');
    //   }); 

    // $('.saveInputCheck').on('switch-change', function(e) {
    //   console.log(e.target.checked);
    // })


  })
</script>
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
<?= $this->endSection('content_js') ?>