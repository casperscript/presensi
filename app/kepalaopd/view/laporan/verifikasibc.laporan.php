<?php
ob_start();
use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$format = 'A';
?>
<div class="alert-verifikasi alert-verifikasi-ok <?= $tingkat == 2 && isset($laporan['admin_opd']) ? '' : 'hide' ?>">
    <span class="blink">
        <i class="fa fa-check-circle"></i>
        Laporan Telah Diverifikasi Admin OPD
    </span>
</div>
<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>1 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
    </div>
</div>
<h5 class="center-align" style="color: #e65100"><b>
Laporan Rekap Kehadiran/Ketidakhadiran Masuk Kerja Karyawan <br>
OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun?>
</b></h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="orange lighten-4 center-align" rowspan="2">No</th>
            <th class="orange lighten-4 center-align" rowspan="2">Nama</th>
            <th class="orange lighten-4 center-align" colspan="<?= $hitungtgl ?>">Tanggal</th>
        </tr>
        <tr>
            <?php
                for ($i = 1; $i <= $hitungtgl; $i++)
                    echo "<th class='orange lighten-4 center-align' width='25'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; $pin_absen = ''; $allverified = true;
        foreach ($pegawai['value'] as $peg) { ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 180px"><?= $peg['nama_personil'] ?></td>
                <?php
                $pin = $peg['pin_absen'];

                if (isset($rekapbc[$peg['id']])) {
                    $find = $rekapbc[$peg['id']];
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        if ($find['t'.$i]) {
                            $get = json_decode($find['t'.$i], true);
                            $masuk = $get[$tingkat]['mk'];
                        } else 
                            $masuk = ['waktu' => '', 'kode' => '', 'color' => ''];

                        $show = $masuk['kode'];
                        echo '<td class="center-align '.$masuk['color'].'">'.$show.'</td>';
                    }
                } elseif (isset($rekap[$pin])) {
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        $masuk = $rekap[$pin][$i]['mk'];
                        echo '<td class="center-align '.$masuk['color'].'">'.$masuk['kode'].'</td>';
                    }
                } else {
                    echo '<td class="center-align" colspan="'.$hitungtgl.'"><i>data backup presensi tidak ditemukan</i></td>';
                }
                ?>
            </tr>
        <?php 
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
            $no++;
        } 
        ?>
    </tbody>
</table>
<br><br>

<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>2 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
    </div>
</div>
<h5 class="center-align" style="color: #01579b"><b>
Laporan Rekap Kehadiran/Ketidakhadiran Apel Pagi Karyawan <br>
OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun?>
</b></h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="light-blue lighten-4 center-align" rowspan="2">No</th>
            <th class="light-blue lighten-4 center-align" rowspan="2">Nama</th>
            <th class="light-blue lighten-4 center-align" colspan="<?= $hitungtgl ?>">Tanggal</th>
        </tr>
        <tr>
            <?php
                for ($i = 1; $i <= $hitungtgl; $i++)
                    echo "<th class='light-blue lighten-4 center-align' width='25'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; $pin_absen = ''; $allverified = true;
        foreach ($pegawai['value'] as $peg) { ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 180px"><?= $peg['nama_personil'] ?></td>
                <?php
                $pin = $peg['pin_absen'];
                
                if (isset($rekapbc[$peg['id']])) {
                    $find = $rekapbc[$peg['id']];
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        if ($find['t'.$i]) {
                            $get = json_decode($find['t'.$i], true);
                            $apel = $get[$tingkat]['ap'];
                        } else 
                            $apel = ['waktu' => '', 'kode' => '', 'color' => ''];

                        $show = $apel['kode'];
                        echo '<td class="center-align '.$apel['color'].'">'.$show.'</td>';
                    }
                } elseif (isset($rekap[$pin])) {
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        $apel = $rekap[$pin][$i]['ap'];
                        echo '<td class="center-align '.$apel['color'].'">'.$apel['kode'].'</td>';
                    }
                } else {
                    echo '<td class="center-align" colspan="'.$hitungtgl.'"><i>data backup presensi tidak ditemukan</i></td>';
                }
                ?>
            </tr>
        <?php 
            $no++;
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
        } 
        ?>
    </tbody>
</table>
<br><br>

<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>3 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
    </div>
</div>
<h5 class="center-align" style="color: #33691e"><b>
Laporan Rekap Finger Print Pulang Kerja Karyawan <br>
OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun?>
</b></h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="light-green lighten-4 center-align" rowspan="2">No</th>
            <th class="light-green lighten-4 center-align" rowspan="2">Nama</th>
            <th class="light-green lighten-4 center-align" colspan="<?= $hitungtgl ?>">Tanggal</th>
        </tr>
        <tr>
            <?php
                for ($i = 1; $i <= $hitungtgl; $i++)
                    echo "<th class='light-green lighten-4 center-align' width='25'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; $pin_absen = ''; $allverified = true;
        foreach ($pegawai['value'] as $peg) { ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 180px"><?= $peg['nama_personil'] ?></td>
                <?php
                $pin = $peg['pin_absen'];

                if (isset($rekapbc[$peg['id']])) {
                    $find = $rekapbc[$peg['id']];
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        if ($find['t'.$i]) {
                            $get = json_decode($find['t'.$i], true);
                            $pulang = $get[$tingkat]['pk'];
                        } else 
                            $pulang = ['waktu' => '', 'kode' => '', 'color' => ''];

                        $show = $pulang['kode'];
                        echo '<td class="center-align '.$pulang['color'].'">'.$show.'</td>';
                    }
                } elseif (isset($rekap[$pin])) {
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        $pulang = $rekap[$pin][$i]['pk'];
                        echo '<td class="center-align '.$pulang['color'].'">'.$pulang['kode'].'</td>';
                    }
                } else {
                    echo '<td class="center-align" colspan="'.$hitungtgl.'"><i>data backup presensi tidak ditemukan</i></td>';
                }

                ?>
            </tr>
        <?php 
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
            $no++;
        } 
        ?>
    </tbody>
</table>
<br>

<div class="ttd-laporan">
    <table class="ttd-tabel">
         <tr>
            <td width="50%">
                <?php 
                if ($tingkat == 3)
                    echo '<b>Mengesahkan '.$laporan['jabatan_kepala_opd'].' Kepala OPD</b><br>'.
                    $laporan['nama_kepala_opd'].'<br>
                    NIP '.$laporan['nip_kepala_opd'].'<br>
                    ('.FUNC::tanggal($laporan['dt_kepala_opd'], 'short_date').')';

                else if ($tingkat > 3)
                    echo '<b>Telah diverifikasi '.$laporan['jabatan_admin_kota'].' Admin Kota</b><br>'.
                    $laporan['nama_admin_kota'].'<br>
                    NIP '.$laporan['nip_admin_kota'].'<br>
                    ('.FUNC::tanggal($laporan['dt_admin_kota'], 'short_date').')';
                ?>                
            </td>
            <td width="50%">
                <?php if ($tingkat > 1)
                    echo '<b>Telah diverifikasi '.$laporan['jabatan_admin_opd'].' Admin OPD</b><br>'.
                    $laporan['nama_admin_opd'].'<br>
                    NIP '.$laporan['nip_admin_opd'].'<br>
                    ('.FUNC::tanggal($laporan['dt_admin_opd'], 'short_date').')';
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php if ($tingkat > 4)
                    echo '<b>Mengesahkan '.$laporan['jabatan_kepala_bkppd'].' Kepala BKPPD</b><br>'.
                    $laporan['nama_kepala_bkppd'].'<br>
                    NIP '.$laporan['nip_kepala_bkppd'].'<br>
                    ('.FUNC::tanggal($laporan['dt_kepala_bkppd'], 'short_date').')';
                ?>
            </td>
            <td>
                <?php if ($tingkat > 3 && $tingkat < 6)
                    echo '<b>Mengesahkan '.$laporan['jabatan_kepala_opd'].' Kepala OPD</b><br>'.
                    $laporan['nama_kepala_opd'].'<br>
                    NIP '.$laporan['nip_kepala_opd'].'<br>
                    ('.FUNC::tanggal($laporan['dt_kepala_opd'], 'short_date').')';

                    if ($tingkat == 6)
                        echo '<b>Mengesahkan '.$laporan['jabatan_final'].' Kepala OPD</b><br>'.
                        $laporan['nama_final'].'<br>
                        NIP '.$laporan['nip_final'].'<br>
                        ('.FUNC::tanggal($laporan['dt_final'], 'short_date').')';
                ?>
            </td>
        </tr>
    </table>
</div>  

<br><br>
<div style="color: #ddd;">source: db_backup-<?= count($rekapbc) > 0 ? 'rekapbc' : 'rekap    ' ?></div>