
<form method="post" action="">
    NIP:<?= comp\BOOTSTRAP::inputText('nip', 'text', $nip, '') ?>
    Bulan:<?= comp\BOOTSTRAP::inputSelect('bulan', comp\FUNC::$namabulan1, $bulan, '') ?>
    Tahun:<?= comp\BOOTSTRAP::inputText('tahun', 'text', $tahun, '') ?>
    <input type="submit" value="Kirim">
</form>


<?php
if (isset($output)) {
    comp\FUNC::showPre(json_decode($output));
}