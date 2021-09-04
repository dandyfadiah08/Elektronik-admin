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
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Commission 1</th>
                    <th>Commission 2</th>
                    <th>Commission 3</th>
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

<!-- Modals -->
<div class="modal" tabindex="-1" id="modalAddEdit" role="document">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <span class="modal_add">Add</span>
          <span class="modal_edit">Edit</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <input type="hidden" id="id">
          <div class="form-group">
            <label for="from">From</label>
            <input type="text" class="form-control" id="from" aria-describedby="codeHelp" placeholder="Ex. 0">
            <label for="to">To</label>
            <input type="text" class="form-control" id="to" aria-describedby="codeHelp" placeholder="Ex. 0">
            
            <label for="commission_1">Commission 1</label>
            <input type="text" class="form-control" id="commission_1" aria-describedby="codeHelp" placeholder="Ex. 0">
            <label for="commission_2">Commission 2</label>
            <input type="text" class="form-control" id="commission_2" aria-describedby="codeHelp" placeholder="Ex. 0">
            <label for="commission_3">Commission 3</label>
            <input type="text" class="form-control" id="commission_3" aria-describedby="codeHelp" placeholder="Ex. 0">

            <!-- <table class="table" id="table_flat_rate">
              <thead>
                <th>From</th>
                <th>To</th>
                <th>Commission 1</th>
                <th>Commission 2</th>
                <th>Commission 3</th>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <input type="text" class="form-control text-right input_price input_from" data-no="1" value="0">
                  </td>
                  <td>
                    <input type="text" class="form-control text-right input_price input_to" data-no="1" value="0">
                  </td>
                  <td>
                    <input type="text" class="form-control text-right input_price input_commission_1" data-no="1" value="0">
                  </td>
                  <td>
                    <input type="text" class="form-control text-right input_price input_commission_2" data-no="1" value="0">
                  </td>
                  <td>
                    <input type="text" class="form-control text-right input_price input_commission_3" data-no="1" value="0">
                  </td>
                  
                </tr>
              </tbody>
            </table> -->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btnSave">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
<script>
  $(document).ready(function() {
    let datatable = $("#datatable1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
        url: '<?= base_url() ?>/commissionrate/load_data',
        type: "post",
        data: function(d) {
          d.status = $('#filter-status option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 6, 7],
        className: "text-center",
      }, {
        targets: [2, 3, 4, 5],
        className: "text-right",
      }, {
        targets: 0,
        orderable: false
      }, {
        targets: 7,
        orderable: false
      }],
      order: [
        [2, "asc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [{
          text: `<i class="fas fa-plus"></i> Add`,
          action: btnAddClicked,
          className: "btn-success"
        },
        "excel", "pdf", "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));

    $('body').on('click', '.btnEdit', function(e) {
      btnEditClicked(this)
    });
    $('body').on('click', '.btnDelete', function(e) {
      btnDeleteClicked(this)
    });
    $('#btnSave').click(btnSaveClicked);

    function btnAddClicked() {
      $('input[type="text"]').val('');
      $('#id').val('0');
      $('.modal_add').show();
      $('.modal_edit').hide();
      $('#modalAddEdit').modal('show');
    }

    function btnEditClicked(e) {
      const id = $(e).data('id');
      const price_form = $(e).data('price_form');
      const price_to = $(e).data('price_to');
      const commision_1 = $(e).data('commision_1');
      const commision_2 = $(e).data('commision_2');
      const commision_3 = $(e).data('commision_3');

      
      $('#id').val(id);
      $('#from').val(price_form);
      $('#to').val(price_to);
      $('#commission_1').val(commision_1);
      $('#commission_2').val(commision_2);
      $('#commission_3').val(commision_3);
      

      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      alert('Delete')
      deleteCodesPromo(e);
    }

    function btnSaveClicked() {
      const id = $('#id').val();
      const from = $('#from').val();
      const to = $('#to').val();
      const commission_1 = $('#commission_1').val();
      const commission_2 = $('#commission_2').val();
      const commission_3 = $('#commission_3').val();

      const url = `<?= base_url() ?>/commissionrate/save`;
      $.ajax({
        // url: `${base_url}/masterpromocodes/save`,
        url: url,
        type: "post",
        dataType: "json",
        data: {
          id: id,
          from: from,
          to: to,
          commission_1: commission_1,
          commission_2: commission_2,
          commission_3: commission_3,
        }
      }).done(function(response) {

      }).fail(function(response) {

      }).always(function() {
        $('#modalAddEdit').modal('hide');
        // datatable.ajax.reload();
      })
    }

    function deleteCodesPromo(e) {
      const id = $(e).data('id');

      const url = `<?= base_url() ?>/commissionrate/delete`;
      $.ajax({
        // url: `${base_url}/masterpromocodes/save`,
        url: url,
        type: "post",
        dataType: "json",
        data: {
          id: id,
        }
      }).done(function(response) {

      }).fail(function(response) {

      }).always(function() {
        $('#modalAddEdit').modal('hide');
        // datatable.ajax.reload();
      })
    }
  });
</script>
<?= $this->endSection('content_js') ?>