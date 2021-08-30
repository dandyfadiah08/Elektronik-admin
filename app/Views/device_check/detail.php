<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<?php
$photo_url = base_url() . '/uploads/';
$default_photo = base_url() . '/assets/images/photo-unavailable.png';
$photo_fullset = empty($dc->photo_fullset) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_fullset;
$photo_imei_registered = empty($dc->photo_imei_registered) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_imei_registered;
$photo_device_1 = empty($dc->photo_device_1) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_1;
$photo_device_2 = empty($dc->photo_device_2) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_2;
$photo_device_3 = empty($dc->photo_device_3) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_3;
$photo_device_4 = empty($dc->photo_device_4) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_4;
$photo_device_5 = empty($dc->photo_device_5) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_5;
$photo_device_6 = empty($dc->photo_device_6) ? $default_photo : $photo_url . 'device_checks/' . $dc->photo_device_6;
?>
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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check">Device Check</a></li>
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
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                Detail basic information over here
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="card card-primary collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Software</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col">

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Photos</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="row">
                  <div class="col-3">
                    <img src="<?= $photo_device_1 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_2 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_3 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_4 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_5 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_device_6 ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_fullset ?>" alt="" class="image-fluid myimage">
                  </div>
                  <div class="col-3">
                    <img src="<?= $photo_imei_registered ?>" alt="" class="image-fluid myimage">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalManualGrade">Manual Grade</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
<style>
  .myimage {
    max-height: 150px;
    margin-bottom: 1rem;
  }
</style>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>

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
          console.log(grade);
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
            console.log(response);
            var class_swal = response.success ? 'success' : 'error';
            Swal.fire(response.message, '', class_swal).then(() => {
              if (response.success) location.reload();
            })
          }).fail(function(e) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          })
        } else if (result.isDismissed) {
          $('#modalManualGrade').modal('hide');
          return false;
        }
      });

    }

    $('#grade, input[name="fullset"]').on('change', checkInputManualGrade);
  });

  function checkInputManualGrade() {
      var grade = $('#grade option:selected').val();
      var fullset = $('input[name="fullset"]:checked').val();
      if(grade !== undefined && fullset !== undefined) $('#btnManualGrade').prop('disabled', false);
      else  $('#btnManualGrade').prop('disabled', true);
    }

</script>
<?= $this->endSection('content_js') ?>