<?php
ob_start();

use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

$path_stempel = $this->link() . "upload/stempel/";
$path_ttd = $this->link() . "upload/ttd/";
$period = $bulan . $tahun;
if (!isset($download)) {
    ?>
    <div class="row ini-kotak">
        <div class="input-field col s4 right-align" style="padding-top: 15px">
            <b>BENDAHARA PENGELUARAN</b>
        </div>
        <div class="input-field col s5" id="ini-bendahara">
            <select id="pilihbendahara">
                <option value="">-- Pilih Pegawai --</option>
                <?php
                /* foreach ($pegawai['value'] as $peg) {
                  $selected = '';
                  if (is_array($bendahara) && $bendahara['nipbaru'] == $peg['nipbaru'])
                  $selected = 'selected';

                  echo '<option value="'.$peg['nipbaru'].'" '.$selected.'>'.$peg['nama_personil'].'</option>';
                  } */
                foreach ($pilbendahara as $bend) {
                    $selected = '';
                    if (is_array($bendahara) && $bendahara['nipbaru'] == $bend['nipbaru']) {
                        $selected = 'selected';
                    }

                    echo '<option value="' . $bend['nipbaru'] . '" ' . $selected . '>' . $bend['nama_personil'] . '</option>';
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
        Format TPP <?= $tingkat ?>
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
        <?= $judul_tpp ?><br>
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
            <th class="grey lighten-2 center-align" rowspan="2">TPP Bersih (TPP Kotor - Pajak)</th>
            <th class="grey lighten-2 center-align" rowspan="2">Pot Kepesertaan BPJS Kesehatan</th>
            <th class="grey lighten-2 center-align" rowspan="2">Kurang Bayar Kepesertaan BPJS Kesehatan</th>
            <th class="grey lighten-2 center-align" rowspan="2">Diterimakan (TPP Bersih - BPJS Kes)</th>
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
        $sumPot = 0;
        $tot_tpp = 0;
        $tot_pot = 0;
        $tot_tppkotor = 0;
        $tot_pajak = 0;
        $tot_terima = 0;
        $tot_potbpjs = 0;
        $tot_kurangbayarbpjs = 0;
        $pot_kurangbayar_bpjspemda = 0;
        $tot_terimapotbpjs = 0;
//        $tot_kurangbayarbpjspemda = 0;
        foreach ($pegawai['value'] as $peg) {
            if (in_array($peg['nipbaru'], $kecuali)) {
                continue;
            }

            $pin = $peg['pin_absen'];
            $sum = $rekap[$pin]['sum_pot'];
            $sum['all'] = (($sum['mk'] + $sum['ap'] + $sum['pk']) > 100 ? 100 : $sum['all']);
            
            $sumPot = !empty($sum['tot2']) ? $sum['tot2'] : $sum['all'];
            $nominanTp = (empty($peg['nominal_tp'])) ? 0 : $peg['nominal_tp'];
            
            $pot = ($sumPot / 100 * $nominanTp);
            $tpp_kotor = $peg['nominal_tp'] - $pot;
            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $pot_pajak = 0;
            if (isset($pajak[$gol])) {
                $pot_pajak = round($pajak[$gol] * $tpp_kotor);
            }

            $terima = $tpp_kotor - $pot_pajak;
            
            $checkBpjsGaji = round((($peg['nominal_tp'] + $peg['totgaji']) > $kenabpjs['value']) ?
                    ($kenabpjs['value'] - $peg['totgaji']) * 0.01 :
                    $peg['nominal_tp'] * 0.01);
            $pot_bpjs = ($terima > $checkBpjsGaji) ? $checkBpjsGaji : $terima;
            $pot_kurangbayar_bpjs = (isset($koreksi[$peg['nipbaru']]) && ($terima > 0)) ? $koreksi[$peg['nipbaru']]['data1'] : 0;
            $pot_kurangbayar_bpjspemda = (isset($koreksi[$peg['nipbaru']]) && ($terima > 0)) ? $koreksi[$peg['nipbaru']]['data2'] : 0;
            $terima_potbpjs = round($terima - $pot_bpjs - $pot_kurangbayar_bpjs);
            ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td>
                    <?= $peg['nama_personil'] ?>
                    <br><?= $peg['nipbaru'] ?>
                    <br><?= ($peg['npwp'] ? $peg['npwp'] : '-') ?>
                    <br><?= $peg['gol_jbtn'] ?>
                </td>
                <td><?= $peg['golruang'] ?></td>
                <td class="right-align"><?= ($peg['nominal_tp'] > 0 ? 'Rp ' . number_format($peg['nominal_tp'], 0, ",", ".") : '-') ?></td>
                <?php
                if ($rekap[$pin]['pot_penuh']) {
                    echo '<td class="center-align" colspan="3">' . $rekap[$pin]['sum_pot']['all'] . '</td>';
                    $sum['all'] = 100;
                } else {
                    ?>
                    <td class='center-align'><?= $sum['mk'] ?></td>
                    <td class='center-align'><?= $sum['ap'] ?></td>
                    <td class='center-align'><?= $sum['pk'] ?></td>
                    <?php
                }
                ?>

                <td class="right-align" nowrap><?= ($pot > 0 ? 'Rp ' . number_format($pot, 0, ",", ".") : '-') ?></td>
                <td class="right-align" nowrap><?= ($tpp_kotor ? 'Rp ' . number_format($tpp_kotor, 0, ",", ".") : '-') ?></td>
                <td class="right-align" nowrap><?= ($pot_pajak ? 'Rp ' . number_format($pot_pajak, 0, ",", ".") : '-') ?></td>
                <td class="right-align" nowrap><?= ($terima ? 'Rp ' . number_format($terima, 0, ",", ".") : '-') ?></td>
                <td class="right-align" nowrap><?= ($pot_bpjs ? 'Rp ' . number_format($pot_bpjs, 0, ",", ".") : '-') ?></td> <!-- pot bpjs -->
                <td class="right-align" nowrap><?= ($pot_kurangbayar_bpjs ? 'Rp ' . number_format($pot_kurangbayar_bpjs, 0, ",", ".") : '-') ?></td> <!-- pot kurang bayar bpjs -->
                <td class="right-align" nowrap><?= ($terima_potbpjs ? 'Rp ' . number_format($terima_potbpjs, 0, ",", ".") : '-') ?></td> <!-- diterimakan dipotong bpjs -->
                <td></td>
            </tr>
            <?php
            $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
            $tot_tpp += $peg['nominal_tp'];
            $tot_pot += $pot;
            $tot_tppkotor += $tpp_kotor;
            $tot_pajak += $pot_pajak;
            $tot_terima += $terima;
            $tot_potbpjs += $pot_bpjs; //acil
            $tot_kurangbayarbpjs += $pot_kurangbayar_bpjs; //acil
//            $tot_kurangbayarbpjspemda += $pot_kurangbayar_bpjspemda; //acil
            $tot_terimapotbpjs += $terima_potbpjs; //acil
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
            <th class="right-align"><?= 'Rp ' . number_format($tot_potbpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_kurangbayarbpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= 'Rp ' . number_format($tot_terimapotbpjs, 0, ",", ".") ?></th>
            <th></th>
        </tr>
    </tbody>
</table>
<br>
<?php
if (!isset($download)) {
    exit;
}

if ($bendahara != '') {
    ?>
    <div class="ttd-laporan">
        <table class="small-padding" style="page-break-inside: avoid">
            <tr>
                <td width="50%"></td>
                <td width="50%" style="padding-left: 48mm">
                    Pekalongan, <?= FUNC::tanggal(date("Y-m-d"), 'long_date') ?>
                </td>
            </tr>
            <tr>
                <td width="50%" align="center">
                    Mengetahui, <br>
                    <?= $kepala['namanya'] ?>
                    <br><br><br><br><br>
                    <u><?= $kepala['nama_personil'] ?></u><br>
                    NIP <?= $kepala['nipbaru'] ?>
                </td>
                <td width="50%" align="center">
                    <br>
                    Bendahara Pengeluaran
                    <br><br><br><br><br>
                    <u><?= $bendahara['nama_personil'] ?></u><br>
                    NIP <?= $bendahara['nipbaru'] ?>
                </td>
            </tr>
        </table>
    </div>
    <br /><br />
    <p><strong>Keterangan</strong></p>
    <table>
        <tr><td>Jumlah TPP Kotor</td><td width="5px">:</td><td class="right-align"><?= 'Rp ' . number_format($tot_tppkotor) ?></td></tr>
        <tr><td>Pajak</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_pajak) ?></td></tr>
        <tr><td>TPP Bersih</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_terima) ?></td></tr>
        <tr><td>BPJS 1%</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_potbpjs) ?></td></tr>
        <tr><td>BPJS 1% (kurang bayar)</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_kurangbayarbpjs) ?></td></tr>
        <tr><td>TPP yang diterimakan</td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_terimapotbpjs) ?></td></tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr><td>BPJS 4% dibayar Pemda &nbsp; &nbsp; </td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_potbpjs * 4) ?></td></tr>
        <tr><td>BPJS 4% dibayar Pemda (kurang bayar) &nbsp; &nbsp; </td><td>:</td><td class="right-align"><?= 'Rp ' . number_format($tot_kurangbayarbpjs * 4) ?></td></tr>
        <tr><td>Total BPJS 5%</td><td>:</td><td class="right-align"><strong><?= 'Rp ' . number_format(($tot_potbpjs * 5) + ($tot_kurangbayarbpjs * 5)) ?></strong></td></tr>
    </table>
    <br>
    <?php
}
require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('UTF8', 'F4-L');
$pdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents($this->link() . 'template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));
$filename = 'LaporanTPP-Tahun' . $tahun . '.pdf';

$pdf->Output($filename, 'D');
?>
