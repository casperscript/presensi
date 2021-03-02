<?php
ob_start();

use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

$path_stempel = $this->new_simpeg_url . "/simpeg/upload/stempel/";
$path_ttd = $this->new_simpeg_url . "/simpeg/upload/ttd/";
/*
  $path_stempel = $this->link()."upload/stempel/";
  $path_ttd = $this->link()."upload/ttd/";
 */
$period = $bulan . $tahun;
if ($period != '12018' && $period != '22018') {
    if ($tingkat > 1 && !isset($laporan['admin_opd'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 2 Bulan ' . $namabulan[$bulan - 1] . ' belum diverifikasi dan disahkan oleh Admin OPD
        </div>';
        exit;
    } elseif ($tingkat > 2 && !isset($laporan['kepala_opd'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 3 Bulan ' . $namabulan[$bulan - 1] . ' belum diverifikasi dan disahkan oleh Kepala OPD
        </div>';
        exit;
    } elseif ($tingkat > 3 && !isset($laporan['admin_kota'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 4 Bulan ' . $namabulan[$bulan - 1] . ' belum diverifikasi dan disahkan oleh Admin Kota
        </div>';
        exit;
    } elseif ($tingkat > 4 && !isset($laporan['kepala_bkppd'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 5 Bulan ' . $namabulan[$bulan - 1] . ' belum diverifikasi dan disahkan oleh Kepala BKPPD
        </div>';
        exit;
    } elseif ($tingkat > 5 && !isset($laporan['final'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i> Laporan Presensi Final Bulan ' . $namabulan[$bulan - 1] . ' belum disahkan oleh Kepala OPD
            <br>
            <small>
                Ada catatan dari BKPPD yang perlu dipertimbangkan oleh Kepala OPD dan perlu dilakukan pengesahan kembali.
            </small>
        </div>';
        exit;
    }
}
?>
<div class="row lap">
    <div class="format-lap">
        Format TPP 
        <?php
        if ($period != '12018' && $period != '22018') {
            $tk = ($tingkat == 6 ? 'Final' : $tingkat);
            echo ' - ' . $tk;
        }
        ?>
        <span class="ket-small">
            <?php
            if ($period != '12018' && $period != '22018')
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
        DAFTAR PENERIMAAN TAMBAHAN PENGHASILAN<br>
        <?= $satker ?><br>
        <small>Bulan <?= $namabulan[$bulan - 1] ?> Tahun <?= $tahun ?></small>
    </b></h5>
<table class="bordered hoverable custom-border scrollable <?= (!isset($laporan['sah_final']) && $period != '12018' && $period != '22018') ? 'ini-draft' : '' ?>">
    <thead>
        <tr>
            <th class="grey lighten-2 center-align" rowspan="2">No</th>
            <th class="grey lighten-2 center-align" rowspan="2">NIP</th>
            <th class="grey lighten-2 center-align" rowspan="2">Nama</th>
            <th class="grey lighten-2 center-align" rowspan="2">Gaji Kena BPJS</th>
            <th class="grey lighten-2 center-align" rowspan="3">TPP Bersih</th>
            <th class="grey lighten-2 center-align" rowspan="2">Gaji + TPP</th>
            <th class="grey lighten-2 center-align" rowspan="2">Penghasilan kena BPJS</th>
            <th class="grey lighten-2 center-align" rowspan="2">TPP kena BPJS</th>
            <th class="grey lighten-2 center-align" colspan="2">BPJS dari TPP</th>
        </tr>
        <tr>	
            <th class="grey lighten-2 center-align">4%</th>
            <th class="grey lighten-2 center-align">1%</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $pin_absen = '';
        $tot_tppbersih = 0;
        $tot_gajitpp = 0;
        $tot_pendapatankenabpjs = 0;
        $tot_tppkenabpjs = 0;
        $tot_potbpjs4 = 0;
        $tot_potbpjs1 = 0;

        foreach ($pegawai['value'] as $peg) {
            $pin = $peg['pin_absen'];
            $sum = $rekap[$pin]['sum_pot'];
            $sum['all'] = ($sum['all'] > 100 ? 100 : $sum['all']);

            //januari&Februari 2018 --- potongan belum diberlakukan -- yg untuk dicetak    
            if ($asli != 1 && $download == 1 && (($bulan == 1 && $tahun == 2018) || ($bulan == 2 && $tahun == 2018))) {
                $sum['all'] = 0;
                $sum['mk'] = '-';
                $sum['ap'] = '-';
                $sum['pk'] = '-';
            }

            $pot = ($sum['all'] / 100 * $peg['nominal_tp']);
            $tpp_kotor = $peg['nominal_tp'] - $pot;
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $pot_pajak = 0;
            if (isset($pajak[$gol])) {
                $pot_pajak = round($pajak[$gol] * $tpp_kotor);
            }

            /* acil 20200208 */
            $tppbersih = $tpp_kotor - $pot_pajak;
            $gajitpp = $tppbersih + $peg['totgaji'];
            $pndapatankenabpjs = ($gajitpp > $kenabpjs['value']) ? $kenabpjs['value'] : $gajitpp;
            $tppkenabpjs = ($gajitpp > $kenabpjs['value']) ? $kenabpjs['value'] - $peg['totgaji'] : $tppbersih;
            $pot_bpjs1 = (($tppbersih + $peg['totgaji']) > $kenabpjs['value']) ? ($kenabpjs['value'] - $peg['totgaji']) * 0.01 : $tppbersih * 0.01;
            $pot_bpjs4 = (($tppbersih + $peg['totgaji']) > $kenabpjs['value']) ? ($kenabpjs['value'] - $peg['totgaji']) * 0.04 : $tppbersih * 0.04;
            /* END acil 20200208 */
            ?>
            <tr>
                <td class="right-align"><?= $no ?></td>
                <td class="right-align"><?= $peg['nipbaru'] ?></td>
                <td class="left-align"><?= $peg['nama_personil'] ?></td>
                <td class="right-align"><?= ($peg['totgaji'] > 0) ? 'Rp ' . number_format($peg['totgaji'], 0, ",", ".") : '-' ?></td>
                <td class="right-align"><?= ($tppbersih > 0) ? 'Rp ' . number_format($tppbersih, 0, ",", ".") : '-' ?></td>
                <td class="right-align"><?= ($gajitpp > 0) ? 'Rp ' . number_format($gajitpp, 0, ",", ".") : '-' ?></td>
                <td class="right-align"><?= ($pndapatankenabpjs > 0 ? 'Rp ' . number_format($pndapatankenabpjs, 0, ",", ".") : '-') ?></td>
                <td class="right-align"><?= ($tppkenabpjs > 0 ? 'Rp ' . number_format($tppkenabpjs, 0, ",", ".") : '-') ?></td>
                <td class="right-align"><?= ($pot_bpjs4 > 0 ? 'Rp ' . number_format($pot_bpjs4, 0, ",", ".") : '-') ?></td>
                <td class="right-align"><?= ($pot_bpjs1 > 0 ? 'Rp ' . number_format($pot_bpjs1, 0, ",", ".") : '-') ?></td>
            <tr>
                <?php
                $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
                $tot_tppbersih += $tppbersih;
                $tot_gajitpp += $gajitpp;
                $tot_pendapatankenabpjs += $pndapatankenabpjs;
                $tot_tppkenabpjs += $tppkenabpjs;
                $tot_potbpjs4 += $pot_bpjs4; //acil
                $tot_potbpjs1 += $pot_bpjs1;

                $no++;
            }
            ?>
        <tr>
            <th colspan="4"></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_tppbersih, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_gajitpp, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_pendapatankenabpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_tppkenabpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_potbpjs4, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_potbpjs1, 0, ",", ".") ?></th>
        </tr>
    </tbody>
</table>
<br>

<?php
if ($download == 0) {
    exit;
}

require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('UTF8', 'F4-L');
$pdf->SetDisplayMode('fullpage');
//$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$stylesheet = file_get_contents('http://192.168.254.62/template/theme_admin/assets/css/laporanpdf.css', true);

//BEGIN - tambah watermark di cetak pdf jk blum laporan final
if (!isset($laporan['sah_final']) && $period != '12018' && $period != '22018') {
    $pdf->SetWatermarkText('DRAFT');
    $pdf->showWatermarkText = true;
}
//END - tambah watermark di cetak pdf jk blum laporan final

$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));
$filename = 'Laporan' . $format . '-' . $satker . '-' . $namabulan[$bulan - 1] . $tahun . '-tingkat' . $tingkat . '.pdf';

$pdf->Output($filename, 'D');
?>