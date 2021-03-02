<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_potongan_pajak', $id_potongan_pajak); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('golruang_kepegawaian', $pil_golruang_kepegawaian, $golruang_kepegawaian, ' required'); ?>
        <label for="" class="">Golongan / Ruang</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('potongan_pajak', 'text', $potongan_pajak, ' required'); ?>
        <label for="" class="">Potongan Pajak</label>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        $("select").material_select();
        Materialize.updateTextFields();
        
    })(jQuery);
</script>