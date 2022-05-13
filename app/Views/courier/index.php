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
            <!-- <div class="card-header">
              <h3 class="card-title">DataTable with default features</h3>
            </div> -->
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Status</label>
                    <select id="filter-status" data-placeholder="Filter Status" class="form-control select2bs4 myfilter">
                      <?= $optionStatus ?>
                    </select>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group">
                    <label>Expedition</label>
                    <select id="filter-expedition" data-placeholder="Filter Expedition" class="form-control select2bs4 myfilter">
                      <?= $optionExpedition.'<option value="all" selected>All</option>' ?>
                    </select>
                  </div>
                </div>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Expedition</th>
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

  <!-- Modal Transfer Manual -->
  <div class="modal" tabindex="-1" id="modalAddEdit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span class="modal_add">Add Courier</span>
            <span class="modal_edit">Edit Courier</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddEdit">
            <?= htmlInput([
              'id' => 'id',
              'label' => 'Courier Id',
              'class' => 'saveInput',
              'form_group' => '',
              'placeholder' => 'AUTO',
              'attribute' => 'disabled',
            ]) . htmlInput([
              'id' => 'courier_name',
              'label' => 'Courier Name',
              'class' => 'saveInput',
              'form_group' => '',
              'placeholder' => 'Ex. Agus',
            ]) . htmlInput([
              'id' => 'courier_phone',
              'label' => 'Courier Phone',
              'class' => 'saveInput',
              'form_group' => '',
              'placeholder' => 'Ex. 628123xxx',
            ]) . htmlSelect([
              'id' => 'courier_expedition',
              'label' => 'Expedition',
              'class' => 'select2bs4 saveInput',
              'form_group' => '',
              'option' => $optionExpedition,
              'placeholder' => 'Chose courier expedition',
              'attribute' => 'data-placeholder="Chose courier expedition"',
            ]) . htmlSwitch([
              'id' => 'status',
              'label' => 'Status',
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
<script>
  const path = '/courier';
  var errors = null;
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $("input[data-bootstrap-switch]").bootstrapSwitch();

    $('.datetimepicker').daterangepicker({
      singleDatePicker: true,
      minYear: 2021,
      autoApply: true,
      locale: {
          format: 'YYYY-MM-DD'
      }

    });
    const accessAdd = <?= hasAccess($role, 'r_courier') ? 'true' : 'false' ?>;
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
          d.expedition = $('#filter-expedition option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 3, 4, 5, 6],
        className: "text-center",
      }, {
        targets: 0,
        orderable: false
      }, {
        targets: 6,
        orderable: false
      }],
      order: [
        [2, "asc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: ["reload", {
          text: `<i class="fas fa-plus"></i> Add`,
          action: btnAddClicked,
          className: "btn-success"+(accessAdd ? "" : " d-none")
      },"colvis", "pageLength"],
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

    function btnAddClicked() {
      $('input[type="text"]').val('');
      $('#id').val('');
      $('.modal_add').show();
      $('.modal_edit').hide();
      $('#modalAddEdit').modal('show');
    }

    function btnEditClicked(e) {
      const id = $(e).data('id');
      const courier_name = $(e).data('courier_name');
      const courier_phone = $(e).data('courier_phone');
      const courier_expedition = $(e).data('courier_expedition');
      const status = $(e).data('status');

      $('#id').val(id);
      $('#courier_name').val(courier_name);
      $('#courier_phone').val(courier_phone);
      $('#courier_expedition').val(courier_expedition);
      $('#courier_expedition').trigger('change');
      if(status == 'active')  $('#status').bootstrapSwitch('state', true)
      else $('#status').bootstrapSwitch('state', false)

      btnSaveState();
      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const courier_name = $(e).data('courier_name');
      const courier_phone = $(e).data('courier_phone');
      const courier_expedition = $(e).data('courier_expedition');
      const status = $(e).data('status');
      Swal.fire({
        title: `You are going to delete <b>${status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>'} Courier: <span class="text-primary">${courier_name}</span> with period <span class="text-primary">${courier_phone}</span> to <span class="text-primary">${courier_expedition}</span>`,
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
      const courier_name = $('#courier_name').val();
      const courier_phone = $('#courier_phone').val();
      const courier_expedition = $('#courier_expedition option:selected').val();
      const status = $('#status').prop('checked') ? 'active' : 'inactive';

      if(saveValidation())
      Swal.fire({
        title: `You are going to save Courier to be:`,
        html: `<table class="mx-auto">
        <tr><td class="text-left">Courier Name</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${courier_name}</td></tr>
        <tr><td class="text-left">Courier Phone</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${courier_phone}</td></tr>
        <tr><td class="text-left">Expedition</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${courier_expedition}</td></tr>
        <tr><td class="text-left">Status</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${status == 'active' ? 'Active' : 'Inactive'}</td></tr>
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
              courier_name: courier_name,
              courier_phone: courier_phone,
              courier_expedition: courier_expedition,
              status: status,
            }
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            if (response.success) datatable.ajax.reload();
            Swal.fire(response.message, '', class_swal);
          }).fail(function(response) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          }).always(function() {
            $('#modalAddEdit').modal('hide');
          })
        }
      });
    }

    $('.saveInput').change(btnSaveState);
    function btnSaveState() {
      let state = true;
      if(saveValidation()) state = false;
      $('#btnAddEdit').prop('disabled', state)
    }
    function saveValidation() {
      const courier_name = $('#courier_name').val();
      const courier_phone = $('#courier_phone').val();
      const courier_expedition = $('#courier_expedition option:selected').val();
      let isValid = true;
      $('.invalid-errors').html('');
      if(courier_name == '') {
        $('[for="courier_name"]>.invalid-errors').html('required.');        
        isValid = false;
      }
      if(courier_phone == '') {
        $('[for="courier_phone"]>.invalid-errors').html('required.');        
        isValid = false;
      }
      if(courier_expedition == '') {
        $('[for="courier_expedition"]>.invalid-errors').html('required.');        
        isValid = false;
      }
  
      return isValid;
    }
    
  });
</script>
<?= $this->endSection('content_js') ?>