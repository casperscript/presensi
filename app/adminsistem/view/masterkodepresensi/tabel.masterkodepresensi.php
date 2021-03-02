<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">KODE PRESENSI</th>
            <th class="grey darken-3 white-text center-align">KETERANGAN</th>
            <th class="grey darken-3 white-text center-align">POTONGAN TPP</th>
            <th class="grey darken-3 white-text center-align">MODERASI</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['id_kode_presensi']; ?>" class="btnDetail center-align"><?= $no; ?></td>
                    <td id="<?= $kol['id_kode_presensi']; ?>" class="btnDetail center-align"><?= $kol['kode_presensi']; ?></td>
                    <td id="<?= $kol['id_kode_presensi']; ?>" class="btnDetail"><?= $kol['ket_kode_presensi']; ?></td>
                    <td id="<?= $kol['id_kode_presensi']; ?>" class="btnDetail center-align"><?= $kol['pot_kode_presensi']; ?></td>
                    <td id="<?= $kol['id_kode_presensi']; ?>" class="btnDetail center-align">
                        <?= (($kol['moderasi_kode_presensi']==1)) ? '<span class="chip green white-text">YA</span>' : '<span class="chip red white-text">TIDAK</span>';?>
                    </td>
                    <td id="<?= $kol['id_kode_presensi']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['id_kode_presensi']; ?>" nama="<?= $kol['ket_kode_presensi']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['id_kode_presensi']; ?>" nama="<?= $kol['ket_kode_presensi']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
