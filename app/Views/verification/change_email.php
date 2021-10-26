<<<<<<< HEAD
<div class="row">
  <div class="col text-center">
    <h5>
      <a class="p-2" href="<?= env('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= env('app.name') ?>" height="52"> <?= env('app.name') ?></a>
    </h5>
    <br>
    <img src="<?= base_url('assets/images/' . ($d->success ? 'ok' : 'not-ok') . '.png') ?>" style="max-height: 150px; max-width: 150px; ">
    <br>
    <br>
    <strong><span class="<?= $d->success ? 'text-success' : 'text-danger' ?>"><?= $d->message ?></span></strong>.
    <br>
  </div>
=======
<div class="row">
  <div class="col text-center">
    <h5>
      <a class="p-2" href="<?= env('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= env('app.name') ?>" height="52"> <?= env('app.name') ?></a>
    </h5>
    <br>
    <img src="<?= base_url('assets/images/' . ($d->success ? 'ok' : 'not-ok') . '.png') ?>" style="max-height: 150px; max-width: 150px; ">
    <br>
    <br>
    <strong><span class="<?= $d->success ? 'text-success' : 'text-danger' ?>"><?= $d->message ?></span></strong>.
    <br>
  </div>
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
</div>