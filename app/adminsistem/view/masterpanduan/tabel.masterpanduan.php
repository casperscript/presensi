<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text">KODE PANDUAN</th>
            <th class="grey darken-3 white-text">NAMA PANDUAN</th>
            <th class="grey darken-3 white-text center-align">FILE PANDUAN</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_panduan']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['kd_panduan']; ?>" class="btnDetail"><?= $kol['kd_panduan']; ?></td>
                    <td id="<?= $kol['kd_panduan']; ?>" class="btnDetail"><?= $kol['nama_panduan']; ?></td>
                    <td id="<?= $kol['kd_panduan']; ?>" class="center-align btnDetail">
                        <?= (($kol['file_panduan']=='')) ? '' : '<a href="'.$link_file.$kol['file_panduan'].'" download class="waves-effect waves-light btn m-b-xs chip blue white-text">UNDUH</a>';?>
                        
                    </td>
                    <td id="<?= $kol['kd_panduan']; ?>" class="btnDetail center-align">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['kd_panduan']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['kd_panduan']; ?>" nama="<?= $kol['nama_panduan']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
