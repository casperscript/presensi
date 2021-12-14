<?php
$no = 1;
// comp\FUNC::showPre($data);
?>

<table class="highlight">
    <thead>
        <tr>
            <th class="center-align">No</th>
            <th>Kode</th>
            <th>Nama OPD</th>
            <th class="center-align">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lokasi as $key => $val) : ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td><?= $key ?></td>
                <td><?= $val ?></td>
                <td class="center-align">
                    <a id="<?= comp\FUNC::encryptor($tahun . '|' . $bulan . '|' . $key) ?>" href="javascript:void(0)" class="btn-floating btn waves-effect waves-light amber darken-4 btnBackup" title="Backup <?= $val ?>" type="button">
                        <i class="material-icons left">system_update_alt</i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>