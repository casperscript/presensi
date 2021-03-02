<div class="row">
    <div class="col s12">
        <h4>Moderasi Massal</h4>
        <h3 class="center-align"><?= $flag === '1' ? 'Diterima' : 'Ditolak' ?></h3>
    </div>
</div>
<div class="row">
    <div class="col s12">
        <label for="txtCatatanMassVerif" class="active">Catatan Admin OPD</label>
        <textarea id="txtCatatanMassVerif" class="materialize-textarea" name="catatan" placeholder="Berikanlah catatan Anda kepada pemohon moderasi bila diperlukan..."></textarea>
    </div>
</div>
<input type="hidden" id="hidCheckedMods" name="mods" value="<?= $checkedMods ?>" />
<input type="hidden" id="hidFlag" name="flag" value="<?= $flag ?>" />