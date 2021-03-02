<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">JAM KERJA</th>
            <th class="grey darken-3 white-text center-align">JAM MASUK</th>
            <th class="grey darken-3 white-text center-align">JAM PULANG</th>
            <th class="grey darken-3 white-text center-align">MULAI MASUK</th>
            <th class="grey darken-3 white-text center-align">AKHIR MASUK</th>
            <th class="grey darken-3 white-text center-align">MULAI PULANG</th>
            <th class="grey darken-3 white-text center-align">AKHIR PULANG</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['nama_jam_kerja']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['jam_masuk']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['jam_pulang']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['mulai_masuk']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['akhir_masuk']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['mulai_pulang']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail"><?= $kol['akhir_pulang']; ?></td>
                    <td id="<?= $kol['id_jam_kerja']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['id_jam_kerja']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['id_jam_kerja']; ?>" nama="<?= $kol['nama_jam_kerja']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
