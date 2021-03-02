<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">GOLONGAN / RUANG</th>
            <th class="grey darken-3 white-text center-align">POTONGAN PAJAK</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['id_potongan_pajak']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['id_potongan_pajak']; ?>" class="center-align btnDetail"><?= $kol['golruang_kepegawaian']; ?></td>
                    <td id="<?= $kol['id_potongan_pajak']; ?>" class="center-align btnDetail"><?= $kol['potongan_pajak']; ?></td>
                    <td id="<?= $kol['id_potongan_pajak']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['id_potongan_pajak']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['id_potongan_pajak']; ?>" golongan="<?= $kol['golruang_kepegawaian']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
