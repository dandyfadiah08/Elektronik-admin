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
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Check Date',
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
                htmlTr(['text' => 'Transaction Code', 'id' => 'manual-check_code'])
                . htmlTr(['text' => 'Method', 'id' => 'manual-payment_method'])
                . htmlTr(['text' => 'Account Number', 'id' => 'manual-account_number'])
                . htmlTr(['text' => 'Account Name', 'id' => 'manual-account_name'])
                ?>
              </table>
            </div>
            <div class="form-group">
              <label for="transfer_proof">Transfer Proof</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input inputManualTransfer" name="transfer_proof" id="transfer_proof" accept="image/jpeg,image/png">
                <label class="custom-file-label" for="transfer_proof">Choose a jpg/jpeg/png file only</label>
              </div>
            </div>
            <div class="form-group">
              <label for="notes">Notes</label>
              <input type="text" class="form-control form-control-border inputManualTransfer" name="notes" id="notes" placeholder="Enter notes about this transaction here..">
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

  <!-- Modal Confirm Appointment -->
  <div class="modal" tabindex="-1" id="modalConfirmAppointment">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Confirm Appointment</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formConfirmAppointment">
            <div class="row">
              <div class="form-group col-6">
                <label for="address_detail">Device Details</label>
                <table>
                  <?=
                  htmlTr(['text' => 'Check Code', 'id' => 'ca-check_code'])
                    . htmlTr(['text' => 'IMEI', 'id' => 'ca-imei'])
                    . htmlTr(['text' => 'Brand', 'id' => 'ca-brand'])
                    . htmlTr(['text' => 'Model', 'id' => 'ca-model'])
                    . htmlTr(['text' => 'Storage', 'id' => 'ca-storage'])
                    . htmlTr(['text' => 'Type', 'id' => 'ca-type'])
                    . htmlTr(['text' => 'Grade', 'id' => 'ca-grade'])
                    . htmlTr(['text' => 'Price', 'id' => 'ca-price'])
                    . htmlTr(['text' => 'Fullset', 'id' => 'ca-survey_fullset'])
                  ?>
                </table>
              </div>
              <div class="form-group col-6">
                <label for="address_detail">Address Details</label>
                <table>
                  <?=
                  htmlTr(['text' => 'Customer Name', 'id' => 'ca-customer_name'])
                    . htmlTr(['text' => 'Customer Phone', 'id' => 'ca-customer_phone'])
                    . htmlTr(['text' => 'Date', 'id' => 'ca-choosen_date'])
                    . htmlTr(['text' => 'Time', 'id' => 'ca-choosen_time'])
                    . htmlTr(['text' => 'Province', 'id' => 'ca-province_name'])
                    . htmlTr(['text' => 'City', 'id' => 'ca-city_name'])
                    . htmlTr(['text' => 'District', 'id' => 'ca-district_name'])
                    . htmlTr(['text' => 'Postal Code', 'id' => 'ca-postal_code'])
                    . htmlTr(['text' => 'Full Address', 'id' => 'ca-full_address'])
                  ?>
                </table>
              </div>
            </div>
            <div class="row">
              <?= htmlInput([
                'id' => 'courier_name',
                'label' => 'Courier Name',
                'class' => 'inputConfirmAppointment',
                'form_group' => 'col-6',
                'placeholder' => 'Ex. John Doe',
              ]) . htmlInput([
                'id' => 'courier_phone',
                'label' => 'Courier Phone',
                'class' => 'inputConfirmAppointment',
                'form_group' => 'col-6',
                'placeholder' => 'Ex. 62812345678',
              ]) ?>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnConfirmAppointment" disabled><i class="fas fa-check-circle"></i> Confirm Appointment</button>
        </div>
      </div>
    </div>
  </div>

  <!-- hidden and temporary input/value -->
  <input type="hidden" id="check_id">

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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>

<script>
  const path = '/transaction';
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
        targets: [0, 1, 2, 3, 4, 5, 6, 7],
        className: "text-center",
      }, {
        targets: 0,
        orderable: false
      }, {
        targets: 7,
        orderable: false
      }],
      order: [
        [1, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: ["excel", "pdf", "colvis", "pageLength"],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    // button Proceed Payment
    $('body').on('click', '.btnProceedPayment', function() {
      const check_id = $(this).data('check_id');
      const title = `Confirmation`;
      const subtitle = `You are going to proceed payment with <b>automatic transfer</b> for this transaction<br>
      <center><table>
      <tr><td class="text-left">Transaction Code</td><td> : </td><td><b>${$(this).data('check_code')}</b></td></tr>
      <tr><td class="text-left">Method</td><td> : </td><td><b>${$(this).data('payment_method')}</b></td></tr>
      <tr><td class="text-left">Account Number</td><td> : </td><td><b>${$(this).data('account_number')}</b></td></tr>
      <tr><td class="text-left">Account Name</td><td> : </td><td><b>${$(this).data('account_name')}</b></td></tr>
      </table></center>
      <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        input: 'number',
        inputAttributes: {
          autocapitalize: 'off',
          maxlength: 6,
          minlength: 6,
        },
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, proceed payment`,
        showCancelButton: true,
        cancelButtonText: `<i class="fas fa-undo"></i> No, go back`,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        backdrop: `
          rgba(0,0,100,0.4)
          url("${base_url}/assets/images/warning.gif")
          right center
          no-repeat
        `
      }).then(function(result) {
        if (result.isConfirmed) {
          let data = {
            check_id: check_id,
            codeauth : result.value,
          };
          $.ajax({
            url: base_url + path + '/proceed_payment',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            console.log(response);
            errors = response;
            let message = response.message;
            let additional_message = '';
            if (typeof response.data.errors !== 'undefined') {
              Object.values(response.data.errors).forEach(element => {
                additional_message += element + '<br>';
              });
              message += '<br>' + additional_message;
            }
            if (response.success) {
              Swal.fire('Success', message, 'success');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', message, 'error');
            }
          }).fail(function(response) {
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs01v', 'error');
          })
        }
      });
    })

    // button Manual Transfer (class)
    $('body').on('click', '.btnManualTransfer', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#manual-check_code').text($(this).data('check_code'));
      $('#manual-payment_method').text($(this).data('payment_method'));
      $('#manual-account_name').text($(this).data('account_name'));
      $('#manual-account_number').text($(this).data('account_number'));
      $('#modalManualTransfer').modal('show');
    });

    // button Manual Transfer (id)
    $('#btnManualTransfer').click(function() {
      if (!checkInputManualTransferLogic()) {
        alert('Please complete the form input!');
      } else {
        const check_id = $('#check_id').val();
        const title = `Confirmation`;
        const subtitle = `You are going to proceed payment with <b>manual transfer</b> for this transaction<br>
        <center><table>
        <tr><td class="text-left">Transaction Code</td><td> : </td><td><b>${$('#manual-check_code').text()}</b></td></tr>
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
      }
    })

    $('#transfer_proof').change(function(e) {
      var fileName = $("#transfer_proof")[0].files[0].name;
      var nextSibling = e.target.nextElementSibling;
      nextSibling.innerText = fileName;
    });

    $('.inputManualTransfer').on('change', checkInputManualTransfer);
    $('#notes').on('keyup', checkInputManualTransfer);

    function checkInputManualTransfer() {
      if (checkInputManualTransferLogic()) $('#btnManualTransfer').prop('disabled', false);
      else $('#btnManualTransfer').prop('disabled', true);
    }

    function checkInputManualTransferLogic() {
      var transfer_proof = $('#transfer_proof').val();
      var notes = $('#notes').val();
      if (transfer_proof !== undefined && notes !== '') return true;
      else return false;
    }

    // button Mark as Failed
    $('body').on('click', '.btnMarkAsFailed', function() {
      const check_id = $(this).data('check_id');
      const failed = $(this).data('failed');
      const title = `Confirmation`;
      const subtitle = `You are going to change the <b>${$(this).data('check_code')}</b> transaction status to <b>${failed}</b>.<br>
      <br>This action can not be undone.<br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, mark as ${failed}`,
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
        input: 'text',
        inputLabel: 'Notes',
        inputPlaceholder: 'Enter notes about this transaction here..',
      }).then(function(result) {
        if (result.isConfirmed) {
          let data = {
            check_id: check_id,
            notes: result.value,
          };
          $.ajax({
            url: base_url + path + '/mark_as_failed',
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
      });
    })

    // button Confirm Appointment (class)
    $('body').on('click', '.btnConfirmAppointment', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#ca-check_code').text($(this).data('check_code'));
      $.ajax({
        url: `${base_url}${path}/detail_appointment`,
        type: "post",
        dataType: "json",
        data: {
          check_id: $(this).data('check_id'),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          console.log(response.data)
          let d = response.data;
          $('#ca-imei').html(d.imei);
          $('#ca-brand').html(d.brand);
          $('#ca-model').html(d.model);
          $('#ca-storage').html(d.storage);
          $('#ca-type').html(d.type);
          $('#ca-grade').html(d.grade);
          $('#ca-price').html(d.price);
          $('#ca-survey_fullset').html(d.survey_fullset == 1 ? 'Yes' : 'No');
          $('#ca-customer_name').html(d.customer_name);
          $('#ca-customer_phone').html(d.customer_phone);
          $('#ca-choosen_time').html(d.choosen_time);
          $('#ca-choosen_date').html(d.choosen_date);
          $('#ca-province_name').html(d.province_name);
          $('#ca-city_name').html(d.city_name);
          $('#ca-district_name').html(d.district_name);
          $('#ca-postal_code').html(d.postal_code);
          $('#ca-full_address').html(d.full_address);
          $('#modalConfirmAppointment').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

    // button Confirm Appointment (id)
    $('#btnConfirmAppointment').click(function() {
      if (!checkInputConfirmAppointmentLogic()) {
        alert('Please complete the form input!');
      } else {
        const check_id = $('#check_id').val();
        const title = `Confirmation`;
        const subtitle = `You are going to confirm the appointment for<br>
        <center><table>
        <tr><td class="text-left">Transaction Code</td><td> : </td><td><b>${$('#ca-check_code').text()}</b></td></tr>
        <tr><td class="text-left">Courier Name</td><td> : </td><td><b>${$('#courier_name').val()}</b></td></tr>
        <tr><td class="text-left">Courier Phone</td><td> : </td><td><b>${$('#courier_phone').val()}</b></td></tr>
        </table></center>
        <br>Are you sure ?`;
        Swal.fire({
          title: title,
          html: subtitle,
          icon: 'info',
          confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Confirm Appointment`,
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
            let form = $('#formConfirmAppointment')[0];
            let data = new FormData(form);
            data.append('check_id', $('#check_id').val());
            console.log(data);
            $.ajax({
              url: base_url + path + '/confirm_appointment',
              type: 'post',
              dataType: 'json',
              data: data,
              enctype: 'multipart/form-data',
              processData: false,
              contentType: false,
            }).done(function(response) {
              if (response.success) {
                Swal.fire('Success', response.message, 'success');
                $('#modalConfirmAppointment').modal('hide');
                datatable.ajax.reload();
              } else {
                Swal.fire('Failed', response.message, 'error');
              }
            }).fail(function(response) {
              Swal.fire('Failed', 'Could not perform the task, please try again later. #trs04v', 'error');
            })
          }
        });
      }
    })

    $('.inputConfirmAppointment').on('keyup', checkInputConfirmAppointment);

    function checkInputConfirmAppointment() {
      $('#btnConfirmAppointment').prop('disabled', !checkInputConfirmAppointmentLogic());
    }

    function checkInputConfirmAppointmentLogic() {
      let isValid = true;
      $('.invalid-errors').html('');
      if (isInputEmpty('courier_name')) isValid = false;
      if (isInputEmpty('courier_phone')) isValid = false;

      return isValid;
    }

  });
</script>
<?= $this->endSection('content_js') ?>