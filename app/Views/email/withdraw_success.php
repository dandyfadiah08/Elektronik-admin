<h5>
  <a class="py-2" href="<?= env('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= env('app.name') ?>" height="52"> <?= env('app.name') ?></a>
</h5>
<br>
<span>Hai <b><?= $d->name ?></b></span>
<br><br>
Congratulation you have received your withdraw from <?= env('app.name') ?>
<table class="table table-borderless table-sm">
  <tr>
    <td>Status</td>
    <td> : </td>
    <td>Success</td>
  </tr>
  <tr>
    <td>Type</td>
    <td> : </td>
    <td><?= strtoupper($d->ub_type) ?></td>
  </tr>
  <tr>
    <td>Date & Time</td>
    <td> : </td>
    <td><?= date("d M Y H:i:s") ?></td>
  </tr>
  <tr>
    <td>Amount</td>
    <td> : </td>
    <td><?= number_to_currency($d->currency_amount, strtoupper($d->currency)) ?></td>
  </tr>
  <tr>
    <td>Method</td>
    <td> : </td>
    <td><?= $d->pm_name ?></td>
  </tr>
  <tr>
    <td>Account Number</td>
    <td> : </td>
    <td><?= $d->account_number ?></td>
  </tr>
  <tr>
    <td>Account Name</td>
    <td> : </td>
    <td><?= $d->account_name ?></td>
  </tr>
  <tr>
    <td>Referrence Number</td>
    <td> : </td>
    <td><?= $d->referrence_number ?></td>
  </tr>
</table>
<br>
Best regards,
<br>
<br>
<b><?= env('app.name') ?></b>