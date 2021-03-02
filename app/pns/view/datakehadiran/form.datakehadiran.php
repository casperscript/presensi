<?php
//comp\FUNC::showPre($data);
extract($dataForm);
$stsHadir = array('0' => 'Masuk', '1' => 'Pulang', '2' => 'Apel');
echo comp\MATERIALIZE::inputKey('op', 'editStatus');
echo comp\MATERIALIZE::inputKey('tanggal_log_presensi', $tanggal_log_presensi);
echo comp\MATERIALIZE::inputKey('jam_log_presensi', $jam_log_presensi);
echo comp\MATERIALIZE::inputKey('status_log_presensi', $status_log_presensi);
echo comp\MATERIALIZE::inputKey('pin_absen', $pin_absen);
?>
<h4>Edit Status Kehadiran</h4>
<table class="table-bordered table-responsive striped">
    <thead class="grey darken-3 white-text">
        <tr>
            <th class="center">Tanggal</th>
            <th class="center">Jam</th>
            <th class="center">Status pada Mesin</th>
            <th class="center">Koreksi Id Scan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="center"><?= comp\FUNC::tanggal($tanggal_log_presensi, 'long_date') ?></td>
            <td class="center"><?= $jam_log_presensi ?></td>
            <td class="center"><?= $stsHadir[$status_log_verified] ?></td>
            <td class="center">
                <?= comp\MATERIALIZE::inputSelect('status_presensi', $stsHadir, $status_log_presensi) ?>
            </td>
        </tr>
    </tbody>
</table>
<script>
    $("#status_presensi").material_select();
</script>