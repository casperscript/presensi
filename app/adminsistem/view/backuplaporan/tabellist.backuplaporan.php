<div class="row">
    <div class="col m6">
        <h5 class="center-align"><b>Sudah Backup</b></h5>
        <table class="responsive-table bordered striped hoverable" id="listTable">
            <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
                <tr>
                    <th class="center-align">No</th>
                    <th class="center-align">OPD</th>
                    <th class="center-align" width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $noa = 1;
                foreach ($induk['value'] as $i) :
                    ?>
                    <tr>
                        <td class="center-align"><?= $noa++ ?></td>
                        <td><?= $i['singkatan_lokasi'] ?></td>

                        <td class="center-align"><a href="<?= $this->link('adminsistem/backuplaporan/lihat/') . $i['kdlokasi'] . '/' . $data['bulan'] . $data['tahun'] ?>" class="btn-floating btn waves-effect waves-light light-blue accent-4" title="Tampilkan" type="button">
                                <i class="material-icons left">info</i>
                            </a>

                            <button class="btn-floating btn waves-effect waves-light red accent-4 btnHapus" title="Hapus" type="button" data-kdlokasi="<?= $i['kdlokasi'] ?>" data-lokasi="<?= $i['singkatan_lokasi'] ?>">
                                <i class="material-icons left">delete</i>
                            </button>

                            <?php if (!in_array($i['kdlokasi'], $sudah)) : ?>
                                <button class="btn-floating btn waves-effect waves-light green accent-4 btnPresensi" title="Backup Presensi" type="button" data-kdlokasi="<?= $i['kdlokasi'] ?>" data-lokasi="<?= $i['singkatan_lokasi'] ?>">
                                    <i class="material-icons left">system_update_alt</i>
                                </button>
                            <?php endif; ?>

                        </td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
        </table>
    </div>
    <?= comp\MATERIALIZE::inputKey('bln', $bulan); ?>
    <?= comp\MATERIALIZE::inputKey('thn', $tahun); ?>
    <div class="col m6">
        <h5 class="center-align"><b>Belum Backup</b></h5>
        <table class="responsive-table bordered striped hoverable" id="listTablenot">
            <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
                <tr>
                    <th class="center-align">No</th>
                    <th class="center-align">OPD</th>
                    <th class="center-align">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $nob = 1;
                foreach ($belum as $j) :
                    ?>
                    <tr>
                        <td class="center-align"><?= $nob++ ?></td>
                        <td><?= $lokasi[$j] ?></td>
                        <td class="center-align">
                            <button class="btn-floating btn waves-effect waves-light amber darken-4 btnBackup" title="Backup" type="button" data-kdlokasi="<?= $j ?>">
                                <i class="material-icons left">system_update_alt</i>
                            </button>
                        </td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
        </table>
    </div>
</div>