<?php
ob_start();

use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

$path_stempel = $this->link() . "upload/stempel/";
$path_ttd = $this->link() . "upload/ttd/";
$period = $bulan . $tahun;

if ($period == '122018') {
    echo '<div class="alert-verifikasi">
        <i class="fa fa-info-circle"></i>
        Cetak TPP Bulan ' . $namabulan[$bulan - 1] . ' ' . $tahun . ' melalui menu <a href="../../adminopd/laporandes18/tpp" style="color: #000">Desember 2018</a>
    </div>';
    exit;
} elseif ($period == '122019') {
    echo '<div class="alert-verifikasi">
        <i class="fa fa-info-circle"></i>
        Cetak TPP Bulan ' . $namabulan[$bulan - 1] . ' ' . $tahun . ' melalui menu <a href="../../adminopd/laporandes19/tpp" style="color: #000">Desember 2019</a>
    </div>';
    exit;
}

if (count($rekap) == 0) {
    echo '<h5 class="center-align teal lighten-4" style="padding: 15px 0"><b>
    Data backup presensi tidak ditemukan.
    </b></h5>';
    exit;
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
<table class="bordered hoverable custom-border scrollable">
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
            <?php if ($tahun >= 2020) { ?>
                <th class="grey lighten-2 center-align" rowspan="2">TPP Bersih (TPP Kotor - Pajak)</th>
                <th class="grey lighten-2 center-align" rowspan="2">Pot Kepesertaan BPJS Kesehatan</th>
                <th class="grey lighten-2 center-align" rowspan="2">Diterimakan (TPP Bersih - BPJS Kes)</th>
            <?php } else { ?>
                <th class="grey lighten-2 center-align">Diterimakan <br> (TPP - Pajak)</th>
            <?php } ?>
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
        $no = 1;
        $pin_absen = '';
        $tot_tpp = 0;
        $tot_pot = 0;
        $tot_tppkotor = 0;
        $tot_pajak = 0;
        $tot_terima = 0;
        $tot_potbpjs = 0;
        $tot_terimapotbpjs = 0;
        foreach ($pegawai['value'] as $peg) {
            $pin = $peg['pin_absen'];
            $find = $rekap[$peg['id']];
            $pot_penuh = json_decode($find['pot_penuh'], true)[$tingkat];
            $sum = json_decode($find['sum_pot'], true)[$tingkat];
            $sum['all'] = ($sum['all'] > 100 ? 100 : $sum['all']);

            $pot = ($sum['all'] / 100 * $peg['nominal_tp']);
            $tpp_kotor = $peg['nominal_tp'] - $pot;
            //remove whitespace-- ambil % pajak
            $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);

            $terima = $tpp_kotor - $pot_pajak;
            $pot_bpjs = $find['pot_bpjskes'];
            $terima_potbpjs = $find['tpp_terima'];

            echo '<tr>
    	<td class="center-align">' . $no . '</td>
    	<td>' . $peg['nama_personil'] .
            '<br>' . $peg['nipbaru'] .
            '<br>' . ($peg['npwp'] ? $peg['npwp'] : '-') .
            '<br>' . $peg['gol_jbtn'] .
            '</td>';
            ?>
        <td><?= $peg['golruang'] ?></td>
        <td class="right-align"><?= ($peg['nominal_tp'] > 0 ? 'Rp ' . number_format($peg['nominal_tp'], 0, ",", ".") : '-') ?></td>
        <?php
        if ($pot_penuh) {
            echo '<td class="center-align" colspan="3">' . $sum['all'] . '</td>';
            $sum['all'] = 100;
        } else
            echo "<td class='center-align'>" . $sum['mk'] . "</td>
            <td class='center-align'>" . $sum['ap'] . "</td>
        	<td class='center-align'>" . $sum['pk'] . "</td>";
        ?>

        <td class="right-align"><?= ($pot > 0 ? 'Rp ' . number_format($pot, 0, ",", ".") : '-') ?></td>
        <!-- <td class="right-align"><?= $pot . ' pot' ?></td> -->
        <td class="right-align"><?= ($tpp_kotor ? 'Rp ' . number_format($tpp_kotor, 0, ",", ".") : '-') ?></td>
        <!-- <td class="right-align"><?= $tpp_kotor ?></td> -->
        <td class="right-align"><?= ($pot_pajak ? 'Rp ' . number_format($pot_pajak, 0, ",", ".") : '-') ?></td>
        <td class="right-align"><?= ($terima ? 'Rp ' . number_format($terima, 0, ",", ".") : '-') ?></td>
        <!-- <td class="right-align"><?= $terima ?></td> -->
        <?php if ($tahun >= 2020) { ?>
            <td class="right-align"><?= ($pot_bpjs ? 'Rp ' . number_format($pot_bpjs, 0, ",", ".") : '-') ?></td> <!-- pot bpjs -->
            <td class="right-align"><?= ($terima_potbpjs ? 'Rp ' . number_format($terima_potbpjs, 0, ",", ".") : '-') ?></td> <!-- diterimakan dipotong bpjs -->
        <?php } ?>
        <td></td>
        <?php
        echo '</tr>';
        $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
        $tot_tpp += $peg['nominal_tp'];
        $tot_pot += $pot;
        $tot_tppkotor += $tpp_kotor;
        $tot_pajak += $pot_pajak;
        $tot_terima += $terima;
        $tot_potbpjs += $pot_bpjs;
        $tot_terimapotbpjs += $terima_potbpjs;

        //first page
        /* if ($download == 1 && $no == 6 && count($pegawai['value']) == ($no + 1) )
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
        <th class="right-align"><?= 'Rp ' . number_format($tot_tpp, 0, ",", ".") ?></th>
        <th></th>
        <th></th>
        <th></th>
        <th class="right-align"><?= ($tot_pot > 0 ? 'Rp ' . number_format($tot_pot, 0, ",", ".") : '-') ?></th>
        <th class="right-align"><?= 'Rp ' . number_format($tot_tppkotor, 0, ",", ".") ?></th>
        <th class="right-align"><?= 'Rp ' . number_format($tot_pajak, 0, ",", ".") ?></th>
        <th class="right-align"><?= 'Rp ' . number_format($tot_terima, 0, ",", ".") ?></th>
        <?php if ($tahun >= 2020) { ?>
            <th class="right-align"><?= 'Rp ' . number_format($tot_potbpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_terimapotbpjs, 0, ",", ".") ?></th>
        <?php } ?>
        <th></th>
    </tr>
</tbody>
</table>
<br>
<input type="hidden" id="verified" value="<?= isset($laporan['kepala_opd']) ? 1 : 0 ?>">
<?php
if ($download == 0) {
    echo '<br><br>
    <div style="color: #ddd;">source: db_backup-rekapbc</div>';
    exit;
}

if ($tpp['nip_kepala'] != '' && $tpp['nip_bendahara'] != '') {
    ?>
    <div class="ttd-laporan">
        <table class="small-padding">
            <tr>
                <td width="50%">

                </td>
                <td width="50%" style="padding-left: 48mm">
                    Pekalongan, <?= FUNC::tanggal($tpp['tgl_cetak'], 'long_date') ?>
                </td>
            </tr>
            <tr>
                <td width="50%" align="center">
                    Mengetahui, <br>
                    <?= $tpp['jabatan_kepala'] ?>
                    <br><br><br><br>
                    <u><?= $tpp['nama_kepala'] ?></u><br>
                    NIP <?= $tpp['nip_kepala'] ?>
                </td>
                <td width="50%" align="center">
                    <br>
                    Bendahara Pengeluaran
                    <br><br><br><br>
                    <u><?= $tpp['nama_bendahara'] ?></u><br>
                    NIP <?= $tpp['nip_bendahara'] ?>
                </td>
            </tr>
        </table>
    </div>
	<?php if ($tahun >= 2020) { ?>
		<br /><br />
		<p><strong>Keterangan</strong></p>
		<table>
			<tr><td>Jumlah TPP Kotor</td><td width="5px">:</td><td class="right-align"><?= 'Rp ' . number_format(round($tot_tppkotor)) ?></td></tr>
			<tr><td>Pajak</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format(round($tot_pajak)) ?></td></tr>
			<tr><td>TPP Bersih</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format(round($tot_terima)) ?></td></tr>
			<tr><td>BPJS 1%</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format(round($tot_potbpjs)) ?></td></tr>
			<tr><td>TPP yang diterimakan</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format(round($tot_terimapotbpjs)) ?></td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td>BPJS 4% dibayar Pemda &nbsp; &nbsp; </td><td>:</td><td class="right-align"><?= 'Rp ' . number_format(round($tot_potbpjs * 4)) ?></td></tr>
            <tr><td>Total BPJS 5%</td><td>:</td><td class="right-align"><strong><?= 'Rp ' . number_format(round($tot_potbpjs * 5)) ?></strong></td></tr>
        </table>
	<?php } ?>
    <br>
    <?php
}

require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('UTF8', 'F4-L');
$pdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents($this->link() . 'template/theme_admin/assets/css/laporanpdf.css', true);

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
