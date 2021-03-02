<?php
use comp\FUNC;
?>
<table class="bordered striped hoverable responsive-table">
            <thead>
                <tr>
                    <th class="grey darken-3 white-text center-align">NO</th>
                    <th class="grey darken-3 white-text">NAMA</th>
                    <th class="grey darken-3 white-text center-align">MODERASI</th>
                    <th class="grey darken-3 white-text center-align">KODE</th>
                    <th class="grey darken-3 white-text center-align">TGL MODERASI</th>
                    <th class="grey darken-3 white-text center-align">VER ADM.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER ADM.KOTA</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.BKPPD</th>

                    <th class="grey darken-3 white-text center-align">INFO</th>
                    <th class="grey darken-3 white-text center-align"><i class="material-icons">vpn_key</i></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($daftarMod) < 1): ?>
                    <tr>
                        <td colspan="11" style="text-align: center; color:#f00;">Tidak ada data yang dapat ditampilkan dalam rentang tanggal tersebut...</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($daftarMod as $index => $val): ?>
                    <?php
                    $keylock = "lock_open";
                    $rowColor = "";

                    if ($val["flag_kepala_opd"] === "2" || $val["flag_kepala_opd"] === "3") {
                        $keylock = "lock";
                        $rowColor = "grey lighten-2 grey-text";
                    }
                    ?>
                    <tr class="<?= $rowColor ?>">
                        <td class="center"><?= $index + 1 ?></td>
                        <td><?= $val["nama_lengkap"] ?></td>
                        <td class="center"><?= $val["nama_jenis"] ?></td>
                        <td class="center"><span class="chip brown white-text" title="<?= $val["ket_kode_presensi"] ?>" style="cursor: help;"><?= $val["kode_presensi"] ?></span></td>
                        <td class="center" title="Tgl Pengajuan: <?= FUNC::toHusnanWSniDateTime($val["dt_created"]) ?>" style="cursor: help;">&nbsp; &nbsp;<?= FUNC::toHusnanWSniDate($val["tanggal_awal"]) ?> -<br><?= FUNC::toHusnanWSniDate($val["tanggal_akhir"]) ?></td>
                        <td id="tdOpOpd-<?= $val["id"] ?>" class="center" title="<?= $val["catatan_operator_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_opd"]) ?></td>
                        <td id="tdKepOpd-<?= $val["id"] ?>"  class="center" title="<?= $val["catatan_kepala_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?></td>
                        <td id="tdOpKota-<?= $val["id"] ?>"  class="center" title="<?= $val["catatan_operator_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_kota"]) ?></td>
                        <td id="tdKepKota-<?= $val["id"] ?>"  class="center" title="<?= $val["catatan_kepala_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_kota"]) ?></td>
                        <td class="center">
                            <a class="btn-info-mod btn-floating waves-effect waves-light blue" title="info" mid="<?= $val["id"] ?>"><i class="material-icons">info_outline</i></a>
                        </td>
                        <td class="center">
                            <i class="material-icons"><?= $keylock ?></i>
                        </td>
                    </tr>        
                <?php endforeach; ?>
            </tbody>
        </table>