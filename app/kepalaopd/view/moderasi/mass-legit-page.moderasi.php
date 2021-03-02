<?php
use comp\FUNC;
?>
<style>
    .black-shadow {text-shadow: 2px 2px #000000;}
    .modal { width: 40%; }
</style>
<div class="row">
    <div class="col s12 center">
        HALAMAN PEMBERIAN CATATAN<br>DAN <?= $flag === "2" ? "PENGESAHAN" : "PEMBATALAN" ?> FINAL MODERASI SECARA MASSAL<br><small class="red-text">Hanya berlaku untuk moderasi yang telah disahkan Kepala BKPPD</small>
    </div>
</div>
<div class="row">
    <div class="col s12">
        <textarea id="txtCatatanMassLegit" class="materialize-textarea" placeholder="Berikanlah catatan Anda kepada pemohon moderasi bila diperlukan..."></textarea>
        <label for="txtCatatanMassLegit" class="active">Catatan Kepala OPD</label>
    </div>
</div>
<input type="hidden" id="hidCheckedMods" value="<?= $checkedMods ?>" />
<input type="hidden" id="hidFlag" value="<?= $flag ?>" />