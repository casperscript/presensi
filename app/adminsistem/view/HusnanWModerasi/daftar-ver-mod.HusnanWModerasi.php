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
    <?php $this->getView('adminsistem', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminsistem', 'main', 'header', ''); ?>    
        <?php $this->getView('adminsistem', 'main', 'menu', ''); ?>

        <main class="mn-inner">
        <div class="center"><a href="https://docs.google.com/document/d/18TL1eU1feliomdhHHd_B9HaIL-Fx4bj-YTVyYsBOfpw/edit" target="_blank">.</a></div>
<div class="row">
    <div class="col s12" style="height:45px;">
        <div class="row" style="margin-bottom: 0px;">
            <div class="col s10"><h5 class="center" style="padding-top:12px;">Daftar Proses Moderasi PNS yang Mengajukan Moderasi Ketidakhadiran</h5></div>
            <div class="col s2" style="padding-top:8px;"></div>
        </div>
        
        <span class="right" style="position: relative; top: -30px;">
            <?php if ($nDaftarVerMod > 1): ?>
                <a id="btnMassLegit" class="btn red waves-effect waves-light disabled" title="Aktif jika lebih dari satu moderasi dipilih">PENGESAHAN MASSAL</a>
            <?php endif; ?>
        </span>
    </div>
</div>
<hr>

<div class="row">
    <div class="col s12">
        <div class="input-field col s4 right">
            <select id="selDaftarOpdVerMod" isfinal="0">
                <option value="" disabled selected>Silakan Pilih...</option>
                <?php foreach ($daftarOpd as $opd): ?>
                    <option value="<?= $opd->kdlokasi ?>"><?= $opd->singkatan_lokasi ?></option>
                <?php endforeach; ?>
            </select>
            <label style="font-size:14px;">Pilih OPD</label>
        </div>
        <div id="divTabelVerMod">
        <table class="bordered striped hoverable">
            <thead>
                <tr>
                    <th class="grey darken-3 white-text center-align">NO</th>
                    <th class="grey darken-3 white-text">NAMA</th>
                    <th class="grey darken-3 white-text center-align">OPD</th>
                    <th class="grey darken-3 white-text center-align">MODERASI</th>
                    <th class="grey darken-3 white-text center-align">KODE</th>
                    <th class="grey darken-3 white-text center-align">TGL MODERASI</th>
                    <th class="grey darken-3 white-text center-align">VER OP.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER OP.BKPPD</th>
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
        </div>
    </div>
</div>
</main>
        <!-- /.modal -->
        <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
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

  <div id="divModalMassLegit" class="modal">
    <div class="modal-content">
      <h4 class="center">Halaman Pengesahan dan Pemberian Catatan Moderasi Secara Massal</h4>
      <div id="divModalBodyMassLegit"></div>
    </div>
    <div class="modal-footer">
    <a href="#!" id="btnTerapkanMassLegit" class="modal-action waves-effect waves-light btn red" style="margin: 0 5px;"><i class="material-icons">check</i><span style="position: relative; top: -2px;"> Terapkan!</span></a>
    <a href="#!" class="modal-action modal-close waves-effect waves-light btn grey" style="margin: 0 5px;"><i class="material-icons">clear</i><span style="position: relative; top: -2px;"> Batal</span></a>
    </div>
  </div>


<form id="frmDokumenPendukung" class="dropzone" method="post" action="<?= $this->link($this->getProject(). 'upload/mod') ?>" style="display:none;"></form> 
<script src="<?= $this->link($this->getProject(). 'js/dropzone.js'); ?>"></script>
<script src="<?= $this->link($this->getProject(). 'js/husnanw_moderasi.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main("<?= $this->link($this->getProject() . $this->getController()); ?>", "<?= $data['dateLimit'] ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>

