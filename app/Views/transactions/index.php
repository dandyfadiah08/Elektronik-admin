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
                <div class="col">
                  <div class="form-group">
                    <label>Status</label>
                    <select id="filter-status" data-placeholder="Filter Status" class="form-control select2bs4 myfilter">
                      <?= $optionStatus ?>
                    </select>
                  </div>
                </div>
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

  <!-- hidden and temporary input/value -->
  <input type="hidden" id="check_id">
  <input type="hidden" id="check_code">
  <input type="hidden" id="payment_method">
  <input type="hidden" id="account_name">
  <input type="hidden" id="account_number">

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

<script>
  const base_url = '<?= base_url() ?>';
  const path = '/transactions';
  var errors = null;
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

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
      <tr><td class="text-left">Transaction Code</td><td>:</td><td><b>${$(this).data('check_code')}</b></td></tr>
      <tr><td class="text-left">Method</td><td>:</td><td><b>${$(this).data('payment_method')}</b></td></tr>
      <tr><td class="text-left">Account Number</td><td>:</td><td><b>${$(this).data('account_number')}</b></td></tr>
      <tr><td class="text-left">Account Name</td><td>:</td><td><b>${$(this).data('account_name')}</b></td></tr>
      </table></center>
      <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
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
            if(typeof response.data.errors !== 'undefined') {
              Object.values(response.data.errors).forEach(element => {
                additional_message += element+'<br>';
              });
              message += '<br>'+additional_message;
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
      $('#check_code').val($(this).data('check_code'));
      $('#payment_method').val($(this).data('payment_method'));
      $('#account_name').val($(this).data('account_name'));
      $('#account_number').val($(this).data('account_number'));
      $('#modalManualTransfer').modal('show');
    });

    // button Manual Transfer (id)
    $('#btnManualTransfer').click(function() {
      if(!checkInputManualTransferLogic()) {
        alert('Please complete the form input!');
      } else {
        const check_id = $('#check_id').val();
        const title = `Confirmation`;
        const subtitle = `You are going to proceed payment with <b>manual transfer</b> for this transaction<br>
        <center><table>
        <tr><td class="text-left">Transaction Code</td><td>:</td><td><b>${$('#check_code').val()}</b></td></tr>
        <tr><td class="text-left">Method</td><td>:</td><td><b>${$('#payment_method').val()}</b></td></tr>
        <tr><td class="text-left">Account Number</td><td>:</td><td><b>${$('#account_number').val()}</b></td></tr>
        <tr><td class="text-left">Account Name</td><td>:</td><td><b>${$('#account_name').val()}</b></td></tr>
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
      const title = `Confirmation`;
      const subtitle = `You are going to change the <b>${$(this).data('check_code')}</b> transaction status to <b>Failed</b>.<br>
      <br>This action can not be undone.<br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
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


  });
</script>
<?= $this->endSection('content_js') ?>