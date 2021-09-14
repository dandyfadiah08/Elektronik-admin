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
            <li class="breadcrumb-item"><a href="<?= base_url('promo') ?>"><?= $p->promo_name ?></a></li>
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
                    <th rowspan="2" class="v-align-middle">No</th>
                    <th colspan="4" class="text-center">Device</th>
                    <th colspan="2" class="text-center">Price</th>
                    <th rowspan="2" class="v-align-middle">Last Updated</th>
                    <th rowspan="2" class="v-align-middle">Action</th>
                  </tr>
                  <tr>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Storage</th>
                    <th>Type</th>
                    <th>S</th>
                    <th>Fullset</th>
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
            <div class="row">
              <?php $disabled = hasAccess($role, 'r_price') ? '' : ' disabled'; ?>
              <?=
              htmlInput([
                'id' => 'brand',
                'label' => 'Brand',
                'class' => 'saveInput',
                'form_group' => 'col-6',
                'placeholder' => 'Ex. Samsung',
                'attribute' => $disabled,
              ])
                . htmlInput([
                  'id' => 'model',
                  'label' => 'Model',
                  'class' => 'saveInput',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. SM-N980F',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'storage',
                  'label' => 'Storage',
                  'class' => 'saveInput',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 512GB',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'type',
                  'label' => 'Type',
                  'class' => 'saveInput ',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. Galaxy Note 20',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_s',
                  'label' => 'Price S',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_a',
                  'label' => 'Price A',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_b',
                  'label' => 'Price B',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_c',
                  'label' => 'Price C',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_d',
                  'label' => 'Price D',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_e',
                  'label' => 'Price E',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ])
                . htmlInput([
                  'id' => 'price_fullset',
                  'label' => 'Price Fullset',
                  'class' => 'saveInput inputPrice',
                  'form_group' => 'col-6',
                  'placeholder' => 'Ex. 1.000.000',
                  'attribute' => $disabled,
                ]) ?>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <?php if (hasAccess($role, 'r_price')) : ?>
            <button type="button" class="btn btn-primary btnAddEdit" id="btnAddEdit" disabled><i class="fas fa-save"></i> Save</button>
            <button type="button" class="btn btn-success modal_edit btnAddEdit" id="btnCopy" disabled><i class="fas fa-copy"></i> Copy</button>
          <?php endif; ?>
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
  const path = '/price';
  var errors = null;
  let inputs1 = ['brand', 'model', 'storage', 'type'];
  let inputs2 = ['price_s', 'price_a', 'price_b', 'price_c', 'price_d', 'price_e', 'price_fullset'];
  $(document).ready(function() {
    const accessAdd = <?= hasAccess($role, 'r_price') ? 'true' : 'false' ?>;
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
          d.id = <?= $p->promo_id ?>;
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
        className: "text-center",
      }, {
        targets: 0,
        orderable: false
      }, {
        targets: 8,
        orderable: false
      }],
      order: [
        [7, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [{
        text: `<i class="fas fa-plus"></i> Add`,
        action: btnAddClicked,
        className: "btn-success" + (accessAdd ? "" : " d-none")
      }, "excel", "pdf", "colvis", "pageLength"],
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
      const brand = $(e).data('brand');
      const model = $(e).data('model');
      const storage = $(e).data('storage');
      const type = $(e).data('type');
      const price_s = $(e).data('price_s');
      const price_a = $(e).data('price_a');
      const price_b = $(e).data('price_b');
      const price_c = $(e).data('price_c');
      const price_d = $(e).data('price_d');
      const price_e = $(e).data('price_e');
      const price_fullset = $(e).data('price_fullset');

      $('#id').val(id);
      $('#brand').val(brand);
      $('#model').val(model);
      $('#storage').val(storage);
      $('#type').val(type);
      $('#price_s').val(price_s);
      $('#price_a').val(price_a);
      $('#price_b').val(price_b);
      $('#price_c').val(price_c);
      $('#price_d').val(price_d);
      $('#price_e').val(price_e);
      $('#price_fullset').val(price_fullset);

      btnSaveState(true);
      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const brand = $(e).data('brand');
      const model = $(e).data('model');
      const storage = $(e).data('storage');
      const type = $(e).data('type');
      const device = `${brand} ${model} ${type} - ${type}`;
      Swal.fire({
        title: `You are going to delete Price of : <span class="text-primary">${device}</span>`,
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
      const brand = $('#brand').val();
      const model = $('#model').val();
      const storage = $('#storage').val();
      const type = $('#type').val();
      const price_s = $('#price_s').val();
      const price_a = $('#price_a').val();
      const price_b = $('#price_b').val();
      const price_c = $('#price_c').val();
      const price_d = $('#price_d').val();
      const price_e = $('#price_e').val();
      const price_fullset = $('#price_fullset').val();

      if (saveValidation())
        Swal.fire({
          title: `You are going to save Price to be:`,
          html: `<table class="mx-auto">
        <tr><td class="text-left">Brand</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${brand}</td></tr>
        <tr><td class="text-left">Model</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${model}</td></tr>
        <tr><td class="text-left">Storage</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${storage}</td></tr>
        <tr><td class="text-left">Type</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${type}</td></tr>
        <tr><td class="text-left">Price S</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_s}</td></tr>
        <tr><td class="text-left">Price A</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_a}</td></tr>
        <tr><td class="text-left">Price B</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_b}</td></tr>
        <tr><td class="text-left">Price C</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_c}</td></tr>
        <tr><td class="text-left">Price D</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_d}</td></tr>
        <tr><td class="text-left">Price E</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_e}</td></tr>
        <tr><td class="text-left">Price Fullset</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${price_fullset}</td></tr>
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
                promo_id: <?= $p->promo_id ?>,
                id: id,
                brand: brand,
                model: model,
                storage: storage,
                type: type,
                price_s: removeComma(price_s),
                price_a: removeComma(price_a),
                price_b: removeComma(price_b),
                price_c: removeComma(price_c),
                price_d: removeComma(price_d),
                price_e: removeComma(price_e),
                price_fullset: removeComma(price_fullset),
              }
            }).done(function(response) {
              var class_swal = response.success ? 'success' : 'error';
              if (response.success) datatable.ajax.reload();
              else if (typeof response.data !== undefined) {
                datatable.search(response.data).draw();
              }
              Swal.fire(response.message, '', class_swal)
            }).fail(function(response) {
              Swal.fire('An error occured!', '', 'error')
              console.log(response);
            }).always(function() {
              $('#modalAddEdit').modal('hide');
            })
          }
        });
    }

    $('.saveInput').keyup(function() {
      btnSaveState()
    });
    $('.inputPrice').keyup(function() {
      btnSaveState()
    });
    // $('.saveInput').change(btnSaveState);
    function btnSaveState(isFirst = false) {
      $('.btnAddEdit').prop('disabled', !saveValidation())
      if (isFirst) {
        clearErrors(inputs1)
        clearErrors(inputs2)
      }
    }

    function saveValidation() {
      clearErrors(inputs1)
      clearErrors(inputs2)
      const isInputEmpty = checkIsInputEmpty(inputs1)
      const isInputZero = checkIsInputZero(inputs2)

      console.log(isInputEmpty, isInputZero);
      return !isInputEmpty && !isInputZero;
    }
  });
</script>
<?= $this->endSection('content_js') ?>