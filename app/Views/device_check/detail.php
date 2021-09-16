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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check">Device Check</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check">Unreviewed</a></li>
            <li class="breadcrumb-item status"><?= $page->navbar ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <?= $this->include('device_check/summary') ?>
      <?= $this->include('device_check/software_check') ?>
      <?= $this->include('device_check/photos') ?>
      <?php if(hasAccess($role, 'r_review') && $dc->dc_status == 4 || true): ?>
      <div class="row">
        <div class="col">
          <div class="card card-primary">
            <div class="card-header" data-card-widget="collapse">
              <h3 class="card-title">Action</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalManualGrade"><i class="fas fa-poll-h"></i> Manual Grade</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- Modal Manual Grade -->
  <div class="modal" tabindex="-1" id="modalManualGrade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Manual Grade</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label>Grade</label>
              <select id="grade" data-placeholder="Choose Grade" class="form-control select2bs4 myfilter">
                <option></option>
                <option value="S">S</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="Reject">Reject</option>
              </select>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="fullset" id="fullset-1" value="1">
              <label class="form-check-label" for="fullset-1">Fullset</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="fullset" id="fullset-0" value="0">
              <label class="form-check-label" for="fullset-0">Unit Only</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnManualGrade" disabled>Give Grade</button>
        </div>
      </div>
    </div>
  </div>

</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/custom.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.css">
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>

<script>
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $('#btnManualGrade').click(function() {
      manual_grade();
    })

    async function manual_grade() {
      var grade = $('#grade option:selected').val();
      var fullset = $('input[name="fullset"]:checked').val();
      const thisHTML = btnOnLoading('#btnManualGrade');

      $('#btnManualGrade').html(`<i class="fas fa-spinner fa-spin"></i> Doing magic..`)

      Swal.fire({
        title: `You are going to add grade: ${grade} - ${fullset == 1 ? 'Fullset' : 'Unit Only'}`,
        html: `Click <b>Give Grade</b> to proceed, <br><b>Change</b> to change grade, or<br><b>Close</b> to cancel reviewing`,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Give Grade`,
        denyButtonText: `Change`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          var url = '<?= base_url('device_check/manual_grade'); ?>';
          $.ajax({
            data: {
              check_id: <?= $dc->check_id ?>,
              grade: grade,
              fullset: fullset,
            },
            type: 'POST',
            dataType: 'JSON',
            url: url
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            Swal.fire(response.message, '', class_swal).then(function() {
              if(response.success) {
                  window.location.reload();
              }
            })
          }).fail(function(e) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          }).always(function() {
            btnOnLoading('#btnManualGrade', false, thisHTML)
            checkInputManualGrade()
          })
        } else if (result.isDismissed) {
          btnOnLoading('#btnManualGrade', false, thisHTML)
          $('#modalManualGrade').modal('hide');
          return false;
        } else {
          // change
          btnOnLoading('#btnManualGrade', false, thisHTML)
          checkInputManualGrade()
        }
      });
      
    }
    $('#modalManualGrade').on('show.bs.modal', function() {
      checkInputManualGrade()
    });
    
    $('#grade, input[name="fullset"]').on('change', checkInputManualGrade);

    function checkInputManualGrade() {
      var grade = $('#grade option:selected').val();
      var fullset = $('input[name="fullset"]:checked').val();
      if (grade !== undefined && fullset !== undefined) $('#btnManualGrade').prop('disabled', false);
      else $('#btnManualGrade').prop('disabled', true);
    }

    $('[data-magnify]').magnify({
      resizable: false,
      initMaximized: true,
      headerToolbar: [
        'close'
      ],
    });

  });
</script>
<?= $this->endSection('content_js') ?>