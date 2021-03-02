<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_jenis', 'text', $kd_jenis, ' readonly style="color:#cfd8dc;"'); ?>
        <label for="" class="">Kode Jenis</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_jenis', 'text', $nama_jenis, 'required'); ?>
        <label for="" class="">Jenis Moderasi</label>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        $("select").material_select();        
        <?= (($op=='input')) ? '$("#kd_jenis").val("'.$last_code.'");' : ''; ?>
        Materialize.updateTextFields();
        
    })(jQuery);
</script>
