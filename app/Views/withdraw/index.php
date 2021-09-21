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
                htmlSelect([
                  'id' => 'filter-status',
                  'label' => 'Status',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => ' data-placeholder="Status Filters"',
                  'option' => $optionStatus,
                ]) .
                  htmlSelect([
                    'id' => 'filter-status_payment',
                    'label' => 'Status Payment',
                    'class' => 'select2bs4 myfilter',
                    'form_group' => 'col-4',
                    'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                    'attribute' => ' data-placeholder="Status Filters" multiple="multiple"',
                    'option' => '<option></option><option value="null" selected>None</option><option value="PENDING">Pending</option><option value="SUCCESS">Success</option><option value="FAILED" selected>Failed</option>',
                  ]) .
                  htmlInput([
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
                    <th>Payment / Action</th>
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
  <div class="modal" tabindex="-1" id="modalManualTransfer">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Transfer Manual <span></span></span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formManualTransfer">
            <div class="form-group">
              <label for="transfer_proof">Payment Details</label>
              <table>
                <?=
                htmlTr(['text' => 'Withdraw ref', 'id' => 'manual-withdraw_ref'])
                  . htmlTr(['text' => 'Method', 'id' => 'manual-payment_method'])
                  . htmlTr(['text' => 'Account Number', 'id' => 'manual-account_number'])
                  . htmlTr(['text' => 'Account Name', 'id' => 'manual-account_name'])
                ?>
              </table>
            </div>
            <div class="row">
              <?= htmlInputFile([
                'id' => 'transfer_proof',
                'label' => 'Transfer Proof',
                'class' => 'inputManualTransfer',
                'form_group' => 'col-6',
                'placeholder' => 'Choose a jpg/jpeg/png file only',
                'attribute' => 'accept="image/jpeg,image/png"',
              ]) . htmlInput([
                'id' => 'notes',
                'label' => 'Notes',
                'class' => 'form-control-border inputManualTransfer',
                'form_group' => 'col-6',
                'placeholder' => 'Enter notes about this transaction here..',

              ]) . htmlInput([
                'id' => 'user_payout_id',
                'label' => 'User Payout Id',
                'class' => 'form-control-border',
                'type' => 'hidden',

              ]) ?>
              <div class="col">
                <small><em><strong>Instruction</strong></em>: Choose <em>Transfer Proof</em> first, then fill up the <em>Notes</em></small>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnManualTransfer" disabled><i class="fas fa-check-circle"></i> Transfer</button>
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
  // const base_url = '<?= base_url() ?>';
  const path = '/withdraw';
  var errors = null;
  const inputManualTransfer = ['transfer_proof', 'notes'];

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
          d.status_payment = $('#filter-status_payment').val();
          d.date = $('#filter-date').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 5, 7, 8, 9],
        className: "text-center",
      }, {
        targets: [0, 9],
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
    datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

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
      const withdraw_ref = $(e).data('withdraw_ref');
      const user_payout_id = $(e).data('user_payout_id ');

      const title = `Confirmation`;
      const subtitle = `You are going to confirm the Withdraw for<br>
        <center><table>
        <tr><td class="text-left">Withdraw Ref</td><td> : </td><td><b>` + withdraw_ref + `</b></td></tr>
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
            datatable.ajax.reload();
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs03v', 'error');
            datatable.ajax.reload();
          })
        }
      })
    }

    // button Manual Transfer (class)
    $('body').on('click', '.btnManualTransfer', function() {
      console.log("adas");
      $('#user_payout_id').val($(this).data('user_payout_id'));
      $('#manual-withdraw_ref').text($(this).data('withdraw_ref'));
      $('#manual-payment_method').text($(this).data('method'));
      $('#manual-account_name').text($(this).data('account_name'));
      $('#manual-account_number').text($(this).data('account_number'));
      $('#modalManualTransfer').modal('show');
    });

    $('.inputManualTransfer').keyup(function() {
      btnSaveStateManualTransfer(inputManualTransfer)
    });

    function btnSaveStateManualTransfer(inputs, isFirst = false) {
      $('#btnManualTransfer').prop('disabled', !saveValidation(inputs))
      if (isFirst) clearErrors(inputs)
    }

    // button Manual Transfer (id)
    $('#btnManualTransfer').click(function() {
      const user_payout_id = $('#user_payout_id').val();
      const title = `Confirmation`;
      const subtitle = `You are going to proceed withdraw with <b>manual transfer</b><br>
        <center><table>
        <tr><td class="text-left">Withdraw Ref</td><td> : </td><td><b>${$('#manual-withdraw_ref').text()}</b></td></tr>
        <tr><td class="text-left">Method</td><td> : </td><td><b>${$('#manual-payment_method').text()}</b></td></tr>
        <tr><td class="text-left">Account Number</td><td> : </td><td><b>${$('#manual-account_number').text()}</b></td></tr>
        <tr><td class="text-left">Account Name</td><td> : </td><td><b>${$('#manual-account_name').text()}</b></td></tr>
        </table></center>
        <br>Please make sure you have already send and have transfer proof!
        <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, transfer manual`,
        showCancelButton: true,
        cancelButtonText: `<i class="fas fa-undo"></i> No, go back`,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        backdrop: `
          rgba(0,0,100,0.4)
          url("${base_url}/assets/images/warning.gif")
          right center
          no-repeat
          `,
      }).then(function(result) {
        console.log(result);
        if (result.isConfirmed) {
          $('#modalManualTransfer').modal('hide');
          let form = $('#formManualTransfer')[0];
          let data = new FormData(form);
          data.append('check_id', $('#check_id').val());
          console.log(data);
          $.ajax({
            url: base_url + path + '/manual_transfer',
            type: 'post',
            dataType: 'json',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            // cache: false,
            // async: false,
          }).done(function(response) {
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs02v', 'error');
          })
        }
      });

    })

    $('#transfer_proof').change(function(e) {
      var fileName = $("#transfer_proof")[0].files[0].name;
      var nextSibling = e.target.nextElementSibling;
      nextSibling.innerText = fileName;
    });

    function saveValidation(inputs, first = false) {
      clearErrors(inputs)
      return !checkIsInputEmpty(inputs);
    }

    <?php
    if ($search) {
      $_search = htmlspecialchars(str_replace("'", "", str_replace('"', '', $search)));
    ?>
      $('#isLoading').removeClass('d-none');
      setTimeout(() => {
        $('#isLoading').addClass('d-none');
        datatable.search('<?= $_search ?>').draw();
      }, 2000);
    <?php
    }
    ?>


  });
</script>
<?= $this->endSection('content_js') ?>