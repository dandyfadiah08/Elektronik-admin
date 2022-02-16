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
            <li class="breadcrumb-item"><a href="#">Master</a></li>
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
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Role Name</th>
                    <th>Admin Count</th>
                    <th>Last Updated</th>
                    <th>Status / Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add EDit -->
  <div class="modal" id="modalAddEdit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span class="modal_add">Add Role</span>
            <span class="modal_edit">Edit Role</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddEdit">
            <input type="hidden" id="id">
            <div class="row border-bottom">
              <?= htmlInput([
                'id' => 'role_name',
                'label' => 'Role Name',
                'class' => 'saveInput',
                'form_group' => 'col-12',
                'placeholder' => 'Ex. Finance, CS 24 Hours',
                'prepend' => '<i class="fas fa-user-shield"></i>',
              ]) ?>
            </div>
            <label>Master</label>
            <div class="row">
              <?= htmlCheckbox([
                'id' => 'r_admin',
                'label' => 'Admin',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_admin_role',
                'label' => 'Admin Role',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_user',
                'label' => 'User',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_merchant',
                'label' => 'Merchant',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_promo',
                'label' => 'Promo',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-exclude="r_promo_view"',
              ]) . htmlCheckbox([
                'id' => 'r_promo_view',
                'label' => 'Promo (view)',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-exclude="r_promo"',
              ]) . htmlCheckbox([
                'id' => 'r_price',
                'label' => 'Price',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-exclude="r_price_view" data-include="r_promo"',
              ]) . htmlCheckbox([
                'id' => 'r_price_view',
                'label' => 'Price (view)',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-exclude="r_price" data-include="r_promo_view"',
              ]) . htmlCheckbox([
                'id' => 'r_commission_rate',
                'label' => 'Commission Rate',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) ?>
            </div>
            <label>Device Check</label>
            <div class="row">
              <?= htmlCheckbox([
                'id' => 'r_device_check',
                'label' => 'Device Check',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_review',
                'label' => 'Review (Grading)',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_device_check"',
              ]) . htmlCheckbox([
                'id' => 'r_change_grade',
                'label' => 'Change Grade',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_device_check"',
              ])
              ?>
            </div>
            <label>Finance</label>
            <div class="row">
              <?= htmlCheckbox([
                'id' => 'r_transaction',
                'label' => 'Transaction',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_device_check"',
              ]) . htmlCheckbox([
                'id' => 'r_transaction_success',
                'label' => 'Transaction Success (view)',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-exclude="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_withdraw',
                'label' => 'Withdraws',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_bonus_view',
                'label' => 'Agent Bonus',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_request_payment',
                'label' => 'Request Payment',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_proceed_payment',
                'label' => 'Proceed Payment',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_manual_transfer',
                'label' => 'Manual Transfer',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_change_payment',
                'label' => 'Change Payment',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_change_address',
                'label' => 'Change Address',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_balance',
                'label' => 'View Balance',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_tax',
                'label' => 'Tax Data',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ])
              ?>
            </div>
            <label>Settings</label>
            <div class="row">
              <?= htmlCheckbox([
                'id' => 'r_2fa',
                'label' => 'Google Authenticator',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_change_setting',
                'label' => 'Change Setting',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_change_available_date_time',
                'label' => 'Available Date Time',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ])
              ?>
            </div>
            <label>Exports</label>
            <div class="row">
              <?= htmlCheckbox([
                'id' => 'r_export_user',
                'label' => 'User',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_user"',
              ]) . htmlCheckbox([
                'id' => 'r_export_device_check',
                'label' => 'Device Check',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_device_check"',
              ]) . htmlCheckbox([
                'id' => 'r_export_transaction',
                'label' => 'Transaction',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_export_withdraw',
                'label' => 'Withdraw',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_withdraw"',
              ]) . htmlCheckbox([
                'id' => 'r_export_bonus',
                'label' => 'Agent Bonus',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_bonus_view"',
              ]) . htmlCheckbox([
                'id' => 'r_export_tax',
                'label' => 'Tax Data',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_tax"',
              ])
              ?>
            </div>
            <label>Others</label>
            <div class="row">
              <?= htmlCheckbox([
                'id' => 'r_logs',
                'label' => 'Logs',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ])
              ?>
            </div>
            <label>Actions</label>
            <div class="row border-bottom">
              <?= htmlCheckbox([
                'id' => 'r_confirm_appointment',
                'label' => 'Confirm Appointment',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_mark_as_failed',
                'label' => 'Mark as Failed',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_transaction"',
              ]) . htmlCheckbox([
                'id' => 'r_submission',
                'label' => 'Submission',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_user"',
              ]) . htmlCheckbox([
                'id' => 'r_send_bonus',
                'label' => 'Send Bonus',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
                'attribute' => 'data-include="r_bonus_view"',
              ])
              ?>
            </div>
            <label>View</label>
            <div class="row border-bottom">
              <?= htmlCheckbox([
                'id' => 'r_view_photo_id',
                'label' => 'Photo ID',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_view_phone_no',
                'label' => 'Phone No',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_view_email',
                'label' => 'Email',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_view_payment_detail',
                'label' => 'Payment Detail',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ]) . htmlCheckbox([
                'id' => 'r_view_address',
                'label' => 'Address',
                'class' => 'saveInput roleCheck',
                'form_group' => 'col-sm-3',
              ])
              ?>
            </div>
            <?= htmlSwitch([
              'id' => 'status',
              'label' => 'Status',
              'class' => 'saveInput',
              'on' => 'ACTIVE',
              'off' => 'INACTIVE',
            ]) ?>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary btnAddEdit" id="btnAddEdit" disabled><i class="fas fa-save"></i> Save</button>
          <button type="button" class="btn btn-success btnAddEdit modal_edit" id="btnCopy" disabled><i class="fas fa-copy"></i> Copy</button>
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
  const path = '/admin_role';
  var errors = null;
  const inputs = ['role_name'];
  const roles = [<?= $roles ?>];
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $("input[data-bootstrap-switch]").bootstrapSwitch();

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
          d.notification = $('#filter-notification option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 4],
        className: "text-center",
      }, {
        targets: [0, 4],
        orderable: false
      }],
      order: [
        [1, "asc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: ["reload", {
        text: `<i class="fas fa-plus"></i> Add`,
        action: btnAddClicked,
        className: "btn-success"
      }, "colvis", "pageLength"],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('click', '.btnEdit', function(e) {
      btnEditClicked(this)
    });
    $('body').on('click', '.btnDelete', function(e) {
      btnDeleteClicked(this)
    });
    $('#btnAddEdit').click(btnSaveClicked);
    $('#btnCopy').click(function() {
      $('#id').val('');
      btnSaveClicked()
    });

    function btnAddClicked() {
      $('input[type="text"]').val('');
      $('#id').val('');
      btnSaveState(true);
      $('.modal_add').show();
      $('.modal_edit').hide();
      $('#modalAddEdit').modal('show');
    }

    function btnEditClicked(e) {
      const id = $(e).data('id');

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
          $('#id').val(id);
          $('#role_name').val(response.data.role_name);
          roles.forEach(role => {
            parseRole(role, response.data);
          });
          if (response.data.status == 'active') $('#status').bootstrapSwitch('state', true)
          else $('#status').bootstrapSwitch('state', false)

          btnSaveState(true);
          $('.modal_add').hide();
          $('.modal_edit').show();
          $('#modalAddEdit').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    function parseRole(role, data) {
      $('#' + role).prop('checked', data[role] == 'y');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const role_name = $(e).data('role_name');
      const status = $(e).data('status');
      Swal.fire({
        title: `You are going to delete <b><span class="text-${status == 'active' ? 'success' : 'danger'}">${status}</span> Role: <span class="text-primary">${role_name}</span>`,
        html: `Click <b>Continue Delete</b> to proceed, or<br><b>Close</b> to cancel this action`,
        showCancelButton: true,
        confirmButtonText: `Continue Delete`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `${base_url}${path}/delete`,
            type: "post",
            dataType: "json",
            data: {
              id: id,
            }
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            if (response.success) datatable.ajax.reload();
            Swal.fire(response.message, '', class_swal);
          }).fail(function(response) {
            Swal.fire('An error occured!', '', 'error')
            console.log(response);
          })
        }
      });
    }

    function btnSaveClicked() {
      const id = $('#id').val();
      const role_name = $('#role_name').val();
      const status = $('#status').prop('checked') ? 'active' : 'inactive';

      if (saveValidation())
        Swal.fire({
          title: `You are going to save Admin Role ${role_name} ${status == 'active' ? 'Active' : 'Inactive'}`,
          html: `Click <b>Continue Update</b> to proceed, or<br><b>Close</b> to cancel this action`,
          showCancelButton: true,
          confirmButtonText: `Continue Save`,
          cancelButtonText: `Close`,
        }).then((result) => {
          if (result.isConfirmed) {
            let data = {
              id: id,
              role_name: role_name,
              status: status,
            };
            roles.forEach(role => {
              data[role] = $('#' + role).prop('checked') ? 1 : 0;
            });
            console.log(data);
            $.ajax({
              url: `${base_url}${path}/save`,
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
          }
        });
    }

    $('.saveInput').keyup(function() {
      btnSaveState()
    });
    $('.saveInput').change(function() {
      btnSaveState()
    });

    function btnSaveState(isFirst = false) {
      $('.btnAddEdit').prop('disabled', !saveValidation())
      if (isFirst) clearErrors(inputs)
    }

    function saveValidation(first = false) {
      clearErrors(inputs)
      return !checkIsInputEmpty(inputs) && checkIfChecked('.roleCheck');
    }

    $('.roleCheck').change(function() {
      roleCheckInclude($(this).prop('id'))
      roleCheckInclude($(this).prop('id'), false)
    });

    function roleCheckInclude(id, include = true) {
      const _this = '#' + id;
      if ($(_this).prop('checked') == true) {
        let source = include ? 'include' : 'exclude';
        const target = $(_this).data(source);
        const targets = typeof target == 'undefined' ? [] : target.split(',');
        targets.forEach(value => {
          $('#' + value).prop('checked', include)
          $('#' + value).trigger('change')
        });
      }
    }
  });
</script>
<?= $this->endSection('content_js') ?>