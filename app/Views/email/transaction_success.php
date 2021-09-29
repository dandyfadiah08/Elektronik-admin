<h5>
  <a class="p-2" href="<?= base_url('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= env('app.name') ?>" height="32"> <?= env('app.name') ?></a>
</h5>
<br>
<span>Hai <b><?= $d->name ?></b></span>
<br><br>
Congratulation you have received your payment from <?= env('app.name') ?> for Transaction <b><?= $d->check_code ?></b>
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
    <td>Device / IMEI</td>
    <td> : </td>
    <td><?= "$d->brand $d->model $d->storage ($d->dc_type) / $d->imei" ?></td>
  </tr>
  <tr>
    <td>Notes</td>
    <td> : </td>
    <td><?= $d->ub_notes ?></td>
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