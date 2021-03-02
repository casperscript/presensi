<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text">NAMA PANDUAN</th>
            <th class="grey darken-3 white-text center-align">FILE PANDUAN</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_panduan']; ?>" class="center-align btnDetail"><?= $no; ?></td>
                    <td id="<?= $kol['kd_panduan']; ?>" class="btnDetail"><?= $kol['nama_panduan']; ?></td>
                    <td id="<?= $kol['kd_panduan']; ?>" class="center-align btnDetail">
                        <?= (($kol['file_panduan']=='')) ? '' : '<a href="'.$link_file.$kol['file_panduan'].'" download class="waves-effect waves-light btn m-b-xs chip blue white-text">UNDUH</a>';?>
                        
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
