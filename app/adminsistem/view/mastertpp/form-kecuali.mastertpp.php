<h4>Tambah Pegawai yang DIkecualikan</h4>
<?= comp\MATERIALIZE::inputKey('kd_tpp', $kd_tpp); ?>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_lokasi, '', ' required'); ?>
        <label for="" class="">OPD</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('nipbaru', $pil_nipbaru, '', ' required'); ?>
        <label for="" class="">Pegawai</label>
        <div class="progress no-s hide" id="progressPersonil">
            <div class="indeterminate"></div>
        </div>
    </div>

    <div class="input-field col s12">
        <span class="ini-label">Alasan Dikecualikan</span>
        <textarea id="alasan" name="alasan" class="materialize-textarea" placeholder="Contoh: Pensiun" length="120" required></textarea>
        <span class="character-counter" style="float: right; font-size: 12px; height: 1px;"></span>
    </div>
    <div class="input-field col s12">
        <div id="resultFormKecuali"></div>
    </div>
</div>