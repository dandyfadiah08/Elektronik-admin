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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/device_check/reviewed">Reviewed</a></li>
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
      <?php if (hasAccess($role, 'r_change_grade') && $dc->status_internal == 8) : ?>
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
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalChangeGrade"><i class="fas fa-poll-h"></i> Change Grade</button>
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
  <div class="modal" tabindex="-1" id="modalChangeGrade">
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
              <label>Current Grade</label>
              <span> : <?= $dc->grade ?></span>
            </div>
            <div class="form-group">
              <label>Current Price</label>
              <span> : <?= number_to_currency($dc->price, "IDR") ?></span>
            </div>
            <div class="form-group">
              <label>New Grade / Price</label>
              <select id="grade" data-placeholder="Choose Grade" class="form-control select2bs4 myfilter">
                <!-- <option></option>
                <option value="S">S</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option> -->
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnChangeGrade" disabled>Give Grade</button>
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
</style>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/libraries/jquery-magnify/jquery.magnify.min.js"></script>

<script>
  const path = '/device_check'
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

    $('#btnChangeGrade').click(function() {
      change_grade();
    })

    async function change_grade() {
      var grade = $('#grade option:selected').val();
      var grade_text = $('#grade option:selected').text();
      const thisHTML = btnOnLoading('#btnChangeGrade');

      // $('#btnChangeGrade').html(`<i class="fas fa-spinner fa-spin"></i> Doing magic..`)

      Swal.fire({
        title: `You are going to change grade to ${grade_text}`,
        html: `Click <b>Give New Grade</b> to proceed, <br><b>Back</b> to re-choose grade, or<br><b>Close</b> to cancel this action`,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Give Grade`,
        denyButtonText: `Back`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            data: {
              check_id: <?= $dc->check_id ?>,
              grade: grade,
            },
            type: 'POST',
            dataType: 'JSON',
            url: base_url + path + '/change_grade'
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            playSound()
            Swal.fire(response.message, '', class_swal).then(function() {
              if (response.success) {
                window.location.reload();
              }
            })
          }).fail(function(e) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          }).always(function() {
            btnOnLoading('#btnChangeGrade', false, thisHTML)
            checkInputChangeGrade()
          })
        } else if (result.isDismissed) {
          btnOnLoading('#btnChangeGrade', false, thisHTML)
          $('#modalChangeGrade').modal('hide');
          return false;
        } else {
          // change
          btnOnLoading('#btnChangeGrade', false, thisHTML)
          checkInputChangeGrade()
        }
      });

    }
    $('#modalChangeGrade').on('show.bs.modal', function() {
      checkInputChangeGrade()
    });

    $('#grade').on('change', checkInputChangeGrade);

    function checkInputChangeGrade() {
      var grade = $('#grade option:selected').val();
      $('#btnChangeGrade').prop('disabled', grade == undefined);
    }

    $('[data-target="#modalChangeGrade"]').click(function() {
      $.ajax({
        data: {
          price_id: <?= $dc->price_id ?>,
        },
        type: 'POST',
        dataType: 'JSON',
        url: base_url + path + '/get_price'
      }).done(function(response) {
        if (response.success) {
          var option = `<option></option>`;
          console.log(typeof response.data)
          for (const [key, value] of Object.entries(response.data)) {
            option += `<option value="${key}F">${key} - ${toPrice(value.fullset)} Fullset</option>`
            option += `<option value="${key}">${key} - ${toPrice(value.unit_only)}</option>`
          }
          $('#grade').html(option)
          $('#grade').trigger('change')
        } else {
          Swal.fire(response.message, '', 'error')
        }
      }).fail(function(e) {
        Swal.fire('An error occured!', '', 'error')
        console.log(e);
      })
    })

  });
</script>
<?= $this->endSection('content_js') ?>