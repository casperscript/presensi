<?php
extract($moderasi);
echo comp\MATERIALIZE::inputKey('id', $id);
?>

<!-- <div class="card no-s"> -->
<div class="card-content">

    <div class="row no-s">
        <div class="input-field col s6">
            <label for="kd_jenis" class="active">Kategori</label>
            <?= comp\MATERIALIZE::inputSelect('kd_jenis', $jenisMod, $kd_jenis, 'class="kd_jenis" required="true"') ?>
        </div>
        <div class="input-field col s6">
            <label for="kode_presensi" class="active">Jenis Moderasi</label>
            <?= comp\MATERIALIZE::inputSelect('kode_presensi', $kodeMod, $kode_presensi, 'class="browser-default" required="true"') ?>
        </div>
    </div>

    <div class="row no-s">
        <div class="input-field col s12 m6">
            <i class="fa fa-calendar prefix"></i>
            <label for="tanggal_awal" class="active">Tanggal Awal</label>
            <?= comp\MATERIALIZE::inputText('tanggal_awal', 'date', $tanggal_awal, 'required') ?>
        </div>
        <div class="input-field col s12 m6">
            <i class="fa fa-calendar prefix"></i>
            <label for="tanggal_akhir" class="active">Tanggal Akhir</label>
            <?= comp\MATERIALIZE::inputText('tanggal_akhir', 'date', $tanggal_akhir, 'required') ?>
        </div>
    </div>

    <div class="input-field col s12">
        <label for="keterangan">Keterangan pengajuan moderasi</label>
        <?= comp\MATERIALIZE::inputTextArea('keterangan', $keterangan, 'class="materialize-textarea validate" required') ?>
    </div>

</div>
<!-- </div> -->

<script>
    $(".kd_jenis").not(".disabled").material_select();
    $("#kode_presensi").not(".disabled").material_select();

    $("#tanggal_awal").pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 4, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
    $("#tanggal_akhir").pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 4, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
</script>