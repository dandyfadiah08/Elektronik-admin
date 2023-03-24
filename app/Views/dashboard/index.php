<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content">
    <div class="container pt-4">
      <div class="row p-4 mt-5">
        <div class="col-lg-3 "></div>
        <div class="col-lg-6 pt-4 text-center mt-5">
          <img class="img-fluid" src="<?= base_url('assets/images/logo-hitam.png') ?>" height="512" alt="">
        </div>
        <div class="col-lg-3"></div>
      </div>
    </div>
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>
<?= $this->section('content_css') ?>
<style>
  .content-wrapper {
    background: rgb(59, 157, 255);
    background: linear-gradient(0deg, rgba(59, 157, 255, 1) 0%, rgba(37, 103, 255, 1) 77%);
  }


  img:hover {
    animation: shake 0.5s;
    animation-iteration-count: infinite;
  }

  @keyframes shake {
    0% {
      transform: translate(1px, 1px) rotate(0deg);
    }

    10% {
      transform: translate(-1px, -2px) rotate(-1deg);
    }

    20% {
      transform: translate(-3px, 0px) rotate(1deg);
    }

    30% {
      transform: translate(3px, 2px) rotate(0deg);
    }

    40% {
      transform: translate(1px, -1px) rotate(1deg);
    }

    50% {
      transform: translate(-1px, 2px) rotate(-1deg);
    }

    60% {
      transform: translate(-3px, 1px) rotate(0deg);
    }

    70% {
      transform: translate(3px, 1px) rotate(-1deg);
    }

    80% {
      transform: translate(-1px, -1px) rotate(1deg);
    }

    90% {
      transform: translate(1px, 2px) rotate(0deg);
    }

    100% {
      transform: translate(1px, -2px) rotate(-1deg);
    }
  }
</style>

<?= $this->endSection('content_css') ?>