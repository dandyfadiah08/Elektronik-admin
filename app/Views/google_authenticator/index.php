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
      <?php if (!$status_2fa) : ?>
        <div class="row" id="2fa_setup_wrapper">
          <div class="col">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Scan QR 2FA</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <ol>
                      <li>
                        Please scan this QR Code wiht <b>Google Authenticator</b> app.<br>
                        <img src="<?= $image_url ?>" alt="Image unavailable. Please reload.">
                      </li>
                      <li>
                        Input 6 code in the <b>Google Authenticator</b> app to the <b>2FA Code</b> field.
                      </li>
                      <li>
                        Click <b>Submit</b>, if success. It is done.
                      </li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="card card-primary" id="2fa_confirm_wrapper">
              <div class="card-header">
                <h3 class="card-title">Confirm 2FA</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="code">2FA Code</label>
                      <input type="text" class="form-control form-control-border border-width-2" id="code" placeholder="Input code her. Ex: 123456">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary float-right" id="btnValidate2FA">Submit</button>
              </div>
            </div>
          </div>
        </div>
      <?php else : ?>
        <div class="row">
          <div class="col">
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">2FA Connected</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    You can test wether your code is working or not.
                    <br>If it is not working, please contact IT Team.
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="code">2FA Code</label>
                      <input type="text" class="form-control form-control-border border-width-2" id="code" placeholder="Input code her. Ex: 123456">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary float-right" id="btnValidate2FA">Submit</button>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script>
  $(document).ready(function() {
    $('#btnValidate2FA').click(function() {
      let code = $('#code').val();
      if (code == '') {
        alert('Please fill 2FA Code');
      } else {
        $.ajax({
          url: '<?= base_url('google_authenticator/validate_2fa') ?>',
          method: 'post',
          dataType: 'json',
          data: {
            code: code,
          }
        }).done(function(response) {
          console.log(response);
          if (response.success) {
            Swal.fire('Success', response.message, 'success')
            $('#code').val('');
            if('<?= $status_2fa ?>' == '') window.location.reload();
            // $('#2fa_setup_wrapper').hide();
            // $('#2fa_confirm_wrapper').addClass('card-success');
            // $('#2fa_confirm_wrapper').addClass('card-primary');
            // $('#2fa_confirm_wrapper > .card-header > .card-title').text('2FA Connected');
          } else {
            Swal.fire('Failed', response.message, 'error')
          }
        }).fail(function(response) {
          console.log('fail');
          console.log(response);
          Swal.fire('Error occured', '', 'error')
        })

      }
    })
  })
</script>
<?= $this->endSection('content_js') ?>