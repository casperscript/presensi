<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text">KODE GRUP PENGGUNA</th>
            <th class="grey darken-3 white-text">GRUP PENGGUNA</th>
            <th class="grey darken-3 white-text center-align">BATAS MODERASI</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_grup_pengguna']; ?>" class="btnDetail center-align"><?= $no; ?></td>
                    <td id="<?= $kol['kd_grup_pengguna']; ?>" class="btnDetail"><?= $kol['kd_grup_pengguna']; ?></td>
                    <td id="<?= $kol['kd_grup_pengguna']; ?>" class="btnDetail"><?= $kol['nama_grup_pengguna']; ?></td>
                    <td id="<?= $kol['kd_grup_pengguna']; ?>" class="btnDetail center-align"><?= $kol['batas_moderasi']; ?></td>
                    <td id="<?= $kol['kd_grup_pengguna']; ?>" class="btnDetail center-align">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['kd_grup_pengguna']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['kd_grup_pengguna']; ?>" nama="<?= $kol['nama_grup_pengguna']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
