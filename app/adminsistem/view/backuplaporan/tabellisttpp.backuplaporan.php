<?php
$no = 1;
// comp\FUNC::showPre($data);

$list_induk = array_flip(array_column($induk, 'kdlokasi'));

switch ($status) {
    case 'belum':
        $result = array_diff_key($lokasi, $list_induk);
        break;
    case 'sudah':
        $result = array_intersect_key($lokasi, $list_induk);
        break;
    default:
        $result = $lokasi;
}

// comp\FUNC::showPre($list_induk);
// comp\FUNC::showPre($result);
// comp\FUNC::showPre($status);
// exit;

// foreach ($lokasi as $key => $val) :

// endforeach;
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
        <?php foreach ($result as $key => $val) : ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td><?= $key ?></td>
                <td><?= $val ?></td>
                <td class="center-align">
                    <?php if ($status == 'sudah') : ?>
                        <a id="<?= comp\FUNC::encryptor($kd_tpp . '|' . $key) ?>" href="javascript:void(0)" class="btn-floating btn waves-effect waves-light red darken-2 btnHapusBackup" title="Hapus backup <?= $val ?>" type="button">
                            <i class="material-icons left">delete</i>
                        </a>
                    <?php else : ?>
                        <a id="<?= comp\FUNC::encryptor($kd_tpp . '|' . $key) ?>" href="javascript:void(0)" class="btn-floating btn waves-effect waves-light amber darken-4 btnBackup" title="Backup <?= $val ?>" type="button">
                            <i class="material-icons left">system_update_alt</i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>