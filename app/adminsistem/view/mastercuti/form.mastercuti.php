<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_jenis_cuti', 'text', $kd_jenis_cuti, ' readonly style="color:#cfd8dc;"'); ?>
        <label for="" class="">Kode Jenis Cuti</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_jenis_cuti', 'text', $nama_jenis_cuti, ' required'); ?>
        <label for="" class="">Jenis Cuti</label>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        <?= (($op=='input')) ? '$("#kd_jenis_cuti").val("'.$last_code.'");' : ''; ?>
        Materialize.updateTextFields();
        
    })(jQuery);
</script>