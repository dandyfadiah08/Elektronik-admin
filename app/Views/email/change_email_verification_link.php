<h5>
  <a class="p-2" href="<?= env('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= env('app.name') ?>" height="52"> <?= env('app.name') ?></a>
</h5>
<br>
<span>Hai <b><?= $d->name ?></b></span>
<br><br>
To verify your email changes on <?= env('app.name') ?>, please click the following link: 
<br>
<br><strong><a href="<?= $d->link ?>" title="Verify Email Chnages"><?= $d->link ?></a></strong>
<br>
<br>
Best regards,
<br>
<br>
<b><?= env('app.name') ?></b>
<br>
<br>
<br>
<small>Please ignore this email if you are not the person in charge</small>
