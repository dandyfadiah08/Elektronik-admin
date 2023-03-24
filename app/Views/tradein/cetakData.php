<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataTradein.xls");
?>
<html>

<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Transaksi</th>
                <th>Status Transaksi</th>
                <th>Mitra dan Toko</th>
                <th>kode Tradein dan Serial Number</th>
                <th>Kategori</th>
                <th>Harga Device</th>
                <th>Subsidi</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datatradein->getResultArray() as  $row) ?>
            <tr>
                <td scope="row">
                    <?= $i; ?>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>