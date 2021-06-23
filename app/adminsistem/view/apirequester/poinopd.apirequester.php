
<form method="post" action="">
    Satuan Kerja:<?= comp\BOOTSTRAP::inputSelect('opd', $listSatker, $opd, '') ?>
    Bulan:<?= comp\BOOTSTRAP::inputSelect('bulan', comp\FUNC::$namabulan1, $bulan, '') ?>
    Tahun:<?= comp\BOOTSTRAP::inputText('tahun', 'text', $tahun, '') ?>
    <input type="submit" value="Kirim">
</form>


<?php
if (isset($output)) {
    $res_decode = json_decode($output, true);
    $data = $res_decode['data'];
    comp\FUNC::showPre($data);
}