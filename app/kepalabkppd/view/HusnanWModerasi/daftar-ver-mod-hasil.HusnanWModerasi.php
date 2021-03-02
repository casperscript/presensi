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
    <?php $this->getView('kepalabkppd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('kepalabkppd', 'main', 'header', ''); ?>    
        <?php $this->getView('kepalabkppd', 'main', 'menu', ''); ?>

        <main class="mn-inner">
<div class="row">
    <div class="col s12">
        <div class="row" style="margin-bottom: 0px;">
            <div class="col s10"><h5 class="center" style="padding-top:12px;">Daftar Hasil Akhir Moderasi PNS yang Mengajukan Moderasi Ketidakhadiran</h5></div>
            <div class="col s2" style="padding-top:8px;"></div>
        </div>
        
        <span class="right" style="position: relative; top: -30px;"></span>
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
            <?= $this->subView('tabel-ver-mod-hasil', $data); ?>
        </div>
    </div>
</div>
</main>

        
        <!-- /.modal -->
        <?php $this->getView('kepalabkppd', 'main', 'footer', ''); ?>
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
<form id="frmDokumenPendukung" class="dropzone" method="post" action="<?= $this->link('upload/mod') ?>" style="display:none;"></form> 
<script src="<?= $this->link('js/dropzone.js'); ?>"></script>
<script src="<?= $this->link('js/husnanw_moderasi_kepala_bkppd.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main_kepala_bkppd("<?= $this->link('kepalabkppd/'.$this->getController()); ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>

