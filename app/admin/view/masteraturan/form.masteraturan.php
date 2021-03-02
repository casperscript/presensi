<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_aturan', 'text', $kd_aturan, ' readonly style="color:#cfd8dc;"'); ?>
        <label for="" class="">Kode Aturan</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('indikator_aturan', 'text', $indikator_aturan, ' required'); ?>
        <label for="" class="">Indikator Aturan</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputSelect('status_aturan', $pil_status_aturan, $status_aturan, ' required'); ?>
        <label for="" class="">Status Aturan</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('batas_waktu_aturan', 'text', $batas_waktu_aturan, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s:s&apos;" required'); ?>
        <label for="" class="">Batas Waktu</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('potongan_tpp_aturan', 'text', $potongan_tpp_aturan, ' required'); ?>
        <label for="" class="">Potongan TPP</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('dasar_hukum_aturan', 'text', $dasar_hukum_aturan, ''); ?>
        <label for="" class="">Dasar Hukum</label>
    </div>
</div>
<script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
<script>
    (function ($) {
        "use strict";
        
        $(".masked").inputmask();
        $("select").material_select();
        
        <?= (($op=='input')) ? '$("#kd_aturan").val("'.$last_code.'");' : ''; ?>
        Materialize.updateTextFields();
        
    })(jQuery);
</script>