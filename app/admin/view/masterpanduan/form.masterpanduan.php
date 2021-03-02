<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_panduan', 'text', $kd_panduan, ' readonly style="color:#cfd8dc;"'); ?>
        <label for="" class="">Kode Panduan</label>
        <div id="cekprimary"></div>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_panduan', 'text', $nama_panduan, ' required'); ?>
        <label for="" class="">Nama Panduan</label>
    </div>
    <div class="input-field col s12">
        <div class="file-field input-field">
            <div class="btn teal lighten-1">
                <span>Pilih File</span>
                <input id="file" name="file" type="file">
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" placeholder="<?= $file_panduan;?>">
            </div>
        </div>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        <?= (($op=='input')) ? '$("#kd_panduan").val("'.$last_code.'");' : ''; ?>
        Materialize.updateTextFields();
        
    })(jQuery);
</script>