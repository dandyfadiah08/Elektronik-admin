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
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'append' => '<i class="fas fa-undo-alt clearFilter" title="Click to Clear Filter" data-target="#filter-status" data-select2="true"></i>',
                  'attribute' => 'data-placeholder="Status Filter" multiple="multiple"' . ($transaction_success ? ' disabled' : ''),
                  'option' => $optionStatus,
                ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Check Date',
                  'class' => 'datepicker myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-calendar" title="Check Date Filter"></i>',
                  'append' => '<i class="fas fa-undo-alt clearFilter" title="Click to Clear Filter" data-target="#filter-date"></i>',
                ]) . htmlInput([
                  'id' => 'filter-payment_date',
                  'label' => 'Payment Date',
                  'class' => 'datepicker myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-calendar" title="Payment Date Filter"></i>',
                  'append' => '<i class="fas fa-undo-alt clearFilter" title="Click to Clear Filter" data-target="#filter-payment_date"></i>',
                ]) . htmlSelect([
                  'id' => 'filter-merchant',
                  'label' => 'Merchant',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-user-tag" title="Merchant Filter"></i>',
                  'attribute' => 'data-placeholder="Merchant Filter"',
                  'option' => $optionMerchant,
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

  <!-- Modal Appointment -->
  <div class="modal" tabindex="-1" id="modalConfirmAppointment">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Appointment</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formConfirmAppointment">
            <div id="printCourier">
              <div class="row">
                <div class="form-group col-12" id="courierView">
                  <label for="courier_detail">Courier</label>
                  <table>
                    <?=
                    htmlTr(['text' => 'Customer Name', 'id' => 'ca-courier_name'])
                      . htmlTr(['text' => 'Customer Phone', 'id' => 'ca-courier_phone'])
                    ?>
                  </table>
                </div>
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
            </div>
            <div class="row" id="paymentDetail">
              <div class="form-group col-6">
                <label for="address_detail">Payment Details</label>
                <table>
                  <?=
                  htmlTr(['text' => 'Bank/Emoney', 'id' => 'ca-bank_emoney'])
                    . htmlTr(['text' => 'Method', 'id' => 'ca-bank_code'])
                    . htmlTr(['text' => 'Account Number', 'id' => 'ca-account_number'])
                    . htmlTr(['text' => 'Account Name', 'id' => 'ca-account_name'])
                  ?>
                </table>
              </div>
              <div class="form-group col-6">
                <label for="address_detail">Payment Validation <a href="#" id="validate_bank_account" data-check_id="" data-payment_method_id="" data-account_number="" data-account_name="" title="Click here to validate payment detail"><small class="fas fa-info-circle"></small> Validate</a></label>
                <table>
                  <?=
                  htmlTr(['text' => 'Bank/Emoney', 'id' => 'cav-bank_emoney', 'class_tr' => 'd-none validate_bank_account'])
                    . htmlTr(['text' => 'Method', 'id' => 'cav-bank_code', 'class_tr' => 'd-none validate_bank_account'])
                    . htmlTr(['text' => 'Account Number', 'id' => 'cav-account_number', 'class_tr' => 'd-none validate_bank_account'])
                    . htmlTr(['text' => 'Account Name', 'id' => 'cav-account_name', 'class_tr' => 'd-none validate_bank_account'])
                    . htmlTr(['text' => 'Failure', 'id' => 'cav-failure_reason', 'class_tr' => 'validate_bank_account d-none'])
                  ?>
                </table>
              </div>
            </div>
            <div class="row" id="courierInput">
              <?= htmlInput([
                'id' => 'courier_name',
                'label' => 'Courier Name',
                'class' => 'inputConfirmAppointment',
                'form_group' => 'col-sm-6',
                'placeholder' => 'Ex. John Doe',
              ]) . htmlInput([
                'id' => 'courier_phone',
                'label' => 'Courier Phone',
                'class' => 'inputConfirmAppointment',
                'form_group' => 'col-sm-6',
                'placeholder' => 'Ex. 62812345678',
              ]) ?>
            </div>
          </form>
        </div>
        <div class="modal-footer d-block">
          <button type="button" class="btn btn-outline-success" id="btnPrint" data-target="#printCourier" title="Click to Print Address Info"><i class="fas fa-print"></i></button>
          <button type="button" class="btn btn-primary float-right" id="btnConfirmAppointment" disabled><i class="fas fa-check-circle"></i> Confirm Appointment</button>
          <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Change Payment -->
  <div class="modal" id="modalChangePayment">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Change Payment</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group">
              <label for="address_detail">Payment Details</label>
              <div class="row">
                <?= htmlSelect([
                  'id' => 'bank_emoney',
                  'label' => 'Bank/Emoney',
                  'class' => 'inputChangePayment',
                  'form_group' => 'col-sm-6',
                  'attribute' => 'data-placeholder="Bank/Emoney"',
                  'option' => '<option value="bank">Bank</option><option value="emoney">Emoney</option>',
                ]) . htmlSelect([
                  'id' => 'cp-bank_code',
                  'label' => 'Method',
                  'class' => 'select2bs4 inputChangePayment',
                  'form_group' => 'col-sm-6',
                  'attribute' => 'data-placeholder="Method"',
                  'option' => '<option></option>',
                ]) . htmlInput([
                  'id' => 'cp-account_number',
                  'label' => 'Accoaunt Number <a href="#" id="cp-validate_bank_account" data-check_id="" data-payment_method_id="" data-account_number="" data-account_name="" title="Click here to validate payment detail"><small class="fas fa-info-circle"></small> Validate</a>',
                  'class' => 'inputChangePayment',
                  'form_group' => 'col-sm-6',
                  'placeholder' => 'Ex. 62812345678',
                ]) . htmlInput([
                  'id' => 'cp-account_name',
                  'label' => 'Accoaunt Name',
                  'class' => 'inputChangePayment',
                  'form_group' => 'col-sm-6',
                  'placeholder' => 'Ex. JOhn Doe',
                ]) . htmlInput([
                  'id' => 'cp-check_code',
                  'type' => 'hidden',
                ]) ?>
                <div class="col-6 validate_bank_account d-none">
                  <label for="cp-validate_bank_account">Failure Reason</label>
                  <span class="text-danger" id="cp-failure_reason"></span>
                </div>
                <div class="col-6 validate_bank_account d-none">
                  <label for="cpv-account_name">Customer Name (Resut)</label>
                  <span class="text-danger" id="cpv-account_name" title="Click to apply"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnChangePayment" disabled><i class="fas fa-save"></i> Change Payment</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Change Address -->
  <div class="modal" id="modalChangeAddress">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Change Address</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group">
              <label for="address_detail">Address Details</label>
              <div class="row">
                <?= htmlSelect([
                  'id' => 'choose_province',
                  'label' => 'Choose Province',
                  'class' => 'select2bs4 inputChangeAddress',
                  'form_group' => 'col-sm-6',
                  'attribute' => 'data-placeholder="Choose Province"',
                  'option' => '<option></option>',
                ]) . htmlSelect([
                  'id' => 'choose_city',
                  'label' => 'Choose City',
                  'class' => 'select2bs4 inputChangeAddress',
                  'form_group' => 'col-sm-6',
                  'attribute' => 'data-placeholder="Choose City"',
                  'option' => '<option></option>',
                ]) . htmlSelect([
                  'id' => 'choose_district',
                  'label' => 'Choose District',
                  'class' => 'select2bs4 inputChangeAddress',
                  'form_group' => 'col-sm-6',
                  'attribute' => 'data-placeholder="Choose District"',
                  'option' => '<option></option>',
                ]) . htmlInput([
                  'id' => 'postal_code',
                  'label' => 'Postal Code',
                  'class' => 'inputChangeAddress',
                  'form_group' => 'col-sm-6',
                  'placeholder' => 'Ex. 123456',
                ]) . htmlInputTextArea([
                  'id' => 'full_address',
                  'label' => 'Full Address',
                  'class' => 'inputChangeAddress',
                  'form_group' => 'col',
                  'placeholder' => 'Ex. jl. in aja dulu ya',
                ]) . htmlInput([
                  'id' => 'address_id',
                  'type' => 'hidden',
                ]) . htmlInput([
                  'id' => 'cp-check_code',
                  'type' => 'hidden',
                ])
                ?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnChangeAddress"><i class="fas fa-save"></i> Change Address</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal Change Courier -->
  <div class="modal" id="modalChangeCourier">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Change Courier</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group">
              <label for="courier_name_edit">Courier Details</label>
              <div class="row">
                <?= htmlInput([
                  'id' => 'courier_name_edit',
                  'label' => 'Courier Name',
                  'class' => 'inputChangeCourier',
                  'form_group' => 'col',
                  'placeholder' => 'Ex. Abdoel Hasan',
                ]) . htmlInput([
                  'id' => 'courier_phone_edit',
                  'label' => 'Courier Phone',
                  'class' => 'inputChangeCourier',
                  'form_group' => 'col',
                  'placeholder' => 'Ex. 6289123xxxx',
                ]) . htmlInput([
                  'id' => 'cp-check_code',
                  'type' => 'hidden',
                ])
                ?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnChangeCourier"><i class="fas fa-save"></i> Change Courier</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Change Time -->
  <div class="modal" id="modalChangeTime">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Change Time</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group">
              <label for="time_edit">Time Details</label>
              <div class="row">
                <?= htmlInput([
                  'id' => 'date_edit',
                  'label' => 'Choosen date',
                  'class' => 'inputChangeDateEdit ',
                  'form_group' => 'col-sm-4',
                  'placeholder' => 'Ex. 20-09-2021',
                  'type' => 'date',
                ]) . htmlInput([
                  'id' => 'time_edit_start',
                  'label' => 'Choosen Time Start',
                  'class' => 'inputChangeDateEdit',
                  'form_group' => 'col-sm-4',
                  'placeholder' => 'Ex. 6289123xxxx',
                  'type' => 'time',
                ]) . htmlInput([
                  'id' => 'time_edit_finish',
                  'label' => 'Choosen Time Finish',
                  'class' => 'inputChangeDateEdit',
                  'form_group' => 'col-sm-4',
                  'placeholder' => 'Ex. 6289123xxxx',
                  'type' => 'time',
                ]) . htmlInput([
                  'id' => 'cp-check_code',
                  'type' => 'hidden',
                ])
                ?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnChangeTime"><i class="fas fa-save"></i> Change Appoinment Time</button>
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
                htmlTr(['text' => 'Check Code', 'id' => 'sp-check_code'])
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>

<script>
  const path = '/transaction';
  var errors = null;
  var _search = <?= $search ?>;
  const inputManualTransfer = ['transfer_proof', 'notes'];
  const inputConfirmAppointment = ['courier_name', 'courier_phone'];
  const inputChangePayment = ['cp-bank_code', 'cp-account_number', 'cp-account_name'];
  const inputChangeAddress = ['cp-bank_code', 'choose_province', 'choose_city', 'choose_district', 'postal_code', 'full_address'];
  var province_first = false;
  var city_first = false;
  const exportAccess = <?= hasAccess($role, 'r_export_transaction') ? 'true' : 'false' ?>;
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
          d.status = $('#filter-status').val();
          d.merchant = $('#filter-merchant').val();
          d.date = $('#filter-date').val();
          d.payment_date = $('#filter-payment_date').val();
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

    // button Proceed Payment
    $('body').on('click', '.btnProceedPayment', function() {
      const btn = '#' + $(this).attr('id');
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
            codeauth: result.value,
          };
          const thisHTML = btnOnLoading(btn);
          $.ajax({
            url: base_url + path + '/proceed_payment',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            btnOnLoading(btn, false, thisHTML)

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
              playSound()
              Swal.fire('Success', message, 'success');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading(btn, false, thisHTML)
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
      const btn = '#' + $(this).attr('id');
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
        const thisHTML = btnOnLoading(btn);
        console.log(result);
        if (result.isConfirmed) {
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
            btnOnLoading(btn, false, thisHTML)
            if (response.success) {
              playSound()
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
              playSound()
              changeCountBadge('transaction_count', false);
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
    $('body').on('click', '.btnAppointment', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#ca-check_code').text($(this).data('check_code'));
      const type = $(this).data('type');
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
          if (type == "confirm") {
            $('#ca-bank_emoney').html(d.bank_emoney);
            $('#ca-bank_code').html(d.bank_code);
            $('#ca-account_number').html(d.account_number);
            $('#ca-account_name').html(d.account_name);
            validateBankFirst(d, '#validate_bank_account')
            $('#paymentDetail').show();
            $('#btnConfirmAppointment').show();
            $('#courierInput').show();
            $('#courierView').hide();
          } else {
            $('#ca-courier_name').html(d.courier_name);
            $('#ca-courier_phone').html(d.courier_phone);

            $('#paymentDetail').hide();
            $('#btnConfirmAppointment').hide();
            $('#courierInput').hide();
            $('#courierView').show();
          }
          $('#modalConfirmAppointment').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

    $('#validate_bank_account').click(function() {
      validateBank('#validate_bank_account', 'cav')
    });
    $('#cp-account_number').keyup(function() {
      console.log($(this).val());
      $('#cp-validate_bank_account').attr('data-account_number', $(this).val())
    })
    $('#cp-account_name').keyup(function() {
      console.log($(this).val());
      $('#cp-validate_bank_account').attr('data-account_name', $(this).val())
    })
    $('#cp-bank_code').change(function() {
      console.log($('#cp-bank_code option:selected').val());
      $('#cp-validate_bank_account').attr('data-payment_method_id', $('#cp-bank_code option:selected').val())
    })

    function validateBank(btn, prefix, type = 'confirm-appointment') {
      const thisHTML = btnOnLoading(btn);
      $('.validate_bank_account').addClass('d-none');
      $('.' + prefix + '-failure_reason').addClass('d-none');
      $.ajax({
        url: `${base_url}${path}/validate_bank_account`,
        type: "post",
        dataType: "json",
        data: {
          check_id: $(btn).attr('data-check_id'),
          payment_method_id: $(btn).attr('data-payment_method_id'),
          account_number: $(btn).attr('data-account_number'),
          account_name: $(btn).attr('data-account_name'),
        }
      }).done(function(response) {
        $('.validate_bank_account').removeClass('d-none');
        validateBankBankProcess(response, type)
        if (response.success) {
          playSound()
          $(btn).html(bankIsValid('valid'));
        } else {
          $(btn).html(bankIsValid('invalid'));
        }
        Swal.fire(response.message, '', response.success ? 'success' : 'error')
      }).fail(function(response) {
        btnOnLoading(btn, false, thisHTML)
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })

    }

    function validateBankBankProcess(response, type = 'confirm-appointment') {
      var d = response.data.data_response;
      var isFailure = typeof d.failure_reason == 'string';
      if (type == 'confirm-appointment') {
        var account_number = $('#ca-account_number').html();
        var account_name = $('#ca-account_name').html();

        if (typeof d.bank_account_number == 'string') account_number = isFailure ? `<span class="text-danger">${d.bank_account_number}</span>` : d.bank_account_number;
        $('#cav-account_number').html(account_number);
        if (typeof d.bank_account_holder_name == 'string') account_name = isFailure || !response.success ? `<span class="text-danger">${d.bank_account_holder_name}</span>` : d.bank_account_holder_name;
        $('#cav-account_name').html(account_name);
        if (isFailure) {
          $('#cav-failure_reason').html(`<span class="text-danger">${d.failure_reason}</span>`);
          $('.validate_bank_account').removeClass('d-none');
        } else if (typeof response.data.data_update.account_bank_error == 'string') {
          $('#cav-failure_reason').html(`<span class="text-danger">${response.data.data_update.account_bank_error}</span>`);
          $('.validate_bank_account').removeClass('d-none');
        }
      } else if (type == 'change-payment') {
        var account_number = $('#cp-account_number').val();
        var account_name = $('#cp-account_name').val();

        if (typeof d.bank_account_number == 'string') account_number = isFailure ? `<span class="text-danger">${d.bank_account_number}</span>` : d.bank_account_number;
        $('#cp-account_number').val(account_number);
        if (typeof d.bank_account_holder_name == 'string') account_name = isFailure || !response.success ? d.bank_account_holder_name : d.bank_account_holder_name;
        $('#cpv-account_name').html(account_name);
        if (isFailure) {
          console.log('failure 1');
          $('#cp-failure_reason').html(`<span class="text-danger">${d.failure_reason}</span>`);
          $('.validate_bank_account').removeClass('d-none');
        } else if (typeof response.data.data_update.account_bank_error == 'string') {
          console.log('failure 2');
          $('#cp-failure_reason').html(`<span class="text-danger">${response.data.data_update.account_bank_error}</span>`);
          $('.validate_bank_account').removeClass('d-none');
        } else {
          $('.validate_bank_account').addClass('d-none');
        }
      }
    }

    function bankIsValid(status = 'pending') {
      var output = `<span class="text-primary"><small class="fas fa-info-circle"></small> Validate</span>`;
      if (status == 'valid') output = `<span class="text-success"><small class="fas fa-check-circle"></small> Valid</span>`;
      else if (status == 'invalid') output = `<span class="text-danger"><small class="fas fa-times-circle"></small> Invalid</span>`;
      return output;
    }

    function validateBankFirst(d, btn, type = 'confirm-appointment') {
      $(btn).attr('data-check_id', d.check_id);
      $(btn).attr('data-payment_method_id', d.payment_method_id);
      $(btn).attr('data-account_number', d.account_number);
      $(btn).attr('data-account_name', d.account_name);
      $('.validate_bank_account').removeClass('d-none');
      if (type == 'confirm-appointment') {
        $('#cpv-bank_emoney').html($('#ca-bank_emoney').html());
        $('#cpv-bank_code').html($('#ca-bank_code').html());
        if (d.account_bank_check == 'pending') {
          console.log('pending');
          $(btn).html(bankIsValid());
        } else if (d.account_bank_check == 'valid' || d.account_bank_check == 'invalid') {
          console.log('valid');
          $(btn).html(bankIsValid(d.account_bank_check)); // valid or invalid
          $('.validate_bank_account').removeClass('d-none');
          $('#cav-bank_emoney').html(d.bank_emoney);
          $('#cav-bank_code').html(d.bank_code);
          $('#cav-account_number').html(d.account_number);
          $('#cav-account_name').html(d.account_name_check);
          console.log(d.account_bank_error)
          if (d.account_bank_error) {
            $('#cav-failure_reason').html(`<span class="text-danger">${d.account_bank_error}</span>`);
            $('.validate_bank_account').removeClass('d-none');
          } else {
            $('#cav-failure_reason').html('');
            $('.validate_bank_account').addClass('d-none');
          }
        }
      } else if (type == 'change-payment') {
        if (d.account_bank_check == 'pending') {
          console.log('pending');
          $(btn).html(bankIsValid());
        } else if (d.account_bank_check == 'valid' || d.account_bank_check == 'invalid') {
          $(btn).html(bankIsValid(d.account_bank_check));
          if (d.account_bank_error) {
            $('#cp-failure_reason').html(`<span class="text-danger">${d.account_bank_error}</span>`);
            $('.validate_bank_account').removeClass('d-none');
          } else {
            $('#cp-failure_reason').html('');
            $('.validate_bank_account').addClass('d-none');
          }
        }
      }
    }
    // button Confirm Appointment (id)
    $('#btnConfirmAppointment').click(function() {
      const thisHTML = btnOnLoading('#btnConfirmAppointment')

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
          console.log('data');
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
            btnOnLoading('#btnConfirmAppointment', false, thisHTML)
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              $('#modalConfirmAppointment').modal('hide');
              datatable.ajax.reload();
              changeCountBadge('transaction_count', false);
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading('#btnConfirmAppointment', false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs04v', 'error');
          })
        } else {
          btnOnLoading('#btnConfirmAppointment', false, thisHTML)
        }
      });
    })

    // button Change Payment (class)
    $('body').on('click', '.btnChangePayment', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#cp-check_code').text($(this).data('check_code'));
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
          // console.log(response.data)
          let d = response.data;
          $('#bank_emoney').val(d.bank_emoney);
          changeBank(d.payment_method_id)
          $('#cp-account_number').val(d.account_number);
          $('#cp-account_name').val(d.account_name);
          $('#cp-validate_bank_account').attr('data-check_id', d.check_id);
          $('#cp-validate_bank_account').attr('data-payment_method_id', d.payment_method_id);
          $('#cp-validate_bank_account').attr('data-account_number', d.account_number);
          $('#cp-validate_bank_account').attr('data-account_name', d.account_name);
          validateBankFirst(d, '#cp-validate_bank_account', 'change-payment')

          $('#modalChangePayment').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

    // button Confirm Appointment (class)
    $('body').on('click', '.btnChangeAddress', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#cp-check_code').text($(this).data('check_code'));
      $('#address_id').val($(this).data('address_id'));
      province_first = false;
      city_first = false;

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
          // console.log(response.data)
          let d = response.data;
          changeProvince(d.province_id, d.city_id, d.district_id);
          $('#postal_code').val(d.postal_code);
          $('#full_address').text(d.full_address);

          $('#modalChangeAddress').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

    // button Confirm Appointment (class)
    $('body').on('click', '.btnChangeCourier', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#cp-check_code').text($(this).data('check_code'));

      $('#courier_name_edit').val($(this).data('courier_name'));
      $('#courier_phone_edit').val($(this).data('courier_phone'));

      // console.log('data = ', $(this).data('courier_name'), $(this).data('courier_phone'));

      $('#modalChangeCourier').modal('show');


    });

    $('body').on('click', '.btnChangeTime', function() {
      $('#check_id').val($(this).data('check_id'));
      $('#cp-check_code').text($(this).data('check_code'));

      $('#date_edit').val($(this).data('choosen_date'));

      $('#time_edit_start').val($(this).data('time_start'));
      $('#time_edit_finish').val($(this).data('time_last'));
      // $('#courier_phone_edit').val($(this).data('courier_phone'));

      // console.log('data = ', $(this).data('courier_name'), $(this).data('courier_phone'));

      $('#modalChangeTime').modal('show');


    });

    $('#bank_emoney').change(function() {
      changeBank();
    });

    function changeBank(id = 0) {
      $.ajax({
        url: `${base_url}${path}/payment_method`,
        type: "post",
        dataType: "json",
        data: {
          type: $('#bank_emoney option:selected').val(),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          // console.log(response.data)
          var html = ``;
          response.data.forEach(value => {
            html += `<option value="${value.payment_method_id}" ${value.payment_method_id == id ? 'selected' : ''}>${value.name} / ${value.alias_name}</option>`;
          })
          $('#cp-bank_code').html(html);
          $('#cp-bank_code').trigger('change')
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    function changeProvince(id = 0, idCity = 0, idDistrict = 0) {
      $.ajax({
        url: `${base_url}/api/general/getProvinces`,
        type: "post",
        dataType: "json",
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          // console.log(response.data);
          var html = ``;
          response.data.forEach(value => {
            html += `<option value="${value.province_id}" ${value.province_id == id? 'selected' : ''}>${value.name}</option>`;
            // console.log(html);
          })
          $('#choose_province').html(html);
          $('#choose_province').trigger('change');
          console.log("idCity = ", idCity, " idDistrict = ", idDistrict);
          changeCity(idCity, idDistrict);
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    $('#choose_province').change(function() {
      if (province_first) province_first = true;
      else changeCity();
    });

    $('#choose_city').change(function() {
      if (city_first) city_first = true;
      else changeDistrict();
    });

    function changeCity(id = 0, idDistrict = 0) {
      $.ajax({
        url: `${base_url}/api/general/getCities`,
        type: "post",
        dataType: "json",
        data: {
          province_id: $('#choose_province option:selected').val(),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          // console.log(response.data)
          var html = ``;
          response.data.forEach(value => {
            html += `<option value="${value.city_id}" ${value.city_id == id ? 'selected' : ''}>${value.name}</option>`;
          })
          $('#choose_city').html(html);
          $('#choose_city').trigger('change')
          console.log("idDistrict = ", idDistrict);
          changeDistrict(idDistrict);
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    function changeDistrict(id = 0) {
      $.ajax({
        url: `${base_url}/api/general/getDistrict`,
        type: "post",
        dataType: "json",
        data: {
          city_id: $('#choose_city option:selected').val(),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          // console.log(response.data)
          var html = ``;
          response.data.forEach(value => {
            html += `<option value="${value.district_id}" ${value.district_id == id ? 'selected' : ''}>${value.name}</option>`;
          })
          console.log("idDistrict = ", id);
          $('#choose_district').html(html);
          $('#choose_district').trigger('change')
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    $('#cp-validate_bank_account').click(function() {
      validateBank('#cp-validate_bank_account', 'cp', 'change-payment')
    });

    $('#cpv-account_name').click(function() {
      $('#cp-account_name').val($(this).html())
      $('#cp-validate_bank_account').attr('data-account_name', $(this).html())
      btnSaveStateChangePayment(inputChangePayment)
    })

    // button Change Payment (id)
    $('#btnChangePayment').click(function() {
      const thisHTML = btnOnLoading('#btnChangePayment')

      const check_id = $('#check_id').val();
      const title = `Confirmation`;
      const subtitle = `You are going to change payment for <b class="text-primary">${$('#cp-check_code').text()}</b> become<br>
        <center><table>
        <tr><td class="text-left">Bank/Emoney</td><td> : </td><td><b>${$('#bank_emoney option:selected').html()}</b></td></tr>
        <tr><td class="text-left">Method</td><td> : </td><td><b>${$('#cp-bank_code option:selected').html()}</b></td></tr>
        <tr><td class="text-left">Account Number</td><td> : </td><td><b>${$('#cp-account_number').val()}</b></td></tr>
        <tr><td class="text-left">Account Name</td><td> : </td><td><b>${$('#cp-account_name').val()}</b></td></tr>
        </table></center>
        <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Change Payment`,
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
          let data = {
            check_id: $('#check_id').val(),
            payment_method_id: $('#cp-bank_code option:selected').val(),
            account_number: $('#cp-account_number').val(),
            account_name: $('#cp-account_name').val(),
          };
          console.log('data');
          console.log(data);
          $.ajax({
            url: base_url + path + '/change_payment',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            btnOnLoading('#btnChangePayment', false, thisHTML)
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              $('#modalChangePayment').modal('hide');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading('#btnChangePayment', false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs05v', 'error');
          })
        } else {
          btnOnLoading('#btnChangePayment', false, thisHTML)
        }
      });
    })


    // button Change Address (id)
    $('#btnChangeAddress').click(function() {
      const thisHTML = btnOnLoading('#btnChangeAddress')

      const check_id = $('#check_id').val();
      const title = `Confirmation`;
      const subtitle = `You are going to change address for <b class="text-primary">${$('#cp-check_code').text()}</b> become<br>
        <center><table>
        <tr><td class="text-left">Province</td><td> : </td><td><b>${$('#choose_province option:selected').html()}</b></td></tr>
        <tr><td class="text-left">City</td><td> : </td><td><b>${$('#choose_city option:selected').html()}</b></td></tr>
        <tr><td class="text-left">District</td><td> : </td><td><b>${$('#choose_district option:selected').html()}</b></td></tr>
        <tr><td class="text-left">Postal Code</td><td> : </td><td><b>${$('#postal_code').val()}</b></td></tr>
        <tr><td class="text-left">Full Address</td><td> : </td><td><b>${$('#full_address').val()}</b></td></tr>
        </table></center>
        <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Change Address`,
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
          let data = {
            check_id: $('#check_id').val(),
            address_id: $('#address_id').val(),
            province_id: $('#choose_province option:selected').val(),
            city_id: $('#choose_city option:selected').val(),
            district_id: $('#choose_district option:selected').val(),
            postal_code: $('#postal_code').val(),
            full_address: $('#full_address').val(),
          };
          console.log('data');
          console.log(data);
          $.ajax({
            url: base_url + path + '/change_address',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            btnOnLoading('#btnChangeAddress', false, thisHTML)
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              $('#modalChangeAddress').modal('hide');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading('#btnChangeAddress', false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs05v', 'error');
          })
        } else {
          btnOnLoading('#btnChangeAddress', false, thisHTML)
        }
      });
    })

    // button Change Address (id)
    $('#btnChangeCourier').click(function() {
      const thisHTML = btnOnLoading('#btnChangeCourier')

      const check_id = $('#check_id').val();
      const title = `Confirmation`;
      const subtitle = `You are going to change courier for <b class="text-primary">${$('#cp-check_code').text()}</b> become<br>
        <center><table>
        <tr><td class="text-left">Courier Name</td><td> : </td><td><b>${$('#courier_name_edit').val()}</b></td></tr>
        <tr><td class="text-left">Courier Phone</td><td> : </td><td><b>${$('#courier_phone_edit').val()}</b></td></tr>
        </table></center>
        <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Change Courier`,
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
          let data = {
            check_id: $('#check_id').val(),
            courier_name: $('#courier_name_edit').val(),
            courier_phone: $('#courier_phone_edit').val(),
          };
          console.log('data');
          console.log(data);
          $.ajax({
            url: base_url + path + '/change_courier',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            btnOnLoading('#btnChangeCourier', false, thisHTML)
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              $('#modalChangeCourier').modal('hide');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading('#btnChangeCourier', false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs05v', 'error');
          })
        } else {
          btnOnLoading('#btnChangeCourier', false, thisHTML)
        }
      });
    })

    // button Change Address (id)
    $('#btnChangeTime').click(function() {
      const thisHTML = btnOnLoading('#btnChangeTime')

      const check_id = $('#check_id').val();
      const title = `Confirmation`;
      const choosenDate = $('#date_edit').val();
      var newDate = new Date(choosenDate);
      var newFormat = ("0" + newDate.getDate()).slice(-2) + "-" +
        ("0" + (newDate.getMonth() + 1)).slice(-2) + "-" +
        newDate.getFullYear();

      var timeStart = $('#time_edit_start').val();
      timeStart = timeStart.replace(":", ".");
      var timeEnd = $('#time_edit_finish').val();
      timeEnd = timeEnd.replace(":", ".");
      const choosenTime = timeStart + "-" + timeEnd;
      const subtitle = `You are going to change Appoinment Time for <b class="text-primary">${$('#cp-check_code').text()}</b> become<br>
        <center><table>
        <tr><td class="text-left">Choosen date</td><td> : </td><td><b>${newFormat}</b></td></tr>
        <tr><td class="text-left">Choosen time</td><td> : </td><td><b>${choosenTime}</b></td></tr>
        </table></center>
        <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Change Appoinment Time`,
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
          let data = {
            check_id: $('#check_id').val(),
            choosen_date: newFormat,
            choosen_time: choosenTime,
          };
          console.log('data');
          console.log(data);
          $.ajax({
            url: base_url + path + '/change_time',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            btnOnLoading('#btnChangeTime', false, thisHTML)
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              $('#modalChangeTime').modal('hide');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading('#btnChangeTime', false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #trs05v', 'error');
          })
        } else {
          btnOnLoading('#btnChangeTime', false, thisHTML)
        }
      });
    })


    $('.inputChangePayment').keyup(function() {
      btnSaveStateChangePayment(inputChangePayment)
    });

    function btnSaveStateChangePayment(inputs, isFirst = false) {
      $('#btnChangePayment').prop('disabled', !saveValidation(inputs))
      if (isFirst) clearErrors(inputs)
    }



    $('.inputManualTransfer').keyup(function() {
      btnSaveStateManualTransfer(inputManualTransfer)
    });

    function btnSaveStateManualTransfer(inputs, isFirst = false) {
      $('#btnManualTransfer').prop('disabled', !saveValidation(inputs))
      if (isFirst) clearErrors(inputs)
    }

    $('.inputConfirmAppointment').keyup(function() {
      btnSaveStateConfirmAppointment(inputConfirmAppointment)
    });

    function btnSaveStateConfirmAppointment(inputs, isFirst = false) {
      $('#btnConfirmAppointment').prop('disabled', !saveValidation(inputs))
      if (isFirst) clearErrors(inputs)
    }

    function saveValidation(inputs, first = false) {
      clearErrors(inputs)
      return !checkIsInputEmpty(inputs);
    }

    $('#btnPrint').click(function() {
      const target = $(this).data('target');
      popupPrint($(target).html());
    })

    // button Status Payment (class)
    $('body').on('click', '.btnStatusPayment', function() {
      $('#check_id').val($(this).data('check_id'));
      $.ajax({
        url: `${base_url}${path}/status_payment`,
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
          $('#sp-created_at').html(d.created_at);
          $('#sp-updated_at').html(d.updated_at);
          $('#sp-check_code').html(d.check_code);
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
  });

  if (exportAccess) {
    function btnExportClicked() {
      exportData({
        status: $('#filter-status').val(),
        merchant: $('#filter-merchant').val(),
        date: $('#filter-date').val(),
      })
    }
  }
</script>
<?= $this->endSection('content_js') ?>