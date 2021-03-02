<?php
use comp\FUNC; 
$nDaftarVerMod = count($daftarVerMod);
?>

<?php if($nDaftarVerMod < 1): ?>
    <div class="center" style="margin-top:100px;">
        <p class="chip red white-text" style="font-size:20px;">Belum ada data yang dapat ditampilkan...</p>
    </div>
<?php else: ?>
    <table class="bordered striped hoverable animated rubberBand">
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
                <?php 
                $lastPin = '';
                $no = 1;
                ?>
                <?php foreach ($daftarVerMod as $index => $val): ?>
                    <?php
                    $rowColor = "";
                    $textDecor = "none";
                    $nRowSpan = 0;

                    foreach ($daftarVerMod as $v2) {
                        $nRowSpan += count(array_keys($v2, $val["pin_absen"]));
                    }

                    if ($val["flag_kepala_opd"] === "3") {
                        $rowColor = "red lighten-5 red-text";
                        $textDecor = "line-through";
                    } else {
                        $rowColor = "green lighten-5 green-text";
                    }
                    ?>
                    <tr class="<?= $rowColor ?>" style="text-decoration: <?= $textDecor ?>;">
                        <?php if ($lastPin !== $val["pin_absen"]): ?>         
                            <td class="center" rowspan="<?= $nRowSpan ?>"><?= $no++ ?></td>
                            <td rowspan="<?= $nRowSpan ?>"><?= $val["nama_lengkap"] ?></td>
                            <?php $lastPin = $val["pin_absen"]; ?>
                        <?php endif; ?>
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
<?php endif; ?>