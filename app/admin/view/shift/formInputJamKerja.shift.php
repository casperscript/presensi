<?php
//comp\FUNC::showPre($data);
?>
<h4>Input Jam Kerja</h4>
<div class="input-field col s12">
    <?= comp\MATERIALIZE::inputKey('op', 'addJamKerja') ?>
    <?= comp\MATERIALIZE::inputKey('id_shift_detail', $id_shift_detail) ?>
    <?= comp\MATERIALIZE::inputSelect('id_jam_kerja', $pil_jamKerja, $dataTabel['id_jam_kerja'], 'required') ?>
    <label for="id_jam_kerja"></label>
</div>

<style>
    .modal {
        overflow: visible;
    }
</style>
<script>
    $("#id_jam_kerja").material_select();
    Materialize.updateTextFields();
</script>