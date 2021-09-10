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
            <li class="breadcrumb-item"><a href="#">Prices</a></li>
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
                    <th rowspan="2" class="v-align-middle">No</th>
                    <th colspan="2" class="text-center">Promo</th>
                    <th colspan="2" class="text-center">Date</th>
                    <th rowspan="2" class="v-align-middle">Last Updated</th>
                    <th rowspan="2" class="v-align-middle">Status / Action</th>
                  </tr>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Start</th>
                    <th>End</th>
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
            <span class="modal_add">Add Promo</span>
            <span class="modal_edit">Edit Promo</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddEdit">
            <input type="hidden" id="id">
            <?= htmlInput([
              'id' => 'promo_name',
              'label' => 'Promo Name',
              'class' => 'saveInput',
              'form_group' => '',
              'placeholder' => 'Ex. Periode Agustus',
              ]) ?>
            <?= htmlInput([
              'id' => 'start_date',
              'label' => 'Start Date',
              'class' => 'saveInput datetimepicker',
              'form_group' => '',
              'append' => '<i class="fas fa-calendar"></i>',
              'placeholder' => 'Ex. 2021-09-01',
              ]) ?>
            <?= htmlInput([
              'id' => 'end_date',
              'label' => 'End Date',
              'class' => 'saveInput datetimepicker',
              'form_group' => '',
              'append' => '<i class="fas fa-calendar"></i>',
              'placeholder' => 'Ex. 2021-09-30',
            ]) ?>
            <!-- <?= htmlCheckbox([
              'id' => 'status',
              'label' => 'Active',
              'title' => 'Checked = Active, Unchecked = Inactive',
            ]) ?> -->
            <?= htmlSwitch([
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
<script>
  const path = '/promo';
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
        targets: [0, 1, 2, 3, 4, 5, 6],
        className: "text-center",
      }, {
        targets: 0,
        orderable: false
      }, {
        targets: 6,
        orderable: false
      }],
      order: [
        [3, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [{
          text: `<i class="fas fa-plus"></i> Add`,
          action: btnAddClicked,
          className: "btn-success"
      },"excel", "pdf", "colvis", "pageLength"],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));

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
      const promo_name = $(e).data('promo_name');
      const start_date = $(e).data('start_date');
      const end_date = $(e).data('end_date');
      const status = $(e).data('status');

      $('#id').val(id);
      $('#promo_name').val(promo_name);
      $('#start_date').val(start_date);
      $('#end_date').val(end_date);
      // if(status == 1) $('#status').prop('checked', true);
      // else $('#status').prop('checked', false);
      if(status == 1)  $('#status').bootstrapSwitch('state', true)
      else $('#status').bootstrapSwitch('state', false)

      btnSaveState();
      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const promo_name = $(e).data('promo_name');
      const start_date = $(e).data('start_date');
      const end_date = $(e).data('end_date');
      const status = $(e).data('status');
      Swal.fire({
        title: `You are going to delete <b>${status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>'} Promo: <span class="text-primary">${promo_name}</span> with period <span class="text-primary">${start_date}</span> to <span class="text-primary">${end_date}</span>`,
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
      const promo_name = $('#promo_name').val();
      const start_date = $('#start_date').val();
      const end_date = $('#end_date').val();
      const status = $('#status').prop('checked') ? 1 : 2;

      if(saveValidation())
      Swal.fire({
        title: `You are going to save Promo to be:`,
        html: `<table class="mx-auto">
        <tr><td class="text-left">Promo Name</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${promo_name}</td></tr>
        <tr><td class="text-left">Start Date</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${start_date}</td></tr>
        <tr><td class="text-left">End Date</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${end_date}</td></tr>
        <tr><td class="text-left">Status</td><td>&nbsp; : &nbsp;</td><td class="text-right"> ${status == 1 ? 'Active' : 'Inactive'}</td></tr>
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
              promo_name: promo_name,
              start_date: start_date,
              end_date: end_date,
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
      const promo_name = $('#promo_name').val();
      const start_date = $('#start_date').val();
      const end_date = $('#end_date').val();
      let isValid = true;
      $('.invalid-errors').html('');
      if(promo_name == '') {
        $('[for="promo_name"]>.invalid-errors').html('required.');        
        isValid = false;
      }
      if(start_date == '') {
        $('[for="start_date"]>.invalid-errors').html('required.');        
        isValid = false;
      }
      if(end_date == '') {
        $('[for="end_date"]>.invalid-errors').html('required.');        
        isValid = false;
      }
  
      return isValid;
    }
    
  });
</script>
<?= $this->endSection('content_js') ?>