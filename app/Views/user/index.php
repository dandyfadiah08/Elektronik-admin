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
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ]) . htmlSelect([
                  'id' => 'filter-submission',
                  'label' => 'Submission',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Submission Filter"></i>',
                  'attribute' => 'data-placeholder="Submission Filter"',
                  'option' => '<option></option><option value="all">All</option><option value="1" selected>Need Review</option>',
                ]) . htmlSelect([
                  'id' => 'filter-type',
                  'label' => 'Type',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-4',
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
        <div class="row">
          <div class="col-3">Name</div>
          <div class="col-1"> : </div>
          <div class="col-8" id="name"></div>
          <div class="col-3">NIK</div>
          <div class="col-1"> : </div>
          <div class="col-8" id="nik"></div>
          <div class="col-12 text-center">
            <a href="<?= base_url('/assets/images/photo-unavailable.png') ?>" data-magnify="gallery" data-caption="Photo ID / KTP">
              <img src="<?= base_url('/assets/images/photo-unavailable.png') ?>" id="photo_id" class="img-fluid rounded" alt="Responsive image" style="max-height: 200px">
              <br><span>Photo ID / KTP</span>
            </a>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success btnSave" data-status="y" data-user_id="" data-nik="" data-name="">Accept</button>
        <button type="button" class="btn btn-danger btnSave" data-status="n" data-user_id="" data-nik="" data-name="">Reject</button>
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
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/custom.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.css">
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
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>
<script>
  const path = '/users';
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $('[data-magnify]').magnify({
      resizable: false,
      initMaximized: true,
      headerToolbar: [
        'close'
      ],
    });

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
          d.type = $('#filter-type option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 4],
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
      buttons: [
        "reload", "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('click', '.btnReview', function(e) {
      $('#modalReview').modal('show');
      var user_id = $(this).data('user_id');
      var name = $(this).data('name');
      var nik = $(this).data('nik');
      var url_photo = $(this).data('photo_id');
      $('#name').html(name);
      $('#nik').html(nik);
      $('#photo_id').attr('src', url_photo);
      $('#photo_id').parent().attr('href', url_photo);
      $('.btnSave').attr('data-user_id', user_id)
      $('.btnSave').attr('data-name', name)
      $('.btnSave').attr('data-nik', nik)
    });

    $('body').on('click', '.btnReject', function(e) {
      btnRejectClicked(this)
    });

    function btnRejectClicked(e) {
      updateSubmission(e, 'n');
    }
    
    $('body').on('click', '.btnAccept', function(e) {
      btnAcceptClicked(this)
    });
    
    function btnAcceptClicked(e) {
      updateSubmission(e, 'y');
    }
    
    $('.btnSave').click(function(e) {
      const status = $(this).data('status');
      updateSubmission(this, $(this).data('status'));
    });

    function updateSubmission(e, status_submission) {
      const user_id = $(e).data('user_id');
      const name = $(e).data('name');
      const nik = $(e).data('nik');
      const status = status_submission == 'y' ? 'Confirm' : 'Reject';
      // const url = '<?= base_url() ?>/users/updateSubmission';
      Swal.fire({
        title: `You're going to <span class="${status_submission == 'y' ? 'text-success' : 'text-danger'}">${status}</span> the submission of "${name}"`,
        html: `NIK: <b>${nik}<br>Submission: ${status}<b><br><br>Are you sure?`,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, ${status} Submission`,
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
          $.ajax({
            url: base_url + path + '/updateSubmission',
            type: "post",
            dataType: "json",
            data: {
              user_id: user_id,
              status_submission: status_submission,
            }
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            if (response.success) {
              changeCountBadge('submission_count', false);
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
          }).always(function() {
            $('#modalReview').modal('hide');
            datatable.ajax.reload();
          })
        }
      });
    }

    <?php
    if ($search) {
      $_search = htmlspecialchars(str_replace("'", "", str_replace('"', '', $search)));
    ?>
      $('#isLoading').removeClass('d-none');
      setTimeout(() => {
        $('#isLoading').addClass('d-none');
        datatable.search('<?= $_search ?>').draw();
      }, 2000);
    <?php
    }
    ?>

  });
</script>
<?= $this->endSection('content_js') ?>