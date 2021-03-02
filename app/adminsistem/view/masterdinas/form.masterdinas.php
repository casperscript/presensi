<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_jenis_dinas', 'text', $kd_jenis_dinas, ' readonly style="color:#cfd8dc;"'); ?>
        <label for="" class="">Kode Jenis Dinas</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_jenis_dinas', 'text', $nama_jenis_dinas, ' required'); ?>
        <label for="" class="">Jenis Dinas</label>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        <?= (($op=='input')) ? '$("#kd_jenis_dinas").val("'.$last_code.'");' : ''; ?>
        Materialize.updateTextFields();
        
    })(jQuery);
</script>