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

$path_stempel = $this->new_simpeg_url."/simpeg/upload/stempel/";
$path_ttd = $this->new_simpeg_url."/simpeg/upload/ttd/";
/*
$path_stempel = $this->link()."upload/stempel/";
$path_ttd = $this->link()."upload/ttd/";
*/

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
        if (isset($rekapbc[$peg['id']])) {
            $find = $rekapbc[$peg['id']];
            for ($i = 1; $i <= $hitungtgl; $i++) {
                if ($find['t'.$i]) {
                    $get = json_decode($find['t'.$i], true);
                    $masuk = $get[$tingkat]['mk'];
                    $apel = $get[$tingkat]['ap'];
                    $pulang = $get[$tingkat]['pk'];
                } else {
                    $masuk = ['waktu' => '', 'kode' => '', 'color' => '', 'pot' => ''];
                    $apel = ['waktu' => '', 'kode' => '', 'color' => '', 'pot' => ''];
                    $pulang = ['waktu' => '', 'kode' => '', 'color' => '', 'pot' => ''];
                }

                echo '<tr>
                <td class="center-align">'.$i.'</td>';
            ?>
                <!---isi--> 
                <td class="center-align <?= $masuk['color'] ?>"><?= $masuk['kode'] == 'M1' ? $masuk['waktu'] : $masuk['kode'] ?></td>
                <td class="center-align <?= $apel['color'] ?>"><?= $apel['kode'] == 'A1' ? $apel['waktu'] : $apel['kode'] ?></td>
                <td class="center-align <?= $pulang['color'] ?>"><?= $pulang['kode'] == 'P1' ? $pulang['waktu'] : $pulang['kode'] ?></td>

            <?php   
                echo '</tr>';
            }
        } elseif (isset($rekap[$key])) {
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
        }
        ?>
	</tbody>
</table>
<br>
<?php if (!isset($download)) { ?>
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
<?php
continue;
}

$all = [2 => 'admin_opd', 3 => 'kepala_opd', 4 => 'admin_kota', 5 => 'kepala_bkppd'];

foreach ($all as $i => $level) {
    if ($i == 2)
        $ket = 'Telah diverifikasi '.$laporan['jabatan_'.$level].' Admin OPD';
    elseif ($i == 3)
        $ket = 'Mengesahkan '.$laporan['jabatan_'.$level].' Kepala OPD';
    elseif ($i == 4)
        $ket = 'Telah diverifikasi '.$laporan['jabatan_'.$level].' Admin Kota';
    elseif ($i == 5)
        $ket = 'Mengesahkan '.$laporan['jabatan_'.$level].' Kepala BKPPD';

    $ttd = $path_ttd.$laporan['nip_'.$level].'.png';
    $ttd_headers = @get_headers($ttd);

    if ($level == 'kepala_opd')
        $stempel = $path_stempel.$laporan['stempel_opd'];
    elseif ($level == 'kepala_bkppd')
        $stempel = $path_stempel.$laporan['stempel_bkppd'];

    $stempel_headers = @get_headers($stempel);

    if ($tingkat == 6 && $level == 'kepala_opd')
        $level = 'final';

    $$level = '<div class="teks-atas"><b>'.$ket.'</b></div>
        <div class="ttd-area ttd-area-portrait">';

    if ($ttd_headers[0] == 'HTTP/1.1 200 OK') { 
        
        $$level .= '<div class="ini-ttd ini-ttd-portrait">
            <img class="ttd" src="'.$ttd .'">
        </div>';

        if (($level == 'kepala_opd' || $level == 'final'  || $level == 'kepala_bkppd') && $stempel_headers[0] == 'HTTP/1.1 200 OK')
            $$level .= '<div class="ini-stempel ini-stempel-portrait">
                <img class="stempel" src="'.$stempel.'">
            </div>';
        else
            $$level .= '<br><br>';
    } else {
        $$level .= '<br><br><br><br><br><br><br><br><br>';
    }
    $$level .= '</div>';
    $$level .= '<p class="teks-bawah">'
        .$laporan['nama_'.$level].'<br>
        NIP '.$laporan['nip_'.$level].'<br>
        ('.FUNC::tanggal($laporan['dt_'.$level], 'short_date').')</p>';
}
?>
<!--pagebreak-->
<div class="kiri-atas kiri-bawah-portrait"><?= $tingkat == 3 ? $kepala_opd : ($tingkat > 3 ? $admin_kota : '') ?></div>
<div class="kanan-atas kanan-atas-portrait"><?= $tingkat > 1 ? $admin_opd : '' ?></div>
<div style="clear: both"></div>
<div class="kiri-bawah kiri-bawah-portrait"><?= $tingkat > 4 ? $kepala_bkppd : '' ?></div>
<div class="kanan-bawah kanan-bawah-portrait"><?= $tingkat == 6 ? $final : ($tingkat > 3 ? $kepala_opd : '') ?></div>
<div style="clear: both"></div>

<?php
} //end of foreach

if (!isset($download)) {
    echo '<br><br>
    <div style="color: #ddd;">source: db_backup-'.(isset($rekapbc) ? 'rekapbc' : 'rekap').'</div>';
    exit;
}

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
$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$pdf->AddPage();
$pdf->WriteHTML(utf8_encode($tambahan));
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

if ($tingkat == 6)
    $tingkat = 'Final';
$filename = 'LaporanC2'.'-'.$satker.'-'.$namabulan[$bulan - 1].$tahun.'-tingkat'.$tingkat.'.pdf';

$pdf->Output($filename, 'D');
?>