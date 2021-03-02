<?php
extract($dataTabel);
$val_status_shift = ($status_shift == 'publish') ? 'checked' : '';
//comp\FUNC::showPre($data);
?>
<h4>Edit Shift</h4>

<div class="input-field col s12">
    <?= comp\MATERIALIZE::inputKey('id_shift', $id_shift) ?>
    <?= comp\MATERIALIZE::inputKey('op', 'editShift') ?>
    <?= comp\MATERIALIZE::inputText('nama_shift', 'text', $nama_shift, ' required'); ?>
    <label for="nama_shift" class="">Nama Shift</label>
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
    $("#status_shift").change(function () {
        ($(this).is(":checked")) ? $("#status_shift").prop("checked", true) : $("#status_shift").prop("checked", false);
    });

    Materialize.updateTextFields();
</script>