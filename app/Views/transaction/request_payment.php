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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>/transaction">Transaction</a></li>
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
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Request Payment</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <?=
                htmlInput([
                  'id' => 'check_code',
                  'label' => 'Check Code',
                  'class' => 'form-control-border inputRequest',
                  'form_group' => 'col-sm-6',
                  'placeholder' => 'Ex. 21WFNTW1N5',
                ]) . htmlInput([
                  'id' => 'account_number',
                  'label' => 'Account Number (No. Rekening)',
                  'class' => 'form-control-border inputRequest',
                  'form_group' => 'col-sm-6',
                  'placeholder' => 'Ex. 123456789',
                ])
                ?>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right" id="btnRequestPayment">Submit Request</button>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card card-success" id="result" style="display: none;">
            <div class="card-header">
              <h3 class="card-title">Result</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col" id="result-content"></div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-secondary float-right d-none" id="btnResetResult">Clear Result</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script>
  const path = '/transaction';
  $(document).ready(function() {
    $('#btnResetResult').click(function() {
      $('#result-content').text('');
      $('#result').hide();
    });
    $('#btnRequestPayment').click(function() {
      const thisHTML = btnOnLoading('#btnRequestPayment');
      $('#result-content').text('');
      $('#result').hide();
      var data = {
        check_code: $('#check_code').val(),
        account_number: $('#account_number').val(),
      }
      $.ajax({
        url: base_url + path + '/do_request_payment',
        type: 'post',
        dataType: 'json',
        data: data,
      }).done(function(response) {
        btnOnLoading('#btnRequestPayment', false, thisHTML)
        if (response.success) {
          playSound()
          Swal.fire('Success', response.message, 'success').then(function() {
            $('#check_code').val(''),
            $('#account_number').val(''),
            $('#result-content').html(response.message);
            $('#result').show();
          });
        } else {
          Swal.fire('Failed', response.message, 'error');
        }
      }).fail(function(response) {
        btnOnLoading('#btnRequestPayment', false, thisHTML)
        Swal.fire('Failed', 'Could not perform the task, please try again later. #trs01v', 'error');
      })
    })
  })
</script>
<?= $this->endSection('content_js') ?>