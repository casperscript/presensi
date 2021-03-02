<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">INDIKATOR ATURAN</th>
            <th class="grey darken-3 white-text center-align">STATUS</th>
            <th class="grey darken-3 white-text center-align">BATAS WAKTU</th>
            <th class="grey darken-3 white-text center-align">POTONGAN TPP</th>
            <th class="grey darken-3 white-text center-align">DASAR HUKUM</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_aturan']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['kd_aturan']; ?>" class="btnDetail"><?= $kol['indikator_aturan']; ?></td>
                    <td id="<?= $kol['kd_aturan']; ?>" class="center-align btnDetail">
                        <?php
                            if($kol['status_aturan']=='masuk'){
                                echo '<span class="chip green white-text">Masuk</span>';
                            }
                            else if ($kol['status_aturan']=='pulang') {
                                echo '<span class="chip orange white-text">Pulang</span>';
                            }
                            else{
                                echo '<span class="chip red white-text">Lupa Finger</span>';
                            }
                        ?>
                    </td>
                    <td id="<?= $kol['kd_aturan']; ?>" class="center-align btnDetail"><?= $kol['batas_waktu_aturan']; ?></td>
                    <td id="<?= $kol['kd_aturan']; ?>" class="center-align btnDetail"><?= $kol['potongan_tpp_aturan']; ?></td>
                    <td id="<?= $kol['kd_aturan']; ?>" class="center-align btnDetail"><?= $kol['dasar_hukum_aturan']; ?></td>
                    <td id="<?= $kol['kd_aturan']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['kd_aturan']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['kd_aturan']; ?>" nama="<?= $kol['indikator_aturan']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
