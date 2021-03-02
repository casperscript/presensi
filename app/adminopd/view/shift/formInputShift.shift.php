<?php
//comp\FUNC::showPre($data);
extract($dataTabel);
$unit_shift = ($unit_shift > 0) ? $unit_shift : 1;
$val_status_shift = ($status_shift == 'publish') ? 'checked' : '';
$sdate = (!empty($tanggal_mulai_shift)) ? $tanggal_mulai_shift : date('Y-m-01');
$edate = (!empty($tanggal_akhir_shift)) ? $tanggal_akhir_shift : date('Y-m-t');
$siklus_shift = (!empty($siklus_shift)) ? $siklus_shift : 1;
?>
<h4>Input Shift</h4>

<div class="input-field col s12">
    <?= comp\MATERIALIZE::inputKey('id_shift', $id_shift) ?>
    <?= comp\MATERIALIZE::inputKey('kdlokasi', $id_satker) ?>
    <?= comp\MATERIALIZE::inputKey('op', 'addShift') ?>
    <?= comp\MATERIALIZE::inputText('nama_shift', 'text', $nama_shift, ' required'); ?>
    <label for="nama_shift" class="">Nama Shift</label>
    <div id="cekprimary"></div>
</div>
<div class="row" style="margin-bottom: 0px">
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('tanggal_mulai_shift', 'text', $sdate, ' required'); ?>
        <label for="tanggal_mulai_shift" class="active">Tanggal Mulai Shift</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('tanggal_akhir_shift', 'text', $edate, ' required'); ?>
        <label for="tanggal_akhir_shift" class="active">Tanggal Akhir Shift</label>
    </div>
</div>
<div class="row" style="margin-bottom: 0px">
    <div class="input-field col s5">
        <?= comp\MATERIALIZE::inputSelect('unit_shift', $pil_unitShift, $unit_shift, ' required'); ?>
        <label for="unit_shift" class="">Unit Shift</label>
    </div>    
    <div class="input-field col s7">
        <p class="range-field" style="margin-bottom: 0px"><?= comp\MATERIALIZE::inputText('siklus_shift', 'range', $siklus_shift, ' min="1" max="31" required'); ?></p>
        <label for="siklus_shift" class="">Siklus Shift</label>
    </div>
</div>
<div class="row" style="margin-bottom: 0px">
    <div class="col switch m-b-md">
        <label>
            Draft
            <input id="status_shift" name="status_shift" type="checkbox" value="<?= $status_shift; ?>" <?= $val_status_shift ?>>
            <span class="lever"></span>
            Publish
        </label>
    </div>
</div>

<script>
    $('#tanggal_mulai_shift').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
    $('#tanggal_akhir_shift').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
    $("#unit_shift").material_select();

    $("#status_shift").change(function () {
        ($(this).is(":checked")) ? $("#status_shift").prop("checked", true) : $("#status_shift").prop("checked", false);
    });

    Materialize.updateTextFields();
</script>