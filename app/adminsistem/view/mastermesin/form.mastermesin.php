<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_mesin', $id_mesin); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('id_kelompok_mesin', $pil_kelompok_mesin, $id_kelompok_mesin, ' required'); ?>
        <label for="" class="">Kelompok Mesin</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_mesin', 'text', $nama_mesin, ' required'); ?>
        <label for="" class="">Nama Mesin</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('ip_mesin', 'text', $ip_mesin, ' required'); ?>
        <label for="" class="">IP Mesin</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('port_mesin', 'text', $port_mesin, ' required'); ?>
        <label for="" class="">Port Mesin</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('password_mesin', 'text', $password_mesin, ' required'); ?>
        <label for="" class="">Password Mesin</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('serial_mesin', 'text', $serial_mesin, ' required'); ?>
        <label for="" class="">Serial Mesin</label>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        $("select").material_select();
        Materialize.updateTextFields();
        
    })(jQuery);
</script>