<?php
use comp\FUNC;

$nDaftarVerMod = count($daftarVerMod)
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
    <div class="col s12" style="height:45px;">
        <div class="row" style="margin-bottom: 0px;">
            <div class="col s10"><h4 class="center" style="padding-top:12px;">Daftar Proses Moderasi PNS yang Mengajukan Moderasi Ketidakhadiran</h4></div>
            <div class="col s2" style="padding-top:8px;"></div>
        </div>
        
        <span class="right" style="position: relative; top: -30px;">
            <?php if ($nDaftarVerMod > 1): ?>
                <a id="btnMassLegit" class="btn red waves-effect waves-light disabled" title="Aktif jika lebih dari satu moderasi dipilih">PENGESAHAN MASSAL</a>
            <?php endif; ?>
        </span>
        <span class="center red-text" style="position: relative; top: -25px;">
            <?php if ($totalBelumVerifikasi > 0): ?>
                <p id="pVerifNotif" class="animated infinite bounce" style="font-size:12px;">Pengajuan moderasi masih ada yang belum diverifikasi oleh Anda sebanyak <b id="bTotal"><?= $totalBelumVerifikasi ?></b> pengajuan.</p>
            <?php endif; ?>
        </span>
    </div>
</div>
<hr>

<input type="hidden" id="page" value="1" />
<div class="row">
    <div class="col s12">
        <div id="divTabelVerMod">
        <?= $this->subView('tabel-ver-mod', $data); ?>
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
      <a href="#!" class="modal-action modal-close waves-effect waves-light btn"><span style="position: relative; top: -2px;">Terima Kasih</span> <i class="material-icons">thumb_up</i></a>
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
    husnanw_moderasi_main_kepala_bkppd("<?= $this->link('kepalabkppd/'.$this->getController()); ?>", "<?= $data['kodeLokasi'] ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>

