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
    p, label { font-size: calc(50% + 0.8vw); }
    label {background-color: #ff6f00;}
</style>
<body class="search-app quick-results-off loaded">
    <?php $this->getView('pns', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('pns', 'main', 'header', ''); ?>    
        <?php $this->getView('pns', 'main', 'menu', ''); ?>

        <main class="mn-inner">
<div class="row">
    <div class="col s12">
        <div class="row" style="margin-bottom: 0px;">
            <div class="col s10 offset-s1"><h4 class="center" style="padding-top:12px;"><p>Daftar Status Proses Verifikasi PNS yang Mengajukan Moderasi Ketidakhadiran</p></h4></div>
        </div>
        
        <span class="right" style="position: relative; top: -30px;"></span>
    </div>
</div> 
<hr>

<div class="row">
    <div class="col s7">
        <div class="chip"><h5>Filtering</h5></div>
        <div class="row">
            <div class="col s3 input-field">
                <input type="text" id="txtTglFilterAwal" name="txtTglFilterAwal" class="bw-text dtpfilterawal" maxlength="10" value="" cursor="pointer" style="font-size: calc(50% + 0.8vw);">
                <label for="txtTglFilterAwal" class="white-text">Tgl Awal</label>
            </div>
            <div class="col s3 input-field">
                <input type="text" id="txtTglFilterAkhir" name="txtTglFilterAkhir" class="bw-text dtpfilterakhir" maxlength="10" value="" cursor="pointer" style="font-size: calc(50% + 0.8vw);">
                <label for="txtTglFilterAkhir" class="white-text">Tgl Akhir</label>
            </div>
            <div class="col s3">
                <a id="btnFilterTgl" class="waves-effect waves-light btn" action-value="proses">Tampilkan</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div id="divTabelDaftarMod" class="col s12">
        <?= $this->subView('tabel-daftar-mod', $data); ?>
    </div>
</div>
</main>

        
        <!-- /.modal -->
        <?php $this->getView('pns', 'main', 'footer', ''); ?>
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
<script src="<?= $this->link('js/husnanw_moderasi_pns.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main_pns("<?= $this->link("pns/".$this->getController()); ?>", "<?= $data['dateLimit'] ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>

