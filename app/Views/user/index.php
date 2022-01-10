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
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ]) . htmlSelect([
                  'id' => 'filter-type',
                  'label' => 'Type',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-user" title="Type Filter"></i>',
                  'attribute' => 'data-placeholder="Type Filter"',
                  'option' => $optionType,
                ]) . htmlSelect([
                  'id' => 'filter-merchant',
                  'label' => 'Merchant',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-user-tag" title="Merchant Filter"></i>',
                  'attribute' => 'data-placeholder="Merchant Filter"',
                  'option' => $optionMerchant,
                ]) . htmlSelect([
                  'id' => 'filter-internal_agent',
                  'label' => 'Agen '.env('app.name'),
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-3',
                  'prepend' => '<i class="fas fa-user-tag" title="Agen '.env('app.name').'"></i>',
                  'attribute' => 'data-placeholder="Filter Agen '.env('app.name').'"',
                  'option' => $optionInternalAgent,
                ]) . htmlInput([
                  'id' => 'filter-date',
                  'label' => 'Register Date',
                  'class' => 'datepicker myfilter',
                  'form_group' => 'col-sm-3',
                  'append' => '<i class="fas fa-calendar" title="Register Date Filter"></i>',
                  'prepend' => '<i class="fas fa-undo-alt" title="Clear Date Filter" id="clearDate"></i>',
                  'attribute' => 'title="Tidak berpengaruh jika filter Submission Request aktif"',
                ])
                ?>
              </div>
              <div class="row">
                <?= htmlIcheckbox([
                  'id' => 'filter-submission',
                  'class' => 'myfilter',
                  'title' => 'show only submission request / need review',
                  'label' => 'Submission Request',
                  'checked' => '',
                  'color' => 'danger',
                  'form_group' => 'col-sm-3',
                ]) ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Register Date</th>
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
            <a href="<?= base_url('/assets/images/photo-unavailable.png') ?>" data-magnify="gallery" data-caption="Photo ID / KTP" class="magnify_caption">
              <img src="<?= base_url('/assets/images/photo-unavailable.png') ?>" id="photo_id" class="img-fluid rounded" alt="Responsive image" style="max-height: 200px">
              <br><span>Photo ID / KTP</span></span>
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

  <!-- Modal User KTP -->
  <div class="modal" tabindex="-1" id="modalViewUser">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>User KTP</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formConfirmAppointment">
            <div id="printCourier">
              <div class="row">
                <div class="form-group col-6">
                  <label for="address_detail">User Details</label>
                  <table>
                    <?=
                    htmlTr(['text' => 'Name', 'id' => 'vu-name'])
                      . htmlTr(['text' => 'NIK', 'id' => 'vu-nik'])
                    ?>
                  </table>
                </div>
                <div class="col-6 device-check-image-wrapper">
                  <a id="vu-photo_id" href="<?= base_url("assets/images/photo-unavailable.png") ?>" data-magnify="gallery" data-caption="Photo ID (KTP)">
                    <span>Photo ID (KTP)</span>
                    <br>
                    <img src="<?= base_url("assets/images/photo-unavailable.png") ?>" loading="lazy" alt="" class="image-fluid device-check-image">
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer d-block">
          <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>



<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/custom.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
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
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
<script>
  const path = '/users';
  var _search = <?= $search ?>;
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

    initDateRangePicker();
    $('#clearDate').click(function() {
      $('.datepicker').val('')
      datatable.ajax.reload()
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
          d.submission = $('#filter-submission').prop('checked');
          d.type = $('#filter-type option:selected').val();
          d.merchant = $('#filter-merchant option:selected').val();
          d.internal_agent = $('#filter-internal_agent option:selected').val();
          d.date = $('#filter-date').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 5],
        className: "text-center",
      }, {
        targets: [0, 5],
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
    $('body').on('click', '.btnLogs', function(e) {
      window.open(`${base_url}/logs/user/${$(this).data('id')}`)
    });

    $('body').on('click', '.btnReview', function(e) {
      $('#modalReview').modal('show');
      var user_id = $(this).data('user_id');
      var name = $(this).data('name');
      var nik = $(this).data('nik');
      var url_photo = $(this).data('photo_id');
      $('#name').text(name);
      $('#nik').text(nik);
      $('#photo_id').attr('src', url_photo);
      $('#photo_id').parent().attr('href', url_photo);
      $('.btnSave').attr('data-user_id', user_id)
      $('.btnSave').attr('data-name', name)
      $('.btnSave').attr('data-nik', nik)
      $('.magnify_caption').attr('data-caption', `${nik} / ${name}`)
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

    // button View KTP Detail (class)
    $('body').on('click', '.btnViewKtp', function() {
      $('#user_id').val($(this).data('user_id'));
      const type = $(this).data('type');
      $.ajax({
        url: `${base_url}${path}/view_ktp`,
        type: "post",
        dataType: "json",
        data: {
          user_id: $(this).data('user_id'),
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          console.log(response.data)
          let d = response.data;
          $('#vu-name').html(`<a href="${base_url}/users/detail/${d.user_id}" title="Klik untuk lihat detail user">${d.name}</a> ${iconCopy(d.name)}`);
          $('#vu-nik').html(`${d.nik} ${iconCopy(d.nik)}`);
          $('#vu-photo_id').attr('href', d.photo_id);
          $('#vu-photo_id > img').attr('src', d.photo_id);
          $('#modalViewUser').modal('show');
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    });

    $('body').on('click', '.btnMakeAsInternalAgent', function() {
      const user_id = $(this).data('user_id');
      const name = $(this).data('name');
      Swal.fire({
        title: `Kamu akan membuat user <span class="text-success">${name}</span> menjadi "Agen <?= env('app.name') ?>"`,
        html: `Apakah kamu ingin melanjutkan aksi ini?`,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Ya, Jadikan Agen!`,
        showCancelButton: true,
        cancelButtonText: `<i class="fas fa-undo"></i> Tidak, Kembali`,
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
            url: base_url + path + '/makeAsInternalAgent',
            type: "post",
            dataType: "json",
            data: {
              user_id: user_id,
            }
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            if (response.success) {
              Swal.fire(response.message, '', class_swal)
              datatable.ajax.reload();
            } else if (Object.keys(response.data).length > 0) {
              for (const [key, value] of Object.entries(response.data)) {
                inputError(key, value)
              }
            } else
              Swal.fire(response.message, '', class_swal)
          }).fail(function(response) {
            Swal.fire('An error occured!', '', 'error')
          })
        }
      });

    });


    if (_search) {
      $('#isLoading').removeClass('d-none');
      setTimeout(() => {
        $('#isLoading').addClass('d-none');
        datatable.search(_search).draw();
      }, 2000);
    }

  });
</script>
<?= $this->endSection('content_js') ?>