<?php
//comp\FUNC::showPre($dataTabel);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text">USERNAME</th>
            <th class="grey darken-3 white-text">NAMA PEGAWAI</th>
            <th class="grey darken-3 white-text center-align">LOKASI</th>
            <th class="grey darken-3 white-text center-align">GRUP PENGGUNA</th>
            <th class="grey darken-3 white-text center-align">STATUS PENGGUNA</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['username']; ?>" class="btnDetail center-align"><?= $no; ?></td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail"><?= $kol['username']; ?></td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <?= (($kol['nipbaru']=='')) ? '' : $nama_personil[$kol['nipbaru']];?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <?= (($kol['kdlokasi']=='')) ? '' : $nama_lokasi[$kol['kdlokasi']];?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <div class="center-align">
                            <?= $kol['nama_grup_pengguna']; ?>
                        </div>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="center-align btnDetail">
                        <?= (($kol['status_pengguna']=='enable')) ? '<span class="chip green white-text">ENABLE</span>' : '<span class="chip red white-text">DISABLE</span>';?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['username']; ?>" nama="<?= $kol['username']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <!--<i id="<?= $kol['username']; ?>" nama="<?= $kol['username']; ?>" class="small material-icons red-text btnHapus">delete</i>-->
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>