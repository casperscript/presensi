<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_grup_pengguna', 'text', $kd_grup_pengguna, ' readonly style="color:#cfd8dc;"'); ?>
        <label for="" class="">Kode Grup Pengguna</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_grup_pengguna', 'text', $nama_grup_pengguna, ' required'); ?>
        <label for="" class="">Grup Pengguna</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('batas_moderasi', 'text', $batas_moderasi, ''); ?>
        <label for="" class="">Batas Moderasi</label>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        <?= (($op=='input')) ? '$("#kd_grup_pengguna").val("'.$last_code.'");' : ''; ?>
        Materialize.updateTextFields();
        
    })(jQuery);
</script>