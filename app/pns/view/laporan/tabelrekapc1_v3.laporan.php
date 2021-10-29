<?php
ob_start();
//comp\FUNC::showPre($data);
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

$path_stempel = $this->new_simpeg_url . "/simpeg/upload/stempel/";
$path_ttd = $this->new_simpeg_url . "/simpeg/upload/ttd/";

if ($tingkat == 3 && !isset($laporan['kepala_opd'])) {
    echo '<div class="alert-verifikasi">
        <i class="fa fa-info-circle"></i>
        Laporan Tingkat 3 Bulan ' . $namabulan[$bulan - 1] . ' belum diverifikasi dan disahkan oleh Kepala OPD
    </div>';
} elseif ($tingkat == 6 && !isset($laporan['final'])) {
    echo '<div class="alert-verifikasi">
        <i class="fa fa-info-circle"></i>
        Laporan Final Bulan ' . $namabulan[$bulan - 1] . ' belum diverifikasi dan disahkan oleh Kepala OPD
    </div>';
    exit;
}

$key = $data['personil'];
?>
<div class="row lap">
    <div class="format-lap">
        Format C1 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
        <span class="ket-small">
            <?php
            switch ($tingkat) {
                case '1':
                    echo '<br>Belum Diverifikasi Admin OPD.';
                    break;
                case '3':
                    echo '<br>Telah Diverifikasi / Disahkan Admin OPD dan Kepala OPD. Belum Disahkan Admin Kota.';
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
        Laporan Rekap Kehadiran/Ketidakhadiran Masuk Kerja, Apel Pagi dan Pulang Kerja
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
        <td><?= $pegawai['nama_personil'] ?></td>
    </tr>
    <tr>
        <td>NIP</td>
        <td>:</td>
        <td><?= $pegawai['nipbaru'] ?></td>
    </tr>
    <tr>
        <td>OPD / Unit Kerja</td>
        <td>:</td>
        <td><?= $satker['singkatan_lokasi'] ?></td>
    </tr>
</table>
<br>
<table class="bordered hoverable custom-border custom-portrait">
    <thead>
        <tr>
            <th class="grey lighten-2 center-align" rowspan="2">Tanggal</th>
            <th class="grey lighten-2 center-align" colspan="2">Masuk Kerja</th>
            <th class="grey lighten-2 center-align" colspan="2">Apel Pagi</th>
            <th class="grey lighten-2 center-align" colspan="2">Pulang Kerja</th>
            <th class="grey lighten-2 center-align" rowspan="2">Total Potongan Harian (potongan MK, AP, PK)</th>
        </tr>
        <tr>
            <th class="grey lighten-2 center-align">Kode</th>
            <th class="grey lighten-2 center-align">Potongan</th>
            <th class="grey lighten-2 center-align">Kode</th>
            <th class="grey lighten-2 center-align">Potongan</th>
            <th class="grey lighten-2 center-align">Kode</th>
            <th class="grey lighten-2 center-align">Potongan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $tot = 0;
        $subtot = 0;
        $pot_penuh = $rekap[$key]['pot_penuh'];
        for ($i = 1; $i <= $hitungtgl; $i++) {
            $masuk = $rekap[$key][$i]['mk'];
            $apel = $rekap[$key][$i]['ap'];
            $pulang = $rekap[$key][$i]['pk'];
            ?>

            <tr>
                <td class="center-align"><?= $i ?></td>
                <!---isi-->	
                <td class="center-align <?= $masuk['color'] ?>"><?= $masuk['kode'] ?></td>
                <td class="center-align"><?= count($pot_penuh) == 0 ? $masuk['pot'] : '' ?></td>
                <td class="center-align <?= $apel['color'] ?>"><?= $apel['kode'] ?></td>
                <td class="center-align"><?= count($pot_penuh) == 0 ? $apel['pot'] : '' ?></td>
                <td class="center-align <?= $pulang['color'] ?>"><?= $pulang['kode'] ?></td>
                <td class="center-align"><?= count($pot_penuh) == 0 ? $pulang['pot'] : '' ?></td>

                <td class="center-align"><?= count($pot_penuh) == 0 ? $rekap[$key][$i]['all'] : '' ?></td>

            </tr>
            <?php
        }

        $rupiah_pot = 0;
        if (isset($tpp_pegawai['nominal_tp'])) {
            $tpp36 = $tpp_pegawai['nominal_tp'] * 36 / 100;
            $rupiah_pot = round($tpp36 * $rekap[$key]['sum_pot']['all'] / 100, 0);
        }
        $pot_kinerja = 'NAN';
        $rupiah_pot_kinerja = 0;
        if (isset($kinerja[$pegawai['nipbaru']])) {
            $tpp24 = $tpp_pegawai['nominal_tp'] * 24 / 100;
            $pot_kinerja = 100 - $kinerja[$pegawai['nipbaru']];
            $rupiah_pot_kinerja = round($tpp24 * $pot_kinerja / 100, 0);
        }
        
        ?>
    </tbody>
    <tfoot>
        <tr class="grey lighten-2">
            <td class="center-align" colspan="7"><b>POTONGAN PRESENSI</b></td>
            <td class="center-align">
                <b>
                    <?= $rekap[$key]['sum_pot']['all'] . '%' ?>
                    <?= $rupiah_pot == 0 ? '-' : ' (Rp ' . number_format($rupiah_pot, 0, ",", ".") . ')' ?>
                </b>
            </td>
        </tr>
        <tr class="grey lighten-2">
            <td class="center-align" colspan="7"><b>POTONGAN KINERJA</b></td>
            <td class="center-align">
                <b>
                    <?= !empty($pot_kinerja) ? $pot_kinerja . '%' : '' ?>
                    <?= isset($rupiah_pot_kinerja) ? ' (Rp ' . number_format($rupiah_pot_kinerja, 0, ",", ".") . ')' : 'NAN' ?>
                </b>
            </td>
        </tr>
        <tr class="grey lighten-2">
            <td class="center-align" colspan="7"><b>JUMLAH POTONGAN TPP</b></td>
            <td class="center-align"><b><?= ($rupiah_pot + $rupiah_pot_kinerja == 0) ? '-' : 'Rp ' . number_format($rupiah_pot + $rupiah_pot_kinerja, 0, ",", ".") ?></b></td>
        </tr>
    </tfoot>
</table>
<br>
<input type="hidden" value="<?= $rekap['allverified'] ? 0 : 1 ?>" id="unverified">
<?php if (!isset($download)) { ?>
    <div class="ttd-laporan">
        <table class="ttd-tabel">
            <tr>
                <td width="50%">
                    <?php
                    // tk 3
                    if ($tingkat == 3) {
                        if (isset($laporan['kepala_opd'])) {
                            echo '<b>Mengesahkan Kepala OPD</b><br>'
                            . $laporan['kepala_opd']['nama_personil'] . '<br>'
                            . 'NIP ' . $laporan['kepala_opd']['nipbaru'] . '<br>'
                            . '(' . FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date') . ')';
                        } else {
                            echo '<b>[Belum disahkan Kepala OPD]</b>';
                        }

                        //tk 4 & 5
                    } elseif ($tingkat > 3) {
                        if (isset($laporan['admin_kota'])) {
                            echo '<b>Telah diverifikasi Admin Kota</b><br>'
                            . $laporan['admin_kota']['nama_personil'] . '<br>'
                            . 'NIP ' . $laporan['admin_kota']['nipbaru'] . '<br>'
                            . '(' . FUNC::tanggal($laporan['dt_ver_admin_kota'], 'short_date') . ')';
                        } else {
                            echo '<b>[Belum diverifikasi Admin Kota]</b>';
                        }
                    }
                    ?>
                </td>
                <td width="50%">
                    <?php
                    if ($tingkat > 1) {
                        if (isset($laporan['admin_opd'])) {
                            echo '<b>Telah diverifikasi Admin OPD</b><br>'
                            . $laporan['admin_opd']['nama_personil'] . '<br>'
                            . 'NIP ' . $laporan['admin_opd']['nipbaru'] . '<br>'
                            . '(' . FUNC::tanggal($laporan['dt_ver_admin_opd'], 'short_date') . ')';
                        } else {
                            echo '<b>[Belum diverifikasi Admin OPD]</b>';
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    if ($tingkat > 4) {
                        if (isset($laporan['kepala_bkppd'])) {
                            echo '<b>Mengesahkan Kepala BKPPD</b><br>'
                            . $laporan['kepala_bkppd']['nama_personil'] . '<br>'
                            . 'NIP ' . $laporan['kepala_bkppd']['nipbaru'] . '<br>'
                            . '(' . FUNC::tanggal($laporan['dt_sah_kepala_bkppd'], 'short_date') . ')';
                        } else {
                            echo '<b>[Belum disahkan Kepala BKPPD]</b>';
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($tingkat > 3 && $tingkat < 6) {
                        if (isset($laporan['kepala_opd'])) {
                            echo '<b>Mengesahkan Kepala OPD</b><br>'
                            . $laporan['kepala_opd']['nama_personil'] . '<br>'
                            . 'NIP ' . $laporan['kepala_opd']['nipbaru'] . '<br>'
                            . '(' . FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date') . ')';
                        } else {
                            echo '<b>[Belum disahkan Kepala OPD]</b>';
                        }
                    }

                    if ($tingkat == 6) {
                        if (isset($laporan['final'])) {
                            echo '<b>Mengesahkan Kepala OPD</b><br>'
                            . $laporan['kepala_opd']['nama_personil'] . '<br>'
                            . 'NIP ' . $laporan['kepala_opd']['nipbaru'] . '<br>'
                            . '(' . FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date') . ')';
                        } else {
                            echo '<b>[Belum disahkan Kepala OPD]</b>';
                        }
                    }
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
    $tipe = ($i == 2 || $i == 4) ? 'ver' : 'sah';

    if ($i == 2)
        $ket = 'Telah diverifikasi Admin OPD';
    elseif ($i == 3)
        $ket = 'Mengesahkan Kepala OPD';
    elseif ($i == 4)
        $ket = 'Telah diverifikasi Admin Kota';
    elseif ($i == 5)
        $ket = 'Mengesahkan Kepala BKPPD';

    if ($tingkat >= $i && isset($laporan[$level])) {
        $ttd = $path_ttd . $laporan[$level]['ttd'];
        $ttd_headers = @get_headers($ttd);

        $stempel = $path_stempel . $laporan[$level]['stempel'];
        $stempel_headers = @get_headers($stempel);

        $$level = '<div class="teks-atas"><b>' . $ket . '</b></div>
            <div class="ttd-area ttd-area-portrait">';

        if ($ttd_headers[0] == 'HTTP/1.1 200 OK') {

            $$level .= '<div class="ini-ttd ini-ttd-portrait">
                <img class="ttd" src="' . $path_ttd . $laporan[$level]['ttd'] . '" width="180">
            </div>';

            if (($level == 'kepala_opd' || $level == 'kepala_bkppd') && $stempel_headers[0] == 'HTTP/1.1 200 OK')
                $$level .= '<div class="ini-stempel ini-stempel-portrait">
                    <img class="stempel stempel-portrait" src="' . $path_stempel . $laporan[$level]['stempel'] . '" width="180">
                </div>';
        } else {
            $$level .= '<br><br><br><br><br><br><br>';
        }
        $$level .= '</div>';
        $$level .= '<p class="teks-bawah">'
                . $laporan[$level]['nama_personil'] . '<br>
            NIP ' . $laporan[$level]['nipbaru'] . '<br>
            (' . FUNC::tanggal($laporan['dt_' . $tipe . '_' . $level], 'short_date') . ')</p>';
    }
}
?>
<!--pagebreak-->
<div class="kiri-atas kiri-bawah-portrait"><?= $tingkat == 3 ? $kepala_opd : $admin_kota ?></div>
<div class="kanan-atas kanan-atas-portrait"><?= $admin_opd ?></div>
<div style="clear: both"></div>
<div class="kiri-bawah kiri-bawah-portrait"><?= $kepala_bkppd ?></div>
<div class="kanan-bawah kanan-bawah-portrait"><?= $tingkat > 3 ? $kepala_opd : '' ?></div>
<div style="clear: both"></div>

<?php
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
        <td align="center">' . $i['kode_presensi'] . '</td>
        <td>' . $i['ket_kode_presensi'] . '</td>
        <td align="center">' . ($i['pot_kode_presensi'] * 100) . '</td>
    </tr>';
}
$tambahan .= '</table>';
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$pdf = new mPDF('UTF8', 'A4');
$pdf->SetDisplayMode('fullpage');
//$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$stylesheet = file_get_contents('http://192.168.254.62/template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$pdf->AddPage();
$pdf->WriteHTML(utf8_encode($tambahan));
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$filename = 'LaporanC1' . '-' . $satker . '-' . $namabulan[$bulan - 1] . $tahun . '-tingkat' . $tingkat . '.pdf';

$pdf->Output($filename, 'D');
