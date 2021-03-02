<?php
extract($dataJadwal);
//comp\FUNC::showPre($data);
?>
<h4>Input Jam Kerja</h4>
<div class="input-field col s12">
    <?= comp\MATERIALIZE::inputKey('id_jadwal', $id_jadwal) ?>
    <?= comp\MATERIALIZE::inputKey('pin_absen', $dataPersonil['pin_absen']) ?>
    <?= comp\MATERIALIZE::inputKey('kdlokasi', $dataPersonil['kdlokasi']) ?>
    <?= comp\MATERIALIZE::inputSelect('id_shift', $pilShift, $id_shift, 'required') ?>
    <label for="id_shift"></label>
</div>
<div class="row">
    <div class="input-field col s12 m6">
        <i class="fa fa-calendar prefix"></i>
        <?= comp\MATERIALIZE::inputText('sdate', 'text', $sdate, 'required') ?>
        <label for="sdate">Awal</label>
    </div>
    <div class="input-field col s12 m6">
        <i class="fa fa-calendar prefix"></i>
        <?= comp\MATERIALIZE::inputText('edate', 'text', $edate, 'required') ?>
        <label for="edate">Akhir</label>
    </div>
</div>

<style>
    /*    .modal {
            overflow: visible;
        }*/
</style>
<script>
    $("#id_shift").material_select();
    Materialize.updateTextFields();
    $('#sdate').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 4, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
    $('#edate').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 4, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
</script>