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
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => ' data-placeholder="Status Filters"',
                  'option' => $optionStatus,
                ]) .
                  htmlSelect([
                    'id' => 'filter-status_payment',
                    'label' => 'Status Payment',
                    'class' => 'select2bs4 myfilter',
                    'form_group' => 'col-sm-4',
                    'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                    'attribute' => ' data-placeholder="Status Filters" multiple="multiple"',
                    'option' => '<option></option><option value="null">None</option><option value="PENDING">Pending</option><option value="SUCCESS">Success</option><option value="FAILED">Failed</option>',
                  ]) .
                  htmlInput([
                    'id' => 'filter-date',
                    'label' => 'Withdraw Date',
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
                    <th>ID</th>
                    <th>Withdraw Ref</th>
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
                'form_group' => 'col-sm-6',
                'placeholder' => 'Choose a jpg/jpeg/png file only',
                'attribute' => 'accept="image/jpeg,image/png"',
              ]) . htmlInput([
                'id' => 'notes',
                'label' => 'Notes',
                'class' => 'form-control-border inputManualTransfer',
                'form_group' => 'col-sm-6',
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

  <!-- Modal User Details -->
  <div class="modal" tabindex="-1" id="modalViewUser">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>User Details</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formConfirmAppointment">
            <div id="printCourier">
              <div class="row">
                <div class="form-group col-6">
                  <label for="address_detail">User Details</label>
                  <table>
                    <?=
                    htmlTr(['text' => 'Name', 'id' => 'vu-name'])
                      . htmlTr(['text' => 'NIK', 'id' => 'vu-nik'])
                    ?>
                  </table>
                </div>
                <div class="col-6 device-check-image-wrapper">
                  <a id="vu-photo_id" href="<?= base_url("assets/images/photo-unavailable.png") ?>" data-magnify="gallery" data-caption="Photo ID (KTP)">
                    <span>Photo ID (KTP)</span>
                    <br>
                    <img src="<?= base_url("assets/images/photo-unavailable.png") ?>" loading="lazy" alt="" class="image-fluid device-check-image">
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer d-block">
          <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

    <!-- Modal Status Payment -->
    <div class="modal" id="modalStatusPayment">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Status Payment</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-12" id="courierView">
              <table>
                <?=
                htmlTr(['text' => 'Withdraw Ref', 'id' => 'sp-withdraw_ref'])
                  . htmlTr(['text' => 'Created', 'id' => 'sp-created_at'])
                  . htmlTr(['text' => 'Updated', 'id' => 'sp-updated_at'])
                  . htmlTr(['text' => 'Bank/Emoney', 'id' => 'sp-bank_code'])
                  . htmlTr(['text' => 'Account Name', 'id' => 'sp-account_name'])
                  . htmlTr(['text' => 'Account Number', 'id' => 'sp-account_number'])
                  . htmlTr(['text' => 'Description', 'id' => 'sp-description'])
                  . htmlTr(['text' => 'Type', 'id' => 'sp-type'])
                  . htmlTr(['text' => 'Status', 'id' => 'sp-status'])
                  . htmlTr(['text' => 'Failure', 'id' => 'sp-failure_code'])
                ?>
              </table>
            </div>
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
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/custom.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.css">
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
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>
<script>
  // const base_url = '<?= base_url() ?>';
  const path = '/withdraw';
  var errors = null;
  var _search = <?= $search ?>;
  const inputManualTransfer = ['transfer_proof', 'notes'];
  const exportAccess = <?= hasAccess($role, 'r_export_withdraw') ? 'true' : 'false' ?>;

  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $('[data-magnify]').magnify({
      resizable: false,
      initMaximized: true,
      headerToolbar: [
        'close'
      ],
    });

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
          d.status = $('#filter-status option:selected').val();
          d.status_payment = $('#filter-status_payment').val();
          d.date = $('#filter-date').val();

          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 5, 7, 8, 9, 10],
        className: "text-center",
      }, {
        targets: [0, 10],
        orderable: false
      }],
      order: [
        [1, "desc"]
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

    $('body').on('click', '.btnProceedPayment', function() {
      const btn = '#' + $(this).attr('id');
      const method = $(this).data('method');
      const account_name = $(this).data('account_name');
      const account_number = $(this).data('account_number');
      const withdraw_ref = $(this).data('withdraw_ref');
      const user_payout_id = $(this).data('user_payout_id');

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
          const thisHTML = btnOnLoading(btn);
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
            btnOnLoading(btn, false, thisHTML)
            datatable.ajax.reload();
            if (response.success) {
              changeCountBadge('withdraw_count', false);
              Swal.fire('Success', response.message, 'success');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading(btn, false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs03v', 'error');
          })

        }
      })
    })

    // button Manual Transfer (class)
    $('body').on('click', '.btnManualTransfer', function() {
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
      const btn = '#' + $(this).attr('id');
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
        const thisHTML = btnOnLoading(btn);
        console.log(result);
        if (result.isConfirmed) {
          let form = $('#formManualTransfer')[0];
          let data = new FormData(form);
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
            btnOnLoading(btn, false, thisHTML)
            if (response.success) {
              changeCountBadge('withdraw_count', false);
              Swal.fire('Success', response.message, 'success');
              datatable.ajax.reload();
              $('#modalManualTransfer').modal('hide');
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading(btn, false, thisHTML)
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

    // button View User Detail (class)
    $('body').on('click', '.btnViewUser', function() {
      $('#user_id').val($(this).data('user_id'));
      const type = $(this).data('type');
      $.ajax({
        url: `${base_url}${path}/view_user`,
        type: "post",
        dataType: "json",
        data: {
          user_id: $(this).data('user_id'),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          console.log(response.data)
          let d = response.data;
          $('#vu-name').html(`<a href="${base_url}/users/detail/${d.user_id}" title="Klik untuk lihat detail user">${d.name}</a> ${iconCopy(d.name)}`);
          $('#vu-nik').html(`${d.nik} ${iconCopy(d.nik)}`);
          $('#vu-photo_id').attr('href', d.photo_id);
          $('#vu-photo_id > img').attr('src', d.photo_id);
          $('#modalViewUser').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

    // button Status Payment (class)
    $('body').on('click', '.btnStatusPayment', function() {
      $.ajax({
        url: `${base_url}${path}/status_payment`,
        type: "post",
        dataType: "json",
        data: {
          user_payout_id: $(this).data('user_payout_id'),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          console.log(response.data)
          let d = response.data;
          $('#sp-created_at').html(d.created_at);
          $('#sp-updated_at').html(d.updated_at);
          $('#sp-withdraw_ref').html(d.withdraw_ref);
          $('#sp-bank_code').html(d.bank_code);
          $('#sp-account_name').html(d.account_name);
          $('#sp-account_number').html(d.account_number);
          $('#sp-description').html(d.description);
          $('#sp-status').html(d.status);
          $('#sp-type').html(d.type);
          $('#sp-failure_code').html(d.falure_code);
          $('#modalStatusPayment').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

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