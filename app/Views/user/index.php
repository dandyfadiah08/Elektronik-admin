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
                ]).htmlSelect([
                  'id' => 'filter-submission',
                  'label' => 'Submission',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Submission Filter"></i>',
                  'attribute' => 'data-placeholder="Submission Filter"',
                  'option' => '<option></option><option value="all">All</option><option value="1" selected>Need Review</option>',
                ]).htmlSelect([
                  'id' => 'filter-type',
                  'label' => 'Type',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-user" title="Type Filter"></i>',
                  'attribute' => 'data-placeholder="Type Filter"',
                  'option' => $optionType,
                ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Phone No</th>
                    <th>Email</th>
                    <th>Status /Action</th>
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
<div class="modal" tabindex="-1" id="modalReview" role="document">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <span class="modal_review">Review</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <img id="photo_id" class="img-fluid rounded" alt="Responsive image" style="max-height: 200px">
        </div>
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script>
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
        url: '<?= base_url() ?>/users/load_data',
        type: "post",
        data: function(d) {
          d.status = $('#filter-status option:selected').val();
          d.submission = $('#filter-submission option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 4],
        className: "text-center",
      }, {
        targets: [0,4],
        orderable: false
      }],
      order: [
        [1, "asc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [
        "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('click', '.btnReview', function(e) {
      $('#modalReview').modal('show');
      var url_photo = $(this).data('photo_id');
      $('#photo_id').attr('src', url_photo);
      // $(e).data('user_payout_id');
      console.log();
    });

    $('body').on('click', '.btnReject', function(e) {
      btnRejectClicked(this)
    });

    function btnRejectClicked(e) {
      alert('Reject')
      updateSubmission(e, 'n');
    }

    $('body').on('click', '.btnAccept', function(e) {
      btnAcceptClicked(this)
    });

    function btnAcceptClicked(e) {
      alert('Reject')
      updateSubmission(e, 'y');
    }


    function updateSubmission(e, status_submission) {
      const user_id = $(e).data('user_id');
      const url = '<?= base_url() ?>/users/updateSubmission';
      $.ajax({
        // url: `${base_url}/masterpromocodes/save`,
        url: url,
        type: "post",
        dataType: "json",
        data: {
          user_id: user_id,
          status_submission: status_submission,
        }
      }).done(function(response) {

      }).fail(function(response) {

      }).always(function() {
        $('#modalReview').modal('hide');
        datatable.ajax.reload();
      })
    }
  });
</script>
<?= $this->endSection('content_js') ?>