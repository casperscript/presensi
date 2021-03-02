<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">Tanggal</th>
            <th class="grey darken-3 white-text">KETERANGAN</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['id_libur']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['id_libur']; ?>" class="btnDetail center-align"><?= comp\FUNC::tanggal($kol['tgl_libur'], 'long_date'); ?></td>
                    <td id="<?= $kol['id_libur']; ?>" class="btnDetail"><?= $kol['keterangan']; ?></td>
                    <td id="<?= $kol['id_libur'] ?>" class="btnDetail center-align">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['id_libur']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['id_libur']; ?>" nama="<?= $kol['keterangan']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
