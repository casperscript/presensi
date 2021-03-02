<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_shift', $id_shift); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_shift', 'text', $nama_shift, ' required'); ?>
        <label for="" class="">Nama Shift</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_lokasi, $kdlokasi, ' required'); ?>
        <label for="" class="">Lokasi Kerja</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('tanggal_mulai_shift', 'text', $tanggal_mulai_shift, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;y-m-d&apos;" required'); ?>
        <label for="" class="">Tanggal Mulai Shift (tahun-bulan-tanggal)</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('tanggal_akhir_shift', 'text', $tanggal_akhir_shift, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;y-m-d&apos;" required'); ?>
        <label for="" class="">Tanggal Akhir Shift (tahun-bulan-tanggal)</label>
    </div>
    <div class="input-field col s12">
        <p class="range-field"><?= comp\MATERIALIZE::inputText('siklus_shift', 'range', $siklus_shift, ' min="1" max="31" required'); ?></p>
        <label for="" class="">Siklus Shift</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('unit_shift', $pil_unit_shift, $unit_shift, ' required'); ?>
        <label for="" class="">Unit Shift</label>
    </div>
    <div class="input-field col s12">
        <div class="switch m-b-md">
            <label>
                DRAFT
                <input id="status_shift" name="status_shift" type="checkbox" value="<?= $status_shift;?>" />
                <span class="lever"></span>
                PUBLISH
            </label>
        </div>
    </div>
</div>
<script src="assets/plugins/materialize/js/materialize.min.js"></script>
<script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
<script>
    (function ($) {
        "use strict";
        
        $(".masked").inputmask();
        $("select").material_select();
        $(".datepicker").pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            format: 'yyyy-mm-dd',
            formatSubmit: 'yyyy-mm-dd'
        });
                
        <?= ($status_shift=='publish') ? '$("#status_shift").prop("checked", true);' : '$("#status_shift").prop("checked", false);';?>
        
        $("#status_shift").change(function() {
            ($(this).is(":checked")) ? $("#status_shift").prop("checked", true) : $("#status_shift").prop("checked", false);
        });
        
        Materialize.updateTextFields();
        
    })(jQuery);
</script>