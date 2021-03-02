<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">KELOMPOK MESIN</th>
            <th class="grey darken-3 white-text center-align">NAMA MESIN</th>
            <th class="grey darken-3 white-text center-align">IP MESIN</th>
            <th class="grey darken-3 white-text center-align">SERIAL</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['id_mesin']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['id_mesin']; ?>" class="btnDetail"><?= $nama_kelompok[$kol['id_kelompok_mesin']]; ?></td>
                    <td id="<?= $kol['id_mesin']; ?>" class="btnDetail"><?= $kol['nama_mesin']; ?></td>
                    <td id="<?= $kol['id_mesin']; ?>" class="center-align btnDetail"><?= $kol['ip_mesin']; ?></td>
                    <td id="<?= $kol['id_mesin']; ?>" class="center-align btnDetail"><?= $kol['serial_mesin']; ?></td>
                    <td id="<?= $kol['id_mesin']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['id_mesin']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['id_mesin']; ?>" nama="<?= $kol['nama_mesin']; ?>" ip="<?= $kol['ip_mesin']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
