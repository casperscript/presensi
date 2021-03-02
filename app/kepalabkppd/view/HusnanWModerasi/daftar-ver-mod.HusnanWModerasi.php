<?php
use comp\FUNC;
if (isset($notallowed)) {
    echo '<div class="alert-verifikasi">
        <span class="blink">
            <i class="fa fa-info-circle"></i>
            Laporan Belum Diverifikasi oleh Admin BKPPD
        </span>
    </div>';
    exit;
}

$nDaftarVerMod = count($daftarVerMod);
?>
<?php if ($totalBelumVerifikasi > 0): ?>
    <div class="green accent-1" style="margin: -30px 0 -5px 0">
        <p id="pVerifNotif" class="animated infinite pulse center black-text" style="padding: 10px; font-weight: 400;">
            Pengajuan moderasi masih ada yang belum diverifikasi oleh Anda sebanyak <b class="red-text" style="font-size: 18px" id="bTotal"><?= $totalBelumVerifikasi ?></b> pengajuan.
        </p>
    </div>
<?php endif; ?>
<table class="bordered striped hoverable">
    <thead>
        <tr class="grey darken-3 white-text">
            <th rowspan="2" class="center-align">Moderasi</th>
            <th rowspan="2" class="center-align">Kode</th>
            <th rowspan="2" class="center-align">Tanggal</th>
            <th colspan="4" class="center-align">Verifikasi</th>
            <th rowspan="2" class="center-align">Aksi</th>
            <th rowspan="2" class="center-align">check all<br><input type="checkbox" class="filled-in" id="chkCheckAllMod" <?= $nDaftarVerMod < 1 ? 'disabled="disabled"' : "" ?> />
                <label for="chkCheckAllMod" style="padding:10px"></label></th>
            <th rowspan="2" class="center-align"><i class="material-icons">vpn_key</i></th>
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
            if ($nDaftarVerMod == 0) {
                echo '<tr><td colspan="10" class="center-align">Tidak ada data yang dapat ditampilkan</td>
                </tr>';
            }
            $lastPin = '';
            $no = 1;
            $numbBg = 0;
            $new = true;
            $name = '';
            $bgColor = array('orange lighten-4', 'yellow lighten-3');
            $bgHeadColor = array('yellow darken-3 white-text', 'orange darken-3 white-text');
        ?>
        <?php foreach ($daftarVerMod as $index => $val): ?>
            <?php
            $keylock = "lock_open";
            $chkDisabled = '';
            $nRowSpan = 0;

            foreach ($daftarVerMod as $v2) {
                $nRowSpan += count(array_keys($v2, $val["pin_absen"]));
            }

            if ($val["flag_kepala_opd"] === "2" || $val["flag_kepala_opd"] === "3") {
                $keylock = "lock";
                $chkDisabled = 'disabled="disabled"';
            }

            if ($val['tanggal_awal'] == $val['tanggal_akhir']) {
                $tglMod = comp\FUNC::tanggal($val['tanggal_awal'], 'long_date');
            } else {
                $tglMod = '<span>' . comp\FUNC::tanggal($val['tanggal_awal'], 'long_date') . '</span>';
                $tglMod .= '<span class="no-s"> - </span>';
                $tglMod .= '<span>' . comp\FUNC::tanggal($val['tanggal_akhir'], 'long_date') . '</span>';
            }

            //$rowClass = "";
            /*if ($val["flag_kepala_opd"] === "2") {
                $rowClass = "green lighten-5 green-text";
            } elseif ($val["flag_kepala_opd"] === "3") {
                $rowClass = "red lighten-5 grey-text";
            }*/

            if ($val["nama_lengkap"] != $name)
                $new = true;

            if ($new) {
                $name = $val["nama_lengkap"];
                $new = false;

                echo '<tr class="'.$bgHeadColor[$numbBg].'"><td colspan="10">'.$no.'. &nbsp'.$val["nama_lengkap"].'</td></tr>';
                $numbBg = ($numbBg == 0) ? $numbBg + 1 : $numbBg - 1;
                $no++;
            }

            ?>
            <tr class="<?= $bgColor[$numbBg] ?>">
                <td class="center"><?= $val["nama_jenis"] ?></td>
                <td class="center"><span class="chip brown white-text" title="<?= $val["ket_kode_presensi"] ?>" style="cursor: help;"><?= $val["kode_presensi"] ?></span></td>
                <td class="center"><?= $tglMod ?></td>
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

<div id="divModalInfoModerasi" class="modal">
    <div class="modal-content">
      <h4>Informasi Detil Pengajuan Moderasi</h4>
      <div id="divModalBody"></div>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-action modal-close waves-effect waves-green btn"><span style="position: relative; top: -2px;">Terima Kasih</span> <i class="material-icons">thumb_up</i></a>
    </div>
  </div>

  <div id="divModalMassLegit" class="modal">
    <div class="modal-content">
      <h4 class="center">Halaman Pengesahan dan Pemberian Catatan Moderasi Secara Massal</h4>
      <div id="divModalBodyMassLegit"></div>
    </div>
    <div class="modal-footer">
    <input type="hidden" id="kdlokasi" value="<?= $kodeLokasi ?>"> <!--added by daniek-->
    <a href="#!" id="btnTerapkanMassLegit" class="modal-action waves-effect waves-light btn red" style="margin: 0 5px;"><i class="material-icons">check</i><span style="position: relative; top: -2px;"> Terapkan!</span></a>
    <a href="#!" class="modal-action modal-close waves-effect waves-light btn grey" style="margin: 0 5px;"><i class="material-icons">clear</i><span style="position: relative; top: -2px;"> Batal</span></a>
    </div>
  </div>

<script src="<?= $this->link('js/husnanw_moderasi_kepala_bkppd.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main_kepala_bkppd("<?= $this->link('kepalabkppd/'.$this->getController()); ?>");                
    })(jQuery);
</script>