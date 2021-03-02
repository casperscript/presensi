<?php
use comp\FUNC;
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
    <?php $this->getView('admin', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('admin', 'main', 'header', ''); ?>    
        <?php $this->getView('admin', 'main', 'menu', ''); ?>

        <main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="row" style="margin-bottom: 0px;">
                <div class="col s10 offset-s1"><h4 class="center" style="padding-top:12px;">Daftar Hasil Akhir Moderasi PNS yang Mengajukan Moderasi Ketidakhadiran</h4></div>
            </div>
        </div>
    </div>
<hr>

<div class="row">
    <div class="col s12">
        <div class="input-field col s4 right">
            <select id="selDaftarOpdVerMod" isfinal="1">
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
        </div>
    </div>
</div>
</main>
        <!-- /.modal -->
        <?php $this->getView('admin', 'main', 'footer', ''); ?>
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
<form id="frmDokumenPendukung" class="dropzone" method="post" action="<?= $this->link($this->getProject(). 'upload/mod') ?>" style="display:none;"></form> 
<script src="<?= $this->link($this->getProject(). 'js/dropzone.js'); ?>"></script>
<script src="<?= $this->link($this->getProject(). 'js/husnanw_moderasi.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main("<?= $this->link($this->getProject() . $this->getController()); ?>", "<?= $data['dateLimit'] ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>

