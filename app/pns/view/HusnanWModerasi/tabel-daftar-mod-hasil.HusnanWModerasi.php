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
                    <th class="grey darken-3 white-text center-align">TGL PENGAJUAN</th>
                    <th class="grey darken-3 white-text center-align">TGL MODERASI</th>
                    <th class="grey darken-3 white-text center-align">STATUS AKHIR</th>
                    <th class="grey darken-3 white-text center-align">POTONGAN</th>
                    <th class="grey darken-3 white-text center-align">DETAIL</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($daftarMod) < 1): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color:#f00;">Tidak ada data yang dapat ditampilkan dalam rentang tanggal tersebut...</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($daftarMod as $index => $val): ?>
                    <?php
                    $rowColor = "";
                    $textDecor = "none";

                    if ($val["flag_kepala_opd"] === "3") {
                        $rowColor = "red lighten-5 red-text";
                        $textDecor = "line-through";
                    } else {
                        $rowColor = "green lighten-5 green-text";
                    }
                    ?>
                    <tr class="<?= $rowColor ?>" style="text-decoration: <?= $textDecor ?>;">
                        <td class="center"><?= $index + 1 ?></td>
                        <td><?= $val["nama_lengkap"] ?></td>
                        <td class="center"><?= $val["nama_jenis"] ?></td>
                        <td class="center" title="<?= $val["ket_kode_presensi"] ?>" style="cursor:help;"><span class="chip brown white-text"><?= $val["kode_presensi"] ?></span></td>
                        <td class="center"><?= FUNC::toHusnanWSniDateTime($val["dt_created"]) ?></td>
                        <td class="center" title="Moderasi selama: <?= FUNC::getHusnanWDeltaDates($val["tanggal_awal"], $val["tanggal_akhir"]) + 1 ?> hari" style="cursor:help;"><?= $val["tanggal_awal"]." -<br>".$val["tanggal_akhir"] ?></td>
                        <td class="center" title="Catatan Final Kepala OPD: <?= $val["catatan_final_kepala_opd"] ?>" style="cursor:help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?></td>
                        <td class="center"><?= floatval($val["pot_kode_presensi"]) * 100 ?> %</td>
                        <td class="center">
                            <a class="btn-info-mod btn-floating waves-effect waves-light blue" title="info" mid="<?= $val["id"] ?>" isfinal="true"><i class="material-icons">info_outline</i></a>
                        </td>
                    </tr>        
                <?php endforeach; ?>
            </tbody>
        </table>