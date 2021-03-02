<?php
use comp\FUNC; 
$nDaftarVerMod = count($daftarVerMod);
?>
<table class="bordered striped hoverable">
            <thead>
                <tr>
                    <th class="grey darken-3 white-text center-align">NO<br>BARIS</th>
                    <th class="grey darken-3 white-text">NAMA</th>
                    <th class="grey darken-3 white-text center-align">OPD</th>
                    <th class="grey darken-3 white-text center-align">MODERASI</th>
                    <th class="grey darken-3 white-text center-align">KODE</th>
                    <th class="grey darken-3 white-text center-align">TGL MODERASI</th>
                    <th class="grey darken-3 white-text center-align">VER ADM.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER ADM.KOTA</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.BKPPD</th>

                    <th class="grey darken-3 white-text center-align">AKSI</th>
                    <th class="grey darken-3 white-text center-align">check all<br><input type="checkbox" class="filled-in" id="chkCheckAllMod" <?= $nDaftarVerMod < 1 ? 'disabled="disabled"' : "" ?> />
                        <label for="chkCheckAllMod" style="padding:10px"></label></th>
                    <th class="grey darken-3 white-text center-align"><i class="material-icons">vpn_key</i></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $lastPin = '';
                    //$personNo = 0;
                ?>
                <?php foreach ($daftarVerMod as $index => $val): ?>
                    <?php 
                        $keylock = "lock_open";
                        $chkDisabled = '';
                        $nRowSpan = 0;

                        foreach ($daftarVerMod as $v2) {
                            $nRowSpan += count(array_keys($v2, $val["pin_absen"]));
                        }

                        if (!is_null($val["flag_kepala_kota"])) {
                            $keylock = "lock";
                            $chkDisabled = 'disabled="disabled"';
                        }
                    ?>
                    <tr>
                        <?php if ($lastPin !== $val["pin_absen"]): ?>     
                            <td class="center" rowspan="<?= $nRowSpan ?>"><?= $no++ ?></td>
                            <td rowspan="<?= $nRowSpan ?>"><?= $val["nama_lengkap"] ?></td>
                            <?php $lastPin = $val["pin_absen"]; ?>
                        <?php endif; ?>
                        <td class="center"><?= $val["singkatan_lokasi"] ?></td>
                        <td class="center"><?= $val["nama_jenis"] ?></td>
                        <td class="center"><span class="chip brown white-text" title="<?= $val["ket_kode_presensi"] ?>" style="cursor: help;"><?= $val["kode_presensi"] ?></span></td>
                        <td class="center">&nbsp; &nbsp;<?= FUNC::toHusnanWSniDate($val["tanggal_awal"]) ?> -<br><?= FUNC::toHusnanWSniDate($val["tanggal_akhir"]) ?></td>
                        <td id="tdOpOpd-<?= $val["id"] ?>" class="center" title="CATATAN: <?= $val["catatan_operator_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_opd"]) ?></td>
                        <td id="tdKepOpd-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?></td>
                        <td id="tdOpKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_operator_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_kota"]) ?></td>
                        <td id="tdKepKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_kota"]) ?></td>
                        <td class="center">
                            <a class="btn-info-mod btn-floating waves-effect waves-light blue" title="info" mid="<?= $val["id"] ?>"><i class="material-icons">info_outline</i></a>
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
            </tbody>
        </table>
        <?= comp\MATERIALIZE::pagging($page, $limiter, $total); ?>