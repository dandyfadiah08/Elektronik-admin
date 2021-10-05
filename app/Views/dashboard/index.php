<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content">
    <div class="container pt-4">
      <div class="row p-4">
        <div class="col-lg-3"></div>
        <div class="col-lg-6 pt-4 text-center">
          <img class="img-fluid" src="<?= base_url('assets/images/logo.png') ?>" height="512" alt="">
        </div>
        <div class="col-lg-3"></div>
      </div>
    </div>
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>