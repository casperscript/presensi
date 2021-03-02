<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_batal_apel', $id_batal_apel); ?>
<?= comp\MATERIALIZE::inputKey('kdlokasi', $kdlokasi); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('satker', 'text', $satker, 'disabled style="color: rgba(0,0,0,.7)"'); ?>
        <label class="active">Satuan Kerja</label>
    </div>
    <div class="input-field col s6">
        <span class="ini-label">Nama Pegawai</span>
        <select class="browser-default" tabindex="-1" style="width: 100%" name="pin_absen" id="pin_absen"></select>
    </div>
    <div class="input-field col s6">
        <span class="ini-label">Tanggal Apel</span>
        <input type="text" class="datepicker" placeholder="Pilih tanggal" name="tanggal_apel" id="tanggal_apel">
    </div>
    <div class="col s12" id="data-apel"></div>
    <div class="input-field col s12" id="input-keterangan" style="display: none;">
        <span class="ini-label">Alasan Pembatalan Apel</span>
        <textarea id="keterangan" name="keterangan" class="materialize-textarea" length="120"><?= $keterangan ?></textarea>
        <span class="character-counter" style="float: right; font-size: 12px; height: 1px;"></span>
    </div>
    <div class="input-field col s12">
        <div id="resultForm"></div>
    </div>
</div>