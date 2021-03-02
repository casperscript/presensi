<?php
ob_start();
use comp\FUNC;
$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

$path_stempel = $this->new_simpeg_url."/simpeg/upload/stempel/";
$path_ttd = $this->new_simpeg_url."/simpeg/upload/ttd/";
/*
$path_stempel = $this->link()."upload/stempel/";
$path_ttd = $this->link()."upload/ttd/";
*/
$period = $bulan.$tahun;
if ($period != '12018' && $period != '22018') {
    if ($tingkat > 1 && !isset($laporan['admin_opd'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 2 Bulan '.$namabulan[$bulan - 1].' belum diverifikasi dan disahkan oleh Admin OPD
        </div>';
        exit;
    } elseif ($tingkat > 2 && !isset($laporan['kepala_opd'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 3 Bulan '.$namabulan[$bulan - 1].' belum diverifikasi dan disahkan oleh Kepala OPD
        </div>';
        exit;
    } elseif ($tingkat > 3 && !isset($laporan['admin_kota'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 4 Bulan '.$namabulan[$bulan - 1].' belum diverifikasi dan disahkan oleh Admin Kota
        </div>';
        exit;
    } elseif ($tingkat > 4 && !isset($laporan['kepala_bkppd'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i>
            Laporan Presensi Tingkat 5 Bulan '.$namabulan[$bulan - 1].' belum diverifikasi dan disahkan oleh Kepala BKPPD
        </div>';
        exit;
    } elseif ($tingkat > 5 && !isset($laporan['final'])) {
        echo '<div class="alert-verifikasi">
            <i class="fa fa-info-circle"></i> Laporan Presensi Final Bulan '.$namabulan[$bulan - 1].' belum disahkan oleh Kepala OPD
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
            echo ' - '.$tk;
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
        	<th class="grey lighten-2 center-align" rowspan="2">Nama / NIP / NPWP / Jabatan</th>
        	<th class="grey lighten-2 center-align" rowspan="2">Gol</th>
        	<th class="grey lighten-2 center-align" rowspan="2">TPP</th>
        	<th class="grey lighten-2 center-align" colspan="3">Persentase Potongan (%)</th>
        	<th class="grey lighten-2 center-align" rowspan="2">Total Potongan (Rp)</th>
        	<th class="grey lighten-2 center-align" rowspan="2">TPP Kotor (TPP - Tot Pot)</th>
        	<th class="grey lighten-2 center-align" rowspan="2">Pajak</th>
            <th class="grey lighten-2 center-align" rowspan="2">Diterimakan (TPP Kotor - Pajak)</th>
        	<th class="grey lighten-2 center-align" rowspan="2">Tanda Tangan</th>
        </tr>
        <tr>	
        	<th class="grey lighten-2 center-align">MK</th>
        	<th class="grey lighten-2 center-align">AP</th>
        	<th class="grey lighten-2 center-align">PK</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1; $pin_absen = '';
    $tot_tpp = 0; $tot_pot = 0; $tot_tppkotor = 0; $tot_pajak = 0; $tot_terima = 0;
    foreach ($pegawai['value'] as $peg) {
        $pin = $peg['pin_absen'];
        $sum = $rekap[$pin]['sum_pot'];
        $sum['all'] = ($sum['all'] > 100 ? 100 : $sum['all']);

        //januari&Februari 2018 --- potongan belum diberlakukan -- yg untuk dicetak    
        if ($asli != 1 && $download == 1 && (($bulan == 1 && $tahun == 2018) 
            || ($bulan == 2 && $tahun == 2018))) {
            $sum['all'] = 0; $sum['mk'] = '-'; $sum['ap'] = '-'; $sum['pk'] = '-';
        }

        //mengenolkan tunj
        /*
        $no_tunj = $peg['tunjangan_jabatan'];
        if ($no_tunj)
            $sum['all'] = 0; $sum['all'] = 0; $sum['mk'] = '-'; $sum['ap'] = '-'; $sum['pk'] = '-';
        */
        $pot = ($sum['all']/100 * $peg['nominal_tp']);
        $tpp_kotor = $peg['nominal_tp'] - $pot;
        //remove whitespace-- ambil % pajak
        $clean = str_replace(" ", "", $peg['golruang']);
        $gol = explode("/", $clean)[0];
        $pot_pajak = 0;
        if (isset($pajak[$gol])) {
            $pot_pajak = round($pajak[$gol] * $tpp_kotor);
        }

        $terima = $tpp_kotor - $pot_pajak;

    	echo '<tr>
    	<td class="center-align">'.$no.'</td>
    	<td>'.$peg['nama_personil'].
            '<br>'.$peg['nipbaru'].
            '<br>'.($peg['npwp'] ? $peg['npwp'] : '-').
            '<br>'.$peg['gol_jbtn'].
        '</td>';
    ?>
    	<td><?= $peg['golruang'] ?></td>
    	<td class="right-align"><?= ($peg['nominal_tp'] > 0 ? 'Rp '.number_format($peg['nominal_tp'], 0, ",", ".") : '-') ?></td>
        <?php 
        if ($rekap[$pin]['pot_penuh']) {
            echo '<td class="center-align" colspan="3">'.$rekap[$pin]['sum_pot']['all'].'</td>';
            $sum['all'] = 100;
        } else 
            echo "<td class='center-align'>".$sum['mk']."</td>
            <td class='center-align'>".$sum['ap']."</td>
        	<td class='center-align'>".$sum['pk']."</td>";
        ?>
    	
    	<td class="right-align"><?= ($pot > 0 ? 'Rp '.number_format($pot, 0, ",", ".") : '-') ?></td>
    	<td class="right-align"><?= ($tpp_kotor ? 'Rp '.number_format($tpp_kotor, 0, ",", ".") : '-') ?></td>
    	<td class="right-align"><?= ($pot_pajak ? 'Rp '.number_format($pot_pajak, 0, ",", ".") : '-') ?></td>
    	<td class="right-align"><?= ($terima ? 'Rp '.number_format($terima, 0, ",", ".") : '-') ?></td>
        <td></td>
        <?php
        	echo '</tr>';	
            $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
            $tot_tpp += $peg['nominal_tp'];
            $tot_pot += $pot;
            $tot_tppkotor += $tpp_kotor;
            $tot_pajak += $pot_pajak;        
            $tot_terima += $terima;

            //first page
            /*if ($download == 1 && $no == 6 && count($pegawai['value']) == ($no + 1) )
                echo '<tr style="border-right: 1px solid #fff"><td style="border: 1px solid #fff; color: #fff">.<br><br><br></td></tr>';
            elseif ($download == 1 && (($no == 13 || ($no + 2) % 8 == 0) && count($pegawai['value']) == ($no + 1)))
                echo '<tr style="border-right: 1px solid #fff"><td style="border: 1px solid #fff; color: #fff">.<br><br><br><br><br><br><br><br><br><br><br><br></td></tr>';
            */
            $no++;
        }
        ?>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="right-align"><?= 'Rp '.number_format($tot_tpp, 0, ",", ".") ?></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="right-align"><?= ($tot_pot > 0 ? 'Rp '.number_format($tot_pot, 0, ",", ".") : '-') ?></th>
            <th class="right-align"><?= 'Rp '.number_format($tot_tppkotor, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp '.number_format($tot_pajak, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp '.number_format($tot_terima, 0, ",", ".") ?></th>
            <th></th>
        </tr>
    </tbody>
</table>
<br>
<?php
if ($download == 0)
    exit;

if ($bendahara != '') {
?>
<div class="ttd-laporan">
    <table class="small-padding">
        <tr>
            <td width="50%">

            </td>
            <td width="50%" style="padding-left: 45mm">
                Pekalongan, <?= FUNC::tanggal(date("Y-m-d"), 'long_date') ?>
            </td>
        </tr>
        <tr>
            <td width="50%" align="center">
                Mengetahui, <br>
                <?= $kepala['namanya'] ?>
                <br><br><br><br>
                <u><?= $kepala['nama_personil'] ?></u><br>
                NIP <?= $kepala['nipbaru'] ?>
            </td>
            <td width="50%" align="center">
                <br>
                Bendahara Pengeluaran
                <br><br><br><br>
                <u><?= $bendahara['nama_personil'] ?></u><br>
                NIP <?= $bendahara['nipbaru'] ?>
            </td>
        </tr>
    </table>
</div>
<br>
<?php
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
$filename = 'Laporan'.$format.'-'.$satker.'-'.$namabulan[$bulan - 1].$tahun.'-tingkat'.$tingkat.'.pdf';

$pdf->Output($filename, 'D');
?>