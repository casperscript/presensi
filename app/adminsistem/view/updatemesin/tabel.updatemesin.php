<?php
// comp\FUNC::showPre($data);
// extract($dataTabel);
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Lokasi Finger</th>
            <th class="center-align">IP</th>
            <th>Serial Number</th>
            <th class="right-align">Record Akhir</th>
            <th class="center-align">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td>
                    <?= $kol['nama_mesin'] ?>
                    <div class="progress">
                        <div class="indeterminate progres_<?= $kol['id_mesin'] ?>" style="display: none"></div>
                        <div class="determinate success_<?= $kol['id_mesin'] ?>" style="width: 100%; display: none"></div>
                    </div>
                </td>
                <td class="center-align"><?= $kol['ip_mesin'] ?></td>
                <td><?= $kol['serial_mesin'] ?></td>
                <td id="lastUpdate<?= $kol['id_mesin'] ?>" class="right-align">
                    <?= (isset($update[$kol['id_mesin']])) ? comp\FUNC::tanggal($update[$kol['id_mesin']], 'long_date') : '-' ?>
                </td>
                <td class="center-align">
                    <button id="<?= $kol['id_mesin'] ?>" class="btn-floating waves-effect waves-light blue-grey btn btnUpdate btnUpdate<?= $kol['id_mesin'] ?>" onclick="app.updateMesin(this.id)">
                        <i class="material-icons">replay</i>
                    </button>
                    <a id="<?= $kol['id_mesin'] ?>" class="btn-floating waves-effect waves-lignt orange darken-2 btnImport">
                        <i class="material-icons">import_export</i>
                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>
