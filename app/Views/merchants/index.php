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
                    <th>Name</th>
                    <th>Code</th>
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
            <span class="modal_add">Add Merchant</span>
            <span class="modal_edit">Edit Merchant</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddEdit">
            <input type="hidden" id="id">
            <div class="row">
              <?= htmlInput([
                'id' => 'merchant_name',
                'label' => 'Name',
                'class' => 'saveInput',
                'form_group' => 'col-sm-6',
                'placeholder' => 'Ex. Erajaya',
                'prepend' => '<i class="fas fa-user"></i>',
              ]) . htmlInput([
                'id' => 'merchant_code',
                'label' => 'Code',
                'class' => 'saveInput',
                'type' => 'text',
                'form_group' => 'col-sm-6',
                'placeholder' => 'Ex. ABC10',
                'prepend' => '<i class="fas fa-font"></i>',
                'attribute' => 'maxlength="5"',
              ]) ?>
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
          <button type="button" class="btn btn-primary" id="btnAddEdit" disabled><i class="fas fa-save"></i> Save</button>
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
  const path = '/merchants';
  var errors = null;
  const inputs = ['merchant_name', 'merchant_code'];
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

    $('body').on('click', '.btnEdit', function() {
      $('#id').val($(this).data('id'));
      $('#merchant_name').val($(this).data('merchant_name'));
      $('#merchant_code').val($(this).data('merchant_code'));
      if ($(this).data('status') == 'active') $('#status').bootstrapSwitch('state', true)
      else $('#status').bootstrapSwitch('state', false)
      btnSaveState(true);
      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    });
    $('body').on('click', '.btnDelete', function(e) {
      btnDeleteClicked(this)
    });
    $('body').on('click', '.btnLogs', function(e) {
      window.open(`${base_url}/logs/admin/${$(this).data('id')}`)
    });
    $('#btnAddEdit').click(btnSaveClicked);

    function btnAddClicked() {
      $('input[type="text"]').val('');
      $('#id').val('');
      btnSaveState(true);
      $('.modal_add').show();
      $('.modal_edit').hide();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const merchant_name = $(e).data('merchant_name');
      const merchant_code = $(e).data('merchant_code');
      const status = $(e).data('status');
      Swal.fire({
        title: `You are going to delete <b><span class="text-${status == 'active' ? 'success' : 'danger'}">${status}</span> Merchant: <span class="text-primary">${merchant_name} (${merchant_code})</span>`,
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
            Swal.fire(response.message, '', class_swal).then(() => {
              if (response.success) datatable.ajax.reload();
            })
          }).fail(function(response) {
            Swal.fire('An error occured!', '', 'error')
            console.log(response);
          })
        }
      });
    }

    function btnSaveClicked() {
      const id = $('#id').val();
      const merchant_name = $('#merchant_name').val();
      const merchant_code = $('#merchant_code').val();
      const status = $('#status').prop('checked') ? 'active' : 'inactive';

      if (saveValidation())
        Swal.fire({
          title: `You are going to save Merchant to be:`,
          html: `<table class="mx-auto">
        <tr><td class="text-left">Merchant Name</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${merchant_name}</td></tr>
        <tr><td class="text-left">Merchant Code</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${merchant_code}</td></tr>
        <tr><td class="text-left">Status</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${status == 'active' ? 'Active' : 'Inactive'}</td></tr>
        </table><br>Click <b>Continue Update</b> to proceed, or<br><b>Close</b> to cancel this action`,
          showCancelButton: true,
          confirmButtonText: `Continue Save`,
          cancelButtonText: `Close`,
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `${base_url}${path}/save`,
              type: "post",
              dataType: "json",
              data: {
                id: id,
                merchant_name: merchant_name,
                merchant_code: merchant_code,
                status: status,
              }
            }).done(function(response) {
              var class_swal = response.success ? 'success' : 'error';
              if (response.success) {
                Swal.fire(response.message, '', class_swal)
                datatable.ajax.reload();
                $('#modalAddEdit').modal('hide');
              } else if (typeof response.data !== undefined) {
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
      $('#btnAddEdit').prop('disabled', !saveValidation())
      if (isFirst) clearErrors(inputs)
    }

    function saveValidation(first = false) {
      clearErrors(inputs)
      return !checkIsInputEmpty(inputs);
    }

  });
</script>
<?= $this->endSection('content_js') ?>