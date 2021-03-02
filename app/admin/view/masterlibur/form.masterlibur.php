<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_libur', $id_libur); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('tgl_libur', 'text', $tgl_libur, ' class="datepicker" required'); ?>
        <label for="" class="">Tanggal Libur</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('keterangan', 'text', $keterangan, ' required'); ?>
        <label for="" class="">Keterangan</label>
    </div>
</div>
<script src="assets/plugins/materialize/js/materialize.min.js"></script>
<script>
    (function ($) {
        "use strict";
        
        $(".datepicker").pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            format: 'yyyy-mm-dd',
            formatSubmit: 'yyyy-mm-dd'
        });
        
        Materialize.updateTextFields();
        
    })(jQuery);
</script>