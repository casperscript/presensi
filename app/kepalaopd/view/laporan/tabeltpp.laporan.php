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
if ($download == 0) {
?>
<div class="row ini-kotak">
    <div class="input-field col s4 right-align" style="padding-top: 15px">
        <b>BENDAHARA PENGELUARAN</b>
    </div>
    <div class="input-field col s5" id="ini-bendahara">
        <select id="pilihbendahara">
            <option value="">-- Pilih Pegawai --</option>
            <?php
            foreach ($pegawai['value'] as $peg) {
                $selected = '';
                if (is_array($bendahara) && $bendahara['nipbaru'] == $peg['nipbaru'])
                    $selected = 'selected';
                
                echo '<option value="'.$peg['nipbaru'].'" '.$selected.'>'.$peg['nama_personil'].'</option>';
            }
            foreach ($pilbendahara as $bend) {
                $selected = '';
                if (is_array($bendahara) && $bendahara['nipbaru'] == $bend['nipbaru'])
                    $selected = 'selected';
                
                echo '<option value="'.$bend['nipbaru'].'" '.$selected.'>'.$bend['nama_personil'].'</option>';
            }
            ?>
            <?= comp\MATERIALIZE::inputKey('bendahara', (is_array($bendahara) ? $bendahara['id_bendahara'] : '')); ?>
        </select>
    </div>
    <div class="input-field col s3 left-right">
        <button type="button" class="btn waves-effect waves-light blue" title="Tampilkan" type="button" id="btnBendahara">
            ubah
        </button>
    </div>
</div>
<?php } ?>
<div class="row lap">
    <div class="format-lap">
        Format TPP
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

        //januari 2018 --- potongan belum diberlakukan -- yg untuk dicetak    
        if ($asli != 1 && $download == 1 && $bulan == 1 && $tahun == 2018) {
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
            if ($download == 1 && $no == 6 && count($pegawai['value']) == ($no + 1) )
                echo '<tr style="border-right: 1px solid #fff"><td style="border: 1px solid #fff; color: #fff">.<br><br><br></td></tr>';
            elseif ($download == 1 && (($no == 13 || ($no + 2) % 8 == 0) && count($pegawai['value']) == ($no + 1)))
                echo '<tr style="border-right: 1px solid #fff"><td style="border: 1px solid #fff; color: #fff">.<br><br><br><br><br><br><br><br><br><br><br><br></td></tr>';

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
<?php
}

require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('UTF8', 'F4-L');
$pdf->SetDisplayMode('fullpage');
//$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$stylesheet = file_get_contents('http://192.168.254.62/template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));
$filename = 'Laporan'.$format.'-'.$satker.'-'.$namabulan[$bulan - 1].$tahun.'-tingkat'.$tingkat.'.pdf';

$pdf->Output($filename, 'D');
?>