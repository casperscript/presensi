<?php
$nDaftarVerMod = count($daftarVerMod);
//comp\FUNC::showPre($data);
?>

<table class="bordered hoverable responsive-table">
    <thead class="hide-on-small-only">
        <tr class="grey darken-3 white-text">
            <th rowspan="2" class="center-align">Moderasi</th>
            <th rowspan="2" class="center-align">Kode</th>
            <th rowspan="2" class="center-align">Tanggal</th>
            <th colspan="4" class="center-align">Verifikasi</th>
            <th rowspan="2" class="center-align">Aksi</th>
            <th rowspan="2" class="center-align">
                check all<br>
                <input type="checkbox" class="filled-in" id="chkCheckAllMod" <?= $nDaftarVerMod < 1 ? 'disabled="disabled"' : "" ?> />
                <label for="chkCheckAllMod" style="padding:10px"></label>
            </th>
            <th rowspan="2" class="center-align">
                <i class="material-icons">vpn_key</i>
            </th>
        </tr>

        <tr class="grey darken-3 white-text">
            <th class="center-align">Admin OPD</th>
            <th class="center-align">Kepala OPD</th>
            <th class="center-align">Admin Kota</th>
            <th class="center-align">Kepala BKPPD</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $numbBg = 0;
        $bgColor = array('yellow lighten-3', 'orange lighten-4');
        $bgHeadColor = array('yellow darken-3 white-text', 'orange darken-3 white-text');

        if ($daftarPegawai['count'] > 0) {
            foreach ($daftarPegawai['value'] as $valPeg) {
                ?>
                <tr class="<?= $bgHeadColor[$numbBg] ?>">
                    <td colspan="10" style="padding-left: 20px">
                        <?= $no++ . '. ' . $valPeg['nama_personil'] ?>
                    </td>
                </tr>
                <?php
                foreach ($daftarVerMod['moderasi'][$valPeg['pin_absen']] as $valMod) {
                    if (!is_null($valMod["flag_kepala_opd"]) || $lockReport == true) {
                        $keylock = "lock";
                        $chkDisabled = 'disabled="disabled"';
                    } else {
                        $chkDisabled = '';
                        $keylock = "lock_open";
                    }

                    if ($valMod['tanggal_awal'] == $valMod['tanggal_akhir']) {
                        $tglMod = comp\FUNC::tanggal($valMod['tanggal_awal'], 'long_date');
                    } else {
                        $tglMod = '<span>' . comp\FUNC::tanggal($valMod['tanggal_awal'], 'long_date') . '</span>';
                        $tglMod .= '<span class="no-s">-</span>';
                        $tglMod .= '<span>' . comp\FUNC::tanggal($valMod['tanggal_akhir'], 'long_date') . '</span>';
                    }
                    ?>
                    <tr class="<?= $bgColor[$numbBg] ?>">
                        <td class="center"><?= $valMod['nama_jenis'] ?></td>
                        
                        <td class="center">
                            <span class="chip brown white-text" title="<?= $valMod["ket_kode_presensi"] ?>" style="cursor: help;">
                                <?= $valMod["kode_presensi"] ?>
                            </span>
                        </td>

                        <td class="center">
                            <?= $tglMod ?>
                        </td>

                        <td id="tdOpOpd-<?= $valMod["id"] ?>" class="center">
                            <?= comp\FUNC::modSymbol($valMod["flag_operator_opd"], $valMod['catatan_operator_opd']) ?>
                        </td>

                        <td id="tdKepOpd-<?= $valMod["id"] ?>"  class="center">
                            <?= comp\FUNC::modSymbol($valMod["flag_kepala_opd"], $valMod["catatan_kepala_opd"]) ?>
                        </td>

                        <td id="tdOpKota-<?= $valMod["id"] ?>"  class="center">
                            <?= comp\FUNC::modSymbol($valMod["flag_operator_kota"], $valMod["catatan_operator_kota"]) ?>
                        </td>

                        <td id="tdKepKota-<?= $valMod["id"] ?>"  class="center">
                            <?= comp\FUNC::modSymbol($valMod["flag_kepala_kota"], $valMod["catatan_kepala_kota"]) ?>
                        </td>

                        <td class="center">
                            <a id="<?= $valMod["id"] ?>" class="btn-info-mod btn-floating waves-effect waves-light blue" title="info">
                                <i class="material-icons">info_outline</i>
                            </a>
                            <?php
                            if (is_null($valMod["flag_kepala_opd"]) && $lockReport == false):
                                ?>
                                <a id="<?= $valMod["id"] ?>" class="btn-tolak-mod btn-floating waves-effect waves-light red" title="saya tolak">
                                    <i class="material-icons">close</i>
                                </a>
                                <a id="<?= $valMod["id"] ?>" class="btn-terima-mod btn-floating waves-effect waves-light green" title="saya terima">
                                    <i class="material-icons">check</i>
                                </a>
                            <?php else: ?>
                                <?php $keylock = "lock"; ?>
                            <?php endif; ?>
                        </td>

                        <td class="center">
                            <input type="checkbox" class="filled-in check-all-mod" id="chkMod<?= $valMod["id"] ?>" value="<?= $valMod["id"] ?>" <?= $chkDisabled ?>  />
                            <label for="chkMod<?= $valMod["id"] ?>" style="margin:10px 0 0 0; padding: 10px;"></label>
                        </td>

                        <td class="center">
                            <i class="material-icons"><?= $keylock ?></i>
                        </td>
                    </tr>
                    <?php
                }
                ($numbBg > 0) ? $numbBg-- : $numbBg++;
            }
        } else {
            ?>
            <tr>
                <td colspan="10" class="center-align">Tidak ada data yang dapat ditampilkan</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
