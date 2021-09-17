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
            <li class="breadcrumb-item"><a href="#">Others</a></li>
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
                  'id' => 'filter-category',
                  'label' => 'Category',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Category Filter"></i>',
                  'attribute' => 'data-placeholder="Category Filter"',
                  'option' => $optionCategory,
                ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Date',
                  'class' => 'datetimepicker myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-calendar" title="Date Range Filter"></i>',
                ]) . htmlSelect([
                  'id' => 'filter-year',
                  'label' => 'Year',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-calendar-minus" title="Year Filter"></i>',
                  'attribute' => 'data-placeholder="Year Filter"',
                  'option' => $optionYear,
                ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Date Time</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Log</th>
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

  <!-- Modal Transfer Manual -->
  <div class="modal" tabindex="-1" id="modalDetails">
    <div class="modal-dialog  modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Details</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <table>
              <?=
              htmlTr(['text' => 'Date & Time', 'id' => 'created_at'])
                . htmlTr(['text' => 'Username', 'id' => 'user'])
                . htmlTr(['text' => 'Category', 'id' => 'category'])
              ?>
            </table>
          </div>
          <div class="row" id="detail-wrapper">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/json-formatter-js/json-formatter.css">
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
<script src="<?= base_url() ?>/assets/libraries/json-formatter-js/json-formatter.umd.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
<script>
  const path = '/logs';
  var errors = null;
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder'),
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
          d.year = $('#filter-year option:selected').val();
          d.date = $('#filter-date').val();
          d.category = $('#filter-category').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 5],
        className: "text-center",
      }, {
        targets: [0, 5],
        orderable: false
      }],
      order: [
        [1, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 25, 50, 100],
      buttons: ["colvis", "pageLength"],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))
    datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('click', '.btnDetails', function(e) {
      btnDetailClicked(this)
    });

    function btnDetailClicked(e) {
      const id = $(e).data('id');
      $('#created_at').html($(e).data('created_at'));
      $('#user').html($(e).data('user'));
      $('#category').html($(e).data('category'));
      $.ajax({
        url: `${base_url}${path}/details`,
        type: "post",
        dataType: "json",
        data: {
          id: id,
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          // response.data.log.replace(/\\/g, '');
          console.log(response.data)
          const formatter = new JSONFormatter(response.data);
          $('#detail-wrapper').html(formatter.render());
          $('#modalDetails').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

  });
</script>
<?= $this->endSection('content_js') ?>