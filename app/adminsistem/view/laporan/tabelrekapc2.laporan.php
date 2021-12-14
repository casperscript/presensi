<?php
ob_start();
use comp\FUNC;
?>
<style>
@page {
    margin: 20mm 10mm;
}
</style>
<?php
$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$path_stempel = $this->link() . "upload/stempel/";
$path_ttd = $this->link() . "upload/ttd/";

foreach ($pegawai['value'] as $peg) {
    $key = $peg['pin_absen'];
?>
<div class="row lap">
    <div class="format-lap">
        Format C2 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
        <span class="ket-small">
            <?php                
            switch ($tingkat) {
                case '1':
                    echo '<br>Belum Diverifikasi Admin OPD.';
                    break;
                case '2':
                    echo '<br>Telah Diverifikasi Admin OPD. Belum Disahkan Kepala OPD.';
                    break;
                case '3':
                    echo '<br>Telah Diverifikasi / Disahkan Admin OPD dan Kepala OPD. Belum Disahkan Admin Kota.';
                    break;
                case '4':
                    echo '<br>Telah Diverifikasi / Disahkan Admin OPD, Kepala OPD dan Admin Kota. Belum Disahkan Kepala BKPPD.';
                    break;
                case '6':
                    echo '<br>Telah Diverifikasi / Disahkan Admin OPD, Kepala OPD, Admin Kota dan Kepala BKPPD.';
                    break;
                default:
                    # code...
                    break;
            }
            ?>
        </span>
    </div>
</div>
<h5 class="center-align"><b>
Laporan Rekap Bulanan Daftar Kehadiran Individu/Pegawai
</b></h5>
<table class="small-padding">
	<tr>
		<td width="125">Bulan / Tahun</td>
		<td width="30">:</td>
		<td><?= strtoupper($namabulan[$bulan - 1]) ?> / <?= $tahun ?></td>
	</tr>
	<tr>
		<td>Nama</td>
		<td>:</td>
		<td><?= $peg['nama_personil'] ?></td>
	</tr>
	<tr>
		<td>NIP</td>
		<td>:</td>
		<td><?= $peg['nipbaru'] ?></td>
	</tr>
	<tr>
		<td>OPD / Unit Kerja</td>
		<td>:</td>
		<td><?= $satker ?></td>
	</tr>
</table>
<br>
<table class="bordered hoverable custom-border custom-portrait">
	<thead>
		<tr>
			<th class="grey lighten-2 center-align">Tanggal</th>
			<th class="grey lighten-2 center-align">Masuk Kerja</th>
			<th class="grey lighten-2 center-align">Apel Pagi</th>
			<th class="grey lighten-2 center-align">Pulang Kerja</th>
		</tr>
	</thead>
	<tbody>
		<?php
        for ($i = 1; $i <= $hitungtgl; $i++) {
            $masuk = $rekap[$key][$i]['mk'];
            $apel = $rekap[$key][$i]['ap'];
            $pulang = $rekap[$key][$i]['pk'];

            echo '<tr>
            <td class="center-align">'.$i.'</td>';
        ?>
            <!---isi--> 
            <td class="center-align <?= $masuk['color'] ?>"><?= $masuk['kode'] ?></td>
            <td class="center-align <?= $apel['color'] ?>"><?= $apel['kode'] ?></td>
            <td class="center-align <?= $pulang['color'] ?>"><?= $pulang['kode'] ?></td>

        <?php   
            echo '</tr>';
        } 
        ?>
	</tbody>
</table>
<br>
<input type="hidden" value="<?= $rekap['allverified'] ? 0 : 1 ?>" id="unverified">
<?php if (!isset($download)) { ?>
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
}
/*
if (!isset($download) && $tingkat == 4 && !isset($laporan['admin_kota']) 
    && isset($laporan['kepala_opd']) &&
    date('Y') >= $tahun && date('m') > $bulan
) {
?>
<div class="center-align">
    <form id="frmVer">
        <?= comp\MATERIALIZE::inputKey('pin_absen', $key); ?>
        <?= comp\MATERIALIZE::inputKey('bulan', $bulan); ?>
        <?= comp\MATERIALIZE::inputKey('tahun', $tahun); ?>
        <?= comp\MATERIALIZE::inputKey('jenis', 2); ?>
        <?= comp\MATERIALIZE::inputKey('tingkat', $tingkat); ?>
        <?= comp\MATERIALIZE::inputKey('kdlokasi', $kdlokasi); ?>
        <?= comp\MATERIALIZE::inputKey('format', 'C'); ?>
        <button class="btn waves-effect waves-light orange <?= $rekap['allverified'] ? 'btnVer' : 'btnMod' ?>" type="button">
            <i class="material-icons left">verified_user</i>
            Verifikasi
        </button>
    </form>
</div>
<?php
} //end if
*/
if (!isset($download))
    continue;

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
            <div class="ttd-area ttd-area-portrait">';

        if ($ttd_headers[0] == 'HTTP/1.1 200 OK') { 
            
            $$level .= '<div class="ini-ttd ini-ttd-portrait">
                <img class="ttd" src="'.$path_ttd.$laporan[$level]['ttd'] .'">
            </div>';

            if (($level == 'kepala_opd' || $level == 'kepala_bkppd') && $stempel_headers[0] == 'HTTP/1.1 200 OK')
                $$level .= '<div class="ini-stempel ini-stempel-portrait">
                    <img class="stempel stempel-portrait" src="'.$path_stempel.$laporan[$level]['stempel'].'">
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
<div class="kiri-atas kiri-bawah-portrait"><?= $tingkat == 3 ? $kepala_opd : $admin_kota ?></div>
<div class="kanan-atas kanan-atas-portrait"><?= $admin_opd ?></div>
<div style="clear: both"></div>
<div class="kiri-bawah kiri-bawah-portrait"><?= $kepala_bkppd ?></div>
<div class="kanan-bawah kanan-bawah-portrait"><?= $tingkat > 3 ? $kepala_opd : '' ?></div>

<?php
} //end of foreach
if (!isset($download))
    exit;

require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$tambahan = '<table class="bordered custom-border">
    <thead>
        <tr>
            <th width="20%">Kode Presensi</th>
            <th>Keterangan</th>
            <th width="20%">Potongan (%)</th>
        </tr>
    </thead>
';
foreach ($kode as $i) {
    $tambahan .= '<tr>
        <td align="center">'.$i['kode_presensi'].'</td>
        <td>'.$i['ket_kode_presensi'].'</td>
        <td align="center">'.($i['pot_kode_presensi']*100).'</td>
    </tr>';
}
$tambahan .= '</table>';
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$pdf = new mPDF('UTF8', 'F4');
$pdf->SetDisplayMode('fullpage');
//$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$stylesheet = file_get_contents('http://192.168.254.62/template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$pdf->AddPage();
$pdf->WriteHTML(utf8_encode($tambahan));
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$filename = 'LaporanC2'.'-'.$satker.'-'.$namabulan[$bulan - 1].$tahun.'-tingkat'.$tingkat.'.pdf';

$pdf->Output($filename, 'D');
?>