<h5>
  <a class="py-2" href="<?= env('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo-hitam.png') ?>" alt="<?= env('app.name') ?>" height="52"> <?= env('app.name') ?></a>
</h5>
<br>
<span>Hai <b><?= $d->name ?></b></span>
<br><br>
Your email verification code on <?= env('app.name') ?> is <strong><?= $d->otp ?></strong>
<br>
Best regards,
<br>
<br>
<b><?= env('app.name') ?></b>
<br>
<br>
<br>
<small>Please ignore this email if you are not the person in charge</small>