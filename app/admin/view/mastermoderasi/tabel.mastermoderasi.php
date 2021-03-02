<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text">KODE JENIS</th>
            <th class="grey darken-3 white-text">JENIS MODERASI</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_jenis']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['kd_jenis']; ?>" class="btnDetail"><?= $kol['kd_jenis']; ?></td>
                    <td id="<?= $kol['kd_jenis']; ?>" class="btnDetail"><?= $kol['nama_jenis']; ?></td>
                    <td id="<?= $kol['kd_jenis']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['kd_jenis']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['kd_jenis']; ?>" nama="<?= $kol['nama_jenis']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
