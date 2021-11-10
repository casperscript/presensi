<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="highlight">
    <thead>
        <tr>
            <th colspan="2">No</th>
            <th>Kelompok</th>
            <th>Nama</th>
            <th>IP</th>
            <th>No. Seri</th>
            <th class="center-align"><i class="fa fa-flash"></i></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataTabel as $kol) : ?>
            <tr>
                <td width="50px"><?= $no++; ?></td>
                <td width="30px">
                    <?php if ($kol['status'] == 'disable') : ?>
                        <i class="material-icons red-text">not_interested</i>
                    <?php else : ?>
                        <i class="material-icons green-text">done</i>
                    <?php endif; ?>
                </td>
                <td><?= $kol['nama_kelompok']; ?></td>
                <td><?= $kol['nama_mesin']; ?></td>
                <td><?= $kol['ip_mesin']; ?></td>
                <td><?= $kol['serial_mesin']; ?></td>
                <td class="center-align">
                    <a id="<?= $kol['id_mesin'] ?>" href="javascript:void(0)" class="btnForm"><i class="material-icons orange-text">settings</i></a>
                    <a id="<?= $kol['id_mesin'] ?>" href="javascript:void(0)" class="btnHapus" nama="<?= $kol['nama_mesin'] ?>"><i class="material-icons red-text">delete</i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>