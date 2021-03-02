<?php
use comp\FUNC;
$nDaftarVerMod = count($daftarVerMod);

?>

<style>
    .mn-content, body, html {
        font-size: 10px;
    }
    .chip {
        font-size: 10px;
    }
    td, th {
        padding: 5px;
    }
</style>
<body class="search-app quick-results-off loaded">
    <?php $this->getView('adminopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>

        <main class="mn-inner">
        <div class="center"><a href="https://docs.google.com/document/d/18TL1eU1feliomdhHHd_B9HaIL-Fx4bj-YTVyYsBOfpw/edit" target="_blank">.</a></div>
<div class="row">
    <div class="col s12">
        <div class="row" style="margin-bottom: 0px;">
            <div class="col s10 offset-s1"><h4 class="center" style="padding-top:12px;">Daftar Proses Status Verifikasi PNS yang Mengajukan Moderasi Ketidakhadiran</h4></div>
        </div>
        
        <span class="right" style="position: relative; top: 5px;">
            <?php if ($nDaftarVerMod > 1): ?>
                <a id="btnTerimaSemua" class="btn btn-mass-verif green waves-effect waves-light disabled" flag="1" title="Diaktifkan saat lebih dari satu moderasi dipilih">Terima Semua</a>
            <?php endif; ?>
        </span>
        <span class="right" style="position: relative; top: 5px;">
            <?php if ($nDaftarVerMod > 1): ?>
                <a id="btnTolakSemua" class="btn btn-mass-verif red waves-effect waves-light disabled" flag="0" title="Diaktifkan saat lebih dari satu moderasi dipilih">Tolak Semua</a>
            <?php endif; ?>
        </span>
    </div>
</div>
<hr>

<div class="row">
    <div class="col s12">
    <div id="divTabelVerMod">
        <table class="bordered striped hoverable">
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

                    <th class="grey darken-3 white-text center-align">AKSI</th>
                    <th class="grey darken-3 white-text center-align">check all<br><input type="checkbox" class="filled-in" id="chkCheckAllMod" <?= $nDaftarVerMod < 1 ? 'disabled="disabled"' : "" ?> />
                        <label for="chkCheckAllMod" style="padding:10px"></label></th>
                    <th class="grey darken-3 white-text center-align"><i class="material-icons">vpn_key</i></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $lastPin = '';
                    $no = 1;
                ?>
                
                <?php foreach ($daftarVerMod as $index => $val): ?>
                    <?php
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
                    <tr>
                        <?php if ($lastPin !== $val["pin_absen"]): ?>                    
                            <td class="center" rowspan="<?= $nRowSpan ?>"><?= $no++ ?></td>
                            <td rowspan="<?= $nRowSpan ?>"><?= $val["nama_lengkap"] ?></td>
                            <?php $lastPin = $val["pin_absen"]; ?>
                        <?php endif; ?>
                        <td class="center"><?= $val["nama_jenis"] ?></td>
                        <td class="center"><span class="chip brown white-text" title="<?= $val["ket_kode_presensi"] ?>" style="cursor: help;"><?= $val["kode_presensi"] ?></span></td>
                        <td class="center">&nbsp; &nbsp;<?= FUNC::toHusnanWSniDate($val["tanggal_awal"]) ?> -<br><?= FUNC::toHusnanWSniDate($val["tanggal_akhir"]) ?></td>
                        <td id="tdOpOpd-<?= $val["id"] ?>" class="center" title="CATATAN: <?= $val["catatan_operator_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_opd"]) ?></td>
                        <td id="tdKepOpd-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?></td>
                        <td id="tdOpKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_operator_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_kota"]) ?></td>
                        <td id="tdKepKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_kota"]) ?></td>
                        <td class="center">
                            <a class="btn-info-mod btn-floating waves-effect waves-light blue" title="info" mid="<?= $val["id"] ?>"><i class="material-icons">info_outline</i></a>
                            <?php //if(is_null($val["flag_kepala_opd"]) && $val["usergroup"] === "KDGRUP05"):
                            if(is_null($val["flag_kepala_opd"])): ?>
                                <a class="btn-tolak-mod btn-floating waves-effect waves-light red" title="saya tolak" mid="<?= $val["id"] ?>"><i class="material-icons">close</i></a>
                                <a class="btn-terima-mod btn-floating waves-effect waves-light green" title="saya terima" mid="<?= $val["id"] ?>"><i class="material-icons">check</i></a>
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
            </tbody>
        </table>
        </div>
    </div>
</div>
</main>

        
        <!-- /.modal -->
        <?php $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>
</body>

<div id="divModalInfoModerasi" class="modal">
    <div class="modal-content">
      <h4>Informasi Detil Pengajuan Moderasi</h4>
      <div id="divModalBody"></div>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-action modal-close waves-effect waves-green btn"><span style="position: relative; top: -2px;">Terima Kasih</span> <i class="material-icons">thumb_up</i></a>
    </div>
  </div>

  <div id="divModalMassVerif" class="modal">
    <div class="modal-content">
      <div id="divModalBodyMassVerif"></div>
    </div>
    <div class="modal-footer">
    <a href="#!" id="btnTerapkanMassVerif" class="modal-action waves-effect waves-light btn red" style="margin: 0 5px;"><i class="material-icons">check</i><span style="position: relative; top: -2px;"> Terapkan!</span></a>
    <a href="#!" class="modal-action modal-close waves-effect waves-light btn grey" style="margin: 0 5px;"><i class="material-icons">clear</i><span style="position: relative; top: -2px;"> Batal</span></a>
    </div>
  </div>

<form id="frmDokumenPendukung" class="dropzone" method="post" action="<?= $this->link($this->getProject(). 'upload/mod') ?>" style="display:none;"></form> 
<script src="<?= $this->link('js/dropzone.js'); ?>"></script>
<script src="<?= $this->link('js/husnanw_moderasi_admin_opd.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main_admin_opd("<?= $this->link("adminopd/".$this->getController()); ?>", "<?= $data['dateLimit'] ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>

