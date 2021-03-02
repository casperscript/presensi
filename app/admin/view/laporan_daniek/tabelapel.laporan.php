<?php
ob_start();
use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$path_stempel = $this->new_simpeg_url."/simpeg/upload/stempel/";
$path_ttd = $this->new_simpeg_url."/simpeg/upload/ttd/";
/*
$path_stempel = $this->link()."upload/stempel/";
$path_ttd = $this->link()."upload/ttd/";
*/
?>
<br>
<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>2 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
        <span class="ket-small">
            <?php                
            if ($tingkat == 4)
                echo '<br>Telah Diverifikasi / Disahkan Admin OPD, Kepala OPD dan Admin Kota. Belum Disahkan Kepala BKPPD.';
            ?>
        </span>
    </div>
</div>
<h5 class="center-align"><b>
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
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        $apel = $rekap[$pin][$i]['ap'];
                        echo '<td class="center-align '.$apel['color'].'">'.$apel['kode'].'</td>';
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
<br>
<input type="hidden" value="<?= $moderasi['unverified'] ?>" id="unverified">
<?php if ($download == 0) { ?>
<div class="ttd-laporan">
    <table class="ttd-tabel">
         <tr>
            <td width="50%">
                <?php 
                if ($tingkat == 3)
                    if (isset($laporan['kepala_opd'])) {
                        echo '<b>Mengesahkan '.$laporan['kepala_opd']['jabatan_pengguna'].' Kepala OPD</b><br>'.
                        $laporan['kepala_opd']['nama_personil'].'<br>
                        NIP '.$laporan['kepala_opd']['nipbaru'].'<br>
                        ('.FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date').')';
                    } else
                        echo '<b>[Belum disahkan Kepala OPD]';

                else if ($tingkat > 3)
                    if (isset($laporan['admin_kota'])) {
                        echo '<b>Telah diverifikasi '.$laporan['admin_kota']['jabatan_pengguna'].' Admin Kota</b><br>'.
                        $laporan['admin_kota']['nama_personil'].'<br>
                        NIP '.$laporan['admin_kota']['nipbaru'].'<br>
                        ('.FUNC::tanggal($laporan['dt_ver_admin_kota'], 'short_date').')';
                    } else
                        echo '<b>[Belum diverifikasi Admin Kota]';
                ?>                
            </td>
            <td width="50%">
                <?php if ($tingkat > 1)
                if (isset($laporan['admin_opd'])) {
                    echo '<b>Telah diverifikasi '.$laporan['admin_opd']['jabatan_pengguna'].' Admin OPD</b><br>'.
                    $laporan['admin_opd']['nama_personil'].'<br>
                    NIP '.$laporan['admin_opd']['nipbaru'].'<br>
                    ('.FUNC::tanggal($laporan['dt_ver_admin_opd'], 'short_date').')';
                } else
                    echo '<b>[Belum diverifikasi Admin OPD]';
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php if ($tingkat > 4)
                    if (isset($laporan['kepala_bkppd'])) {
                        echo '<b>Mengesahkan '.$laporan['kepala_bkppd']['jabatan_pengguna'].' Kepala BKPPD</b><br>'.
                        $laporan['kepala_bkppd']['nama_personil'].'<br>
                        NIP '.$laporan['kepala_bkppd']['nipbaru'].'<br>
                        ('.FUNC::tanggal($laporan['dt_sah_kepala_bkppd'], 'short_date').')';
                    } else
                        echo '<b>[Belum disahkan Kepala BKPPD]';
                ?>
            </td>
            <td>
                <?php if ($tingkat > 3 && $tingkat < 6)
                    if (isset($laporan['kepala_opd'])) {
                        echo '<b>Mengesahkan '.$laporan['kepala_opd']['jabatan_pengguna'].' Kepala OPD</b><br>'.
                        $laporan['kepala_opd']['nama_personil'].'<br>
                        NIP '.$laporan['kepala_opd']['nipbaru'].'<br>
                        ('.FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date').')';
                    } else
                        echo '<b>[Belum disahkan Kepala OPD]';

                    if ($tingkat == 6)
                        if (isset($laporan['final'])) {
                            echo '<b>Mengesahkan '.$laporan['final']['jabatan_pengguna'].' Kepala OPD</b><br>'.
                            $laporan['kepala_opd']['nama_personil'].'<br>
                            NIP '.$laporan['kepala_opd']['nipbaru'].'<br>
                            ('.FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date').')';
                        } else
                            echo '<b>[Belum disahkan Kepala OPD]';
                ?>
            </td>
        </tr>
    </table>
</div>  
<?php
exit;
}

$all = [2 => 'admin_opd', 3 => 'kepala_opd', 4 => 'admin_kota', 5 => 'kepala_bkppd'];

foreach ($all as $i => $level) {
    $$level = "";
    $tipe = ($i == 2 ||$i == 4) ? 'ver' : 'sah';

    if ($i == 2)
        $ket = 'Telah diverifikasi '.(isset($laporan[$level]) ? $laporan[$level]['jabatan_pengguna'] : '').' Admin OPD';
    elseif ($i == 3)
        $ket = 'Mengesahkan '.(isset($laporan[$level]) ? $laporan[$level]['jabatan_pengguna'] : '').' Kepala OPD';
    elseif ($i == 4)
        $ket = 'Telah diverifikasi '.(isset($laporan[$level]) ? $laporan[$level]['jabatan_pengguna'] : '').' Admin Kota';
    elseif ($i == 5)
        $ket = 'Mengesahkan '.(isset($laporan[$level]) ? $laporan[$level]['jabatan_pengguna'] : '').' Kepala BKPPD';

    if ($tingkat >= $i && isset($laporan[$level])) {
        $ttd = $path_ttd.$laporan[$level]['ttd'];
        $ttd_headers = @get_headers($ttd);

        $stempel = $path_stempel.$laporan[$level]['stempel'];
        $stempel_headers = @get_headers($stempel);

        $$level = '<div class="teks-atas"><b>'.$ket.'</b></div>
            <div class="ttd-area">';

        if ($ttd_headers[0] == 'HTTP/1.1 200 OK') { 
            
            $$level .= '<div class="ini-ttd">
                <img class="ttd" src="'.$path_ttd.$laporan[$level]['ttd'] .'">
            </div>';

            if (($level == 'kepala_opd' || $level == 'kepala_bkppd') && $stempel_headers[0] == 'HTTP/1.1 200 OK')
                $$level .= '<div class="ini-stempel">
                    <img class="stempel" src="'.$path_stempel.$laporan[$level]['stempel'].'">
                </div>';
            else
                $$level .= '<br><br>';
        } else {
            $$level .= '<br><br><br><br><br><br><br><br><br>';
        }
        $$level .= '</div>';
        $$level .= '<p class="teks-bawah">'
            .$laporan[$level]['nama_personil'].'<br>
            NIP '.$laporan[$level]['nipbaru'].'<br>
            ('.FUNC::tanggal($laporan['dt_'.$tipe.'_'.$level], 'short_date').')</p>';

    }
}
?>
<!--pagebreak-->
<div class="kiri-atas"><?= $tingkat == 3 ? $kepala_opd : $admin_kota ?></div>
<div class="kanan-atas"><?= $admin_opd ?></div>
<div style="clear: both"></div>
<div class="kiri-bawah"><?= $kepala_bkppd ?></div>
<div class="kanan-bawah"><?= $tingkat > 3 ? $kepala_opd : '' ?></div>


<?php
require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$bagi = round((count($kode)-1) / 2);
$tambahan = '<div style="width: 48%; float:left">
<table class="bordered custom-border">
    <thead>
        <tr>
            <th width="20%">Kode Presensi</th>
            <th>Keterangan</th>
            <th width="20%">Potongan (%)</th>
        </tr>
    </thead>
';
for ($i = 0; $i <= $bagi; $i++) {
    $tambahan .= '<tr>
        <td align="center">'.$kode[$i]['kode_presensi'].'</td>
        <td>'.$kode[$i]['ket_kode_presensi'].'</td>
        <td align="center">'.($kode[$i]['pot_kode_presensi']*100).'</td>
    </tr>';
}
$tambahan .= '</table></div>';

$tambahan .= '<div style="width: 48%; float:right">
<table class="bordered custom-border">
    <thead>
        <tr>
            <th width="20%">Kode Presensi</th>
            <th>Keterangan</th>
            <th width="20%">Potongan (%)</th>
        </tr>
    </thead>
';
for ($i; $i < count($kode); $i++) {
    $tambahan .= '<tr>
        <td align="center">'.$kode[$i]['kode_presensi'].'</td>
        <td>'.$kode[$i]['ket_kode_presensi'].'</td>
        <td align="center">'.($kode[$i]['pot_kode_presensi']*100).'</td>
    </tr>';
}
$tambahan .= '</table></div>';
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$pdf = new mPDF('UTF8', 'F4-L', 10);
$pdf->SetDisplayMode('fullpage');
//$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$stylesheet = file_get_contents('http://192.168.254.62/template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$pdf->AddPage();
$pdf->WriteHTML(utf8_encode($tambahan));
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$filename = 'Laporan'.$format.$jenis.'-'.$satker.'-'.$namabulan[$bulan - 1].$tahun.'-tingkat'.$tingkat.'.pdf';

$pdf->Output($filename, 'D');
?>