<h5>
  <a class="py-2" href="<?= env('app.home_url') ?>" title="<?= env('app.name') ?>"><img src="<?= base_url('assets/images/logo-hitam.png') ?>" alt="<?= env('app.name') ?>" height="52"> <?= env('app.name') ?></a>
</h5>
<br>
<span>Hai <b><?= $d->name ?></b></span>
<br><br>
Congratulation you have received your bonus as an Agent of <?= env('app.name') ?>
<table class="table table-borderless table-sm">
  <tr>
    <td>Status</td>
    <td> : </td>
    <td>Success</td>
  </tr>
  <tr>
    <td>Type</td>
    <td> : </td>
    <td><?= strtoupper($d->type) ?></td>
  </tr>
  <tr>
    <td>Date & Time</td>
    <td> : </td>
    <td><?= date_format(date_create($d->created_at), "d M Y H:i:s") ?></td>
  </tr>
  <tr>
    <td>Amount</td>
    <td> : </td>
    <td><?= number_to_currency($d->currency_amount, strtoupper($d->currency)) ?></td>
  </tr>
  <tr>
    <td>Notes</td>
    <td> : </td>
    <td><?= $d->notes ?></td>
  </tr>
  <tr>
    <td>Referrence Number</td>
    <td> : </td>
    <td>#<?= $d->user_balance_id ?></td>
  </tr>
</table>
<br>
Best regards,
<br>
<br>
<b><?= env('app.name') ?></b>