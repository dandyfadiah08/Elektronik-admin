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
            <?= htmlInput([
              'id' => 'from',
              'label' => 'From',
              'class' => 'inputPrice',
              'prepend' => 'IDR',
              'placeholder' => 'Ex. 1.000.000',
            ]) ?>
            <?= htmlInput([
              'id' => 'to',
              'label' => 'To',
              'class' => 'inputPrice',
              'prepend' => 'IDR',
              'placeholder' => 'Ex. 1.000.000',
            ]) ?>
            <?= htmlInput([
              'id' => 'commission_1',
              'label' => 'Commission 1',
              'class' => 'inputPrice',
              'prepend' => 'IDR',
              'placeholder' => 'Ex. 1.000.000',
            ]) ?>
            <?= htmlInput([
              'id' => 'commission_2',
              'label' => 'Commission 2',
              'class' => 'inputPrice',
              'prepend' => 'IDR',
              'placeholder' => 'Ex. 1.000.000',
            ]) ?>
            <?= htmlInput([
              'id' => 'commission_3',
              'label' => 'Commission 3',
              'class' => 'inputPrice',
              'prepend' => 'IDR',
              'placeholder' => 'Ex. 1.000.000',
            ]) ?>

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
  const path = '/commission_rate';
  $(document).ready(function() {
    let datatable = $("#datatable1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
        url: `${base_url}${path}/load_data`,
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
      const price_from = $(e).data('price_from');
      const price_to = $(e).data('price_to');
      const commission_1 = $(e).data('commission_1');
      const commission_2 = $(e).data('commission_2');
      const commission_3 = $(e).data('commission_3');

      $('#id').val(id);
      $('#from').val(price_from);
      $('#to').val(price_to);
      $('#commission_1').val(commission_1);
      $('#commission_2').val(commission_2);
      $('#commission_3').val(commission_3);

      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const price_from = $(e).data('price_from');
      const price_to = $(e).data('price_to');
      Swal.fire({
        title: `You are going to delete Commission Rate: IDR ${price_from} to IDR ${price_to}`,
        html: `Click <b>Continue Delete</b> to proceed, or<br><b>Close</b> to cancel this action`,
        showCancelButton: true,
        confirmButtonText: `Continue Delete`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          deleteCommissionRate(id);
        }
      });
    }

    function btnSaveClicked() {
      const id = $('#id').val();
      const from = $('#from').val();
      const to = $('#to').val();
      const commission_1 = $('#commission_1').val();
      const commission_2 = $('#commission_2').val();
      const commission_3 = $('#commission_3').val();

      Swal.fire({
        title: `You are going to save Commission Rate to be:`,
        html: `<table class="mx-auto">
        <tr><td class="text-left">From</td><td>&nbsp; : IDR &nbsp;</td><td class="text-right"> ${from}</td></tr>
        <tr><td class="text-left">To</td><td>&nbsp; : IDR &nbsp;</td><td class="text-right"> ${to}</td></tr>
        <tr><td class="text-left">Commission 1</td><td>&nbsp; : IDR &nbsp;</td><td class="text-right"> ${commission_1}</td></tr>
        <tr><td class="text-left">Commission 2</td><td>&nbsp; : IDR &nbsp;</td><td class="text-right"> ${commission_2}</td></tr>
        <tr><td class="text-left">Commission 3</td><td>&nbsp; : IDR &nbsp;</td><td class="text-right"> ${commission_3}</td></tr>
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
              from: from,
              to: to,
              commission_1: commission_1,
              commission_2: commission_2,
              commission_3: commission_3,
            }
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            Swal.fire(response.message, '', class_swal).then(() => {
              if (response.success) datatable.ajax.reload();
            })
          }).fail(function(response) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          }).always(function() {
            $('#modalAddEdit').modal('hide');
          })
        }
      });
    }

    function deleteCommissionRate(id) {
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
</script>
<?= $this->endSection('content_js') ?>