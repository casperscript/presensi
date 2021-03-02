<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_jam_apel', $id_jam_apel); ?>
<?= comp\MATERIALIZE::inputKey('author', $author); ?>
<?= comp\MATERIALIZE::inputKey('ip', $ip); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_jam_apel', 'text', $nama_jam_apel, 'required'); ?>
        <label for="" class="">Nama Jam Apel Pagi</label>
        <div id="cekprimary"></div>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('mulai_apel', 'text', $mulai_apel, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s:s&apos;" required'); ?>
        <label for="" class="">Mulai Apel</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('akhir_apel', 'text', $akhir_apel, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s:s&apos;" required'); ?>
        <label for="" class="">Akhir Apel</label>
    </div>
    <div class="input-field col s6">
        <span class="ini-label">Tanggal Mulai</span>
        <input type="text" class="datepicker" placeholder="Pilih tanggal mulai" name="tanggal_mulai" id="tanggal_mulai" value="<?=$tanggal_mulai?>" required>
    </div>
    <div class="input-field col s6">
        <span class="ini-label">Tanggal Akhir</span>
        <input type="text" class="datepicker" placeholder="Pilih tanggal akhir" name="tanggal_akhir" id="tanggal_akhir" value="<?=$tanggal_akhir?>" required>
    </div>
    <div class="input-field col s12">
        <div id="resultForm"></div>
    </div>
</div>
<script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
<script>
    (function ($) {
        "use strict";
        
        $(".masked").inputmask();
        Materialize.updateTextFields();

        $('.datepicker').pickadate({
            selectMonths: true, 
            selectYears: true,
            format: 'yyyy-mm-dd',
            onSet: function(context) {
                if(context.select)
                    this.close();
            }
        });
        
    })(jQuery);
</script>