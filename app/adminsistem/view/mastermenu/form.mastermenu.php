<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_menu', $id_menu); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_menu', 'text', $nama_menu, ' required'); ?>
        <label for="" class="">Nama Menu</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('letak_menu', 'text', $letak_menu, ' required'); ?>
        <label for="" class="">Letak Menu</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('urut_menu_menu', 'text', $urut_menu_menu, ' required'); ?>
        <label for="" class="">Urut Menu</label>
    </div>
    <div class="input-field col s12">
        <div id="resultForm"></div>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        var aksi = "<?= $op;?>";        
        Materialize.updateTextFields();
        
    })(jQuery);
</script>