<?php
$nDaftarVerMod = count($daftarVerMod);
comp\FUNC::showPre($data);
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

    <?php
    $lastPin = '';
    $no = 1;
    $numbBg = 0;
    $bgColor = array('yellow lighten-3', 'orange lighten-4');
    $bgHeadColor = array('yellow darken-3 white-text', 'orange darken-3 white-text');
    ?>

    <?php
    foreach ($daftarVerMod as $index => $val):

        $keylock = "lock_open";
        $chkDisabled = '';
        $nRowSpan = 0;

        foreach ($daftarVerMod as $v2) {
            $nRowSpan += count(array_keys($v2, $val["pin_absen"]));
        }

        //if (!is_null($val["flag_kepala_opd"]) || $val["usergroup"] !== "KDGRUP05") {
        if (!is_null($val["flag_kepala_opd"])) {
            $keylock = "lock";
            $chkDisabled = 'disabled="disabled"';
        }
        ?>

        <?php if ($lastPin !== $val["pin_absen"]): ?>
            <tbody class="<?= $bgColor[$numbBg] ?>">

                <tr class="<?= $bgHeadColor[$numbBg] ?>">
                    <td colspan="10" style="padding-left: 20px">
                        <?= $no++ . '. ' . $val["nama_lengkap"] ?>
                    </td>
                </tr>
                <?php
                $numbBg = ($numbBg == 0) ? $numbBg + 1 : $numbBg - 1;
                $lastPin = $val["pin_absen"];
            endif;
            ?>
            <tr>

                <td class="center">
                    <?= $val["nama_jenis"] ?>
                </td>

                <td class="center">
                    <span class="chip brown white-text" title="<?= $val["ket_kode_presensi"] ?>" style="cursor: help;">
                        <?= $val["kode_presensi"] ?>
                    </span>
                </td>

                <td class="center">
                    <span><?= comp\FUNC::tanggal($val["tanggal_awal"], 'long_date') ?></span><br />
                    <span><?= comp\FUNC::tanggal($val["tanggal_akhir"], 'long_date') ?></span><br />
                </td>

                <td id="tdOpOpd-<?= $val["id"] ?>" class="center" title="CATATAN: <?= $val["catatan_operator_opd"] ?>" style="cursor: help;">
                    <?= comp\FUNC::modSymbol($val["flag_operator_opd"]) ?>
                </td>

                <td id="tdKepOpd-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_opd"] ?>" style="cursor: help;">
                    <?= comp\FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?>
                </td>

                <td id="tdOpKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_operator_kota"] ?>" style="cursor: help;">
                    <?= comp\FUNC::husnanWVerModStyle($val["flag_operator_kota"]) ?>
                </td>

                <td id="tdKepKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_kota"] ?>" style="cursor: help;">
                    <?= comp\FUNC::husnanWVerModStyle($val["flag_kepala_kota"]) ?>
                </td>

                <td class="center">
                    <a class="btn-info-mod btn-floating waves-effect waves-light blue" title="info" mid="<?= $val["id"] ?>"><i class="material-icons">info_outline</i></a>
                    <?php
                    //if(is_null($val["flag_kepala_opd"]) && $val["usergroup"] === "KDGRUP05"):
                    if (is_null($val["flag_kepala_opd"])):
                        ?>
                        <a class="btn-tolak-mod btn-floating waves-effect waves-light red" title="saya tolak" mid="<?= $val["id"] ?>">
                            <i class="material-icons">close</i>
                        </a>
                        <a class="btn-terima-mod btn-floating waves-effect waves-light green" title="saya terima" mid="<?= $val["id"] ?>">
                            <i class="material-icons">check</i>
                        </a>
                    <?php else: ?>
                        <?php $keylock = "lock"; ?>
                    <?php endif; ?>
                </td>

                <td class="center">
                    <input type="checkbox" class="filled-in check-all-mod" id="chkMod<?= $val["id"] ?>" value="<?= $val["id"] ?>" <?= $chkDisabled ?>  />
                    <label for="chkMod<?= $val["id"] ?>" style="margin:10px 0 0 0; padding: 10px;"></label>
                </td>

                <td class="center">
                    <i class="material-icons"><?= $keylock ?></i>
                </td>

            </tr>        
        <?php endforeach; ?>
        <?php if ($lastPin !== $val['pin_absen']) : ?>
        </tbody>
    <?php endif; ?>
</table>
