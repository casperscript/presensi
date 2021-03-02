<?php
use comp\FUNC;
?>
<style>
    .mn-content, body, html {
        font-size: 12px;
    }
    .chip {
        font-size: 10px;
    }
    .black-shadow {text-shadow: 2px 2px #000000;}
    .modal { width: 40%; }
</style>
<div class="row">
    <div class="col s12">
        HALAMAN PEMBERIAN CATATAN DAN <?= $flag === "1" ? "PENERIMAAN" : "PENOLAKAN" ?> MODERASI SECARA MASAL
    </div>
</div>
<div class="row">
    <div class="col s12">
        <textarea id="txtCatatanMassVerif" class="materialize-textarea" placeholder="Berikanlah catatan Anda kepada pemohon moderasi bila diperlukan..."></textarea>
        <label for="txtCatatanMassVerif" class="active">Catatan Admin OPD</label>
    </div>
</div>
<input type="hidden" id="hidCheckedMods" value="<?= $checkedMods ?>" />
<input type="hidden" id="hidFlag" value="<?= $flag ?>" />