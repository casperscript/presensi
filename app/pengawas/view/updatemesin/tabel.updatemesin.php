<?php
//comp\FUNC::showPre($data);
extract($dataTabel);
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Lokasi Finger</th>
            <th class="center-align">IP</th>
            <th>Serial Number</th>
            <th>Record Akhir</th>
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
                <td id="lastUpdate<?= $kol['id_mesin'] ?>"><?= (!empty($kol['last_record'])) ? comp\FUNC::tanggal($kol['last_record'], 'long_date') : '-' ?></td>
                <td class="center-align">
                    <button id="<?= $kol['id_mesin'] ?>" class="btn-floating waves-effect waves-light blue-grey btn btnUpdate" onclick="app.updateMesin(this.id); this.disabled = true;">
                        <i class="material-icons">replay</i>
                    </button>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>
