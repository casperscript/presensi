<?php
//comp\FUNC::showPre($nama_lokasi);
//comp\FUNC::showPre($query);
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text">USERNAME</th>
            <th class="grey darken-3 white-text">NAMA PEGAWAI</th>
            <th class="grey darken-3 white-text">Password</th>
            <th class="grey darken-3 white-text center-align">LOKASI</th>
            <th class="grey darken-3 white-text center-align">GRUP PENGGUNA</th>
            <th class="grey darken-3 white-text center-align">STATUS PENGGUNA</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                # Nama personil
                switch (true) {
                    case ($kol['nipbaru'] == 'system'):
                        $nama_pengguna = 'Admin Sistem';
                        break;
                    case ($kol['nipbaru'] == ''):
                        $nama_pengguna = '<span class="chip blue-grey white-text">NIP Tidak Diisi</span>';
                        break;
                    case (isset($listPersonil[$kol['nipbaru']])):
                        $nama_pengguna = $listPersonil[$kol['nipbaru']];
                        break;
                    default: 
                        $nama_pengguna = '<span class="chip blue-grey white-text">Undifined</span>';
                }
                
                # Nama lokasi kerja
                switch (true) {
                    case (isset($listLokasi[$kol['kdlokasi']])):
                        $nama_lokasi = $listLokasi[$kol['kdlokasi']];
                        break;
                    case ($kol['kdlokasi'] == ''):
                        $nama_lokasi = '<span class="chip blue-grey white-text">OPD Kosong</span>';
                        break;
                    case (!empty($kol['kdlokasi']) && !isset($listLokasi[$kol['kdlokasi']])):
                        $nama_lokasi = '<span class="chip blue-grey white-text">OPD Tidak Terdaftar</span>';
                        break;
                    default:
                        $nama_lokasi = '<span class="chip blue-grey white-text">Undefined</span>';
                }
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['username']; ?>" class="btnDetail center-align"><?= $no; ?></td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail"><?= $kol['username']; ?></td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <?= (!empty($kol['jabatan_pengguna'])) ? '[' . $kol['jabatan_pengguna'] . '] ' : '' ?>
                        <?= $nama_pengguna ?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <?= comp\FUNC::decryptor($kol['password']) ?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <?= $nama_lokasi ?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="btnDetail">
                        <div class="center-align">
                            <?= $listGrubPengguna[$kol['grup_pengguna_kd']]; ?>
                        </div>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="center-align btnDetail">
                        <?= (($kol['status_pengguna']=='enable')) ? '<span class="chip green white-text">ENABLE</span>' : '<span class="chip red white-text">DISABLE</span>';?>
                    </td>
                    <td id="<?= $kol['username']; ?>" class="center-align btnDetail">
                        <!-- <i class="small material-icons grey-text btnDetail">visibility</i>-->
                        <i id="<?= $kol['username']; ?>" nama="<?= $kol['username']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['username']; ?>" nama="<?= $kol['username']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>