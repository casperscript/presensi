<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">NAMA SHIFT</th>
            <th class="grey darken-3 white-text center-align">LOKASI KERJA</th>
            <th class="grey darken-3 white-text center-align">TANGGAL MULAI</th>
            <th class="grey darken-3 white-text center-align">TANGGAL AKHIR</th>
            <th class="grey darken-3 white-text center-align">SIKLUS</th>
            <th class="grey darken-3 white-text center-align">UNIT</th>
            <th class="grey darken-3 white-text center-align">STATUS</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['id_shift']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="btnDetail"><?= $kol['nama_shift']; ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="btnDetail"><?= $nama_lokasi[$kol['kdlokasi']]; ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="center-align btnDetail"><?= comp\FUNC::tanggal($kol['tanggal_mulai_shift'], 'long_date'); ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="center-align btnDetail"><?= comp\FUNC::tanggal($kol['tanggal_akhir_shift'], 'long_date'); ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="center-align btnDetail"><?= $kol['siklus_shift']; ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="center-align btnDetail"><?= $kol['unit_shift']; ?></td>
                    <td id="<?= $kol['id_shift']; ?>" class="center-align btnDetail">
                            <?= (($kol['status_shift']=='publish')) ? '<span class="chip green white-text">PUBLISH</span>' : '<span class="chip red white-text">DRAFT</span>';?>
                    </td>
                    <td id="<?= $kol['id_shift'] ?>" class="btnDetail center-align">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['id_shift']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['id_shift']; ?>" nama="<?= $kol['nama_shift']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
