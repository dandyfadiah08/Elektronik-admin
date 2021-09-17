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
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Withdraw Date',
                  'class' => 'datetimepicker myfilter',
                  'form_group' => 'col-4',
                  'append' => '<i class="fas fa-calendar" title="Check Date Filter"></i>',
                ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Payment Type</th>
                    <th>Payment Name</th>
                    <th>Account Number</th>
                    <th>Account Name</th>
                    <th>Amount</th>
                    <th>Status</th>
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
  // const base_url = '<?= base_url() ?>';
  const path = '/withdraw';
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
          d.date = $('#filter-date').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 3, 4, 5],
        className: "text-center",
      }, {
        targets: 0,
        orderable: false
      }, {
        targets: 5,
        orderable: false
      }],
      order: [
        [1, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [
        "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));

    $('body').on('click', '.btnProceedPayment', function(e) {
      btnProcess(this)
    });

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    function btnProcess(e) {

      const method = $(e).data('method');
      const account_name = $(e).data('account_name');
      const account_number = $(e).data('account_number');

      const title = `Confirmation`;
      const subtitle = `You are going to confirm the Withdraw for<br>
        <center><table>
        <tr><td class="text-left">Method</td><td> : </td><td><b>` + method + `</b></td></tr>
        <tr><td class="text-left">Account Name</td><td> : </td><td><b>` + account_name + `</b></td></tr>
        <tr><td class="text-left">Account Number</td><td> : </td><td><b>` + account_number + `</b></td></tr>
        </table></center>
        <br>Are you sure ?`;

      Swal.fire({
        title: title,
        html: subtitle,
        input: 'number',
        inputAttributes: {
          autocapitalize: 'off',
          maxlength: 6,
          minlength: 6,
        },
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Confirm Withdraw`,
        showLoaderOnConfirm: true,
        showCancelButton: true,
        cancelButtonText: `<i class="fas fa-undo"></i> No, go back`,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
      }).then((result) => {
        if (result.isConfirmed) {

          const user_payout_id = $(e).data('user_payout_id');
          let data = {
            user_payout_id: user_payout_id,
            codeauth: result.value,
          };
          $.ajax({
            url: base_url + path + '/proceed_payment',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs03v', 'error');
          })
        }
      })



    }

  });
</script>
<?= $this->endSection('content_js') ?>