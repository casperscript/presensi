<?php
use comp\FUNC;
?>
<style>
    .black-shadow {text-shadow: 2px 2px #000000;}
</style>
<div class="row">
    <div class="col s7 offset-s3">
        <p><small>Fasilitas ini digunakan untuk memberi pengesahan sekaligus catatan (opsional) kepada daftar moderasi yang telah Anda centang.</small></p>
        <textarea id="txtCatatanMassLegit" class="materialize-textarea" placeholder="Berikanlah catatan Anda kepada pemohon moderasi bila diperlukan..."></textarea>
        <label for="txtCatatanMassLegit" class="active">Catatan Admin Kota</label>
    </div>
</div>
<input type="hidden" id="hidCheckedMods" value="<?= $checkedMods ?>" />