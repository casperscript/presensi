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
                foreach ($pegawai['value'] as $peg) {
                    $selected = '';
                    if (is_array($bendahara) && $bendahara['nipbaru'] == $peg['nipbaru']) {
                        $selected = 'selected';
                    }

                    echo '<option value="' . $peg['nipbaru'] . '" ' . $selected . '>' . $peg['nama_personil'] . '</option>';
                }
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
<div class="row lap" <?= $jenis_tpp == 'TPPDES' ? '' : 'style="display: none"' ?>>
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
<?php if ($jenis_tpp == 'TPPDES') { ?>
    <h5 class="center-align"><b>
            DAFTAR PENERIMAAN TAMBAHAN PENGHASILAN<br>
            <?= $satker ?><br>
            <small>Bulan <?= $namabulan[$bulan - 1] ?> Tahun <?= $tahun ?></small>
        </b></h5>
<?php } else { ?>
    <h5 class="center-align">
        <b><?= $judul_tpp ?></b>
    </h5><br>
    <span>Nama OPD : <?= $satker ?></span><br>
<?php } ?>
<table class="bordered hoverable custom-border">
    <thead>
        <tr>
            <th class="grey lighten-2 center-align" width="30">No</th>
            <th class="grey lighten-2 center-align">Nama / NIP / NPWP / Jabatan</th>
            <th class="grey lighten-2 center-align">Gol</th>
            <th class="grey lighten-2 center-align">TPP</th>
            <th class="grey lighten-2 center-align">Pajak</th>
            <th class="grey lighten-2 center-align">Diterimakan <br> (TPP - Pajak)</th>
            <th class="grey lighten-2 center-align" width="80">Tanda Tangan</th>
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
        foreach ($pegawai['value'] as $peg) {
            $find = $rekap[$peg['id']];
            $pot_penuh = json_decode($find['pot_penuh'], true)[$tingkat];
            $sum = json_decode($find['sum_pot'], true)[$tingkat];
            $sum['all'] = ($sum['all'] > 100 ? 100 : $sum['all']);

            if ($sum['all'] == 100 || $pot_penuh) {
                continue;
            }

            $pin = $peg['pin_absen'];
            $tpp_kotor = $peg['nominal_tp'];
            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);
            $terima = $tpp_kotor - $pot_pajak;

            echo '<tr>
    	<td class="center-align">' . $no . '</td>
    	<td>' . $peg['nama_personil'] .
            '<br>' . $peg['nipbaru'] .
            '<br>' . ($peg['npwp'] ? $peg['npwp'] : '-') .
            '<br>' . $peg['gol_jbtn'] .
            '</td>';
            ?>
        <td class="center-align"><?= $peg['golruang'] ?></td>    	
        <td class="right-align"><?= ($tpp_kotor ? 'Rp ' . number_format($tpp_kotor, 0, ",", ".") : '-') ?></td>
        <td class="right-align"><?= ($pot_pajak ? 'Rp ' . number_format($pot_pajak, 0, ",", ".") : '-') ?></td>
        <td class="right-align"><?= ($terima ? 'Rp ' . number_format($terima, 0, ",", ".") : '-') ?></td>
        <td></td>
        <?php
        echo '</tr>';
        $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
        $tot_tpp += $peg['nominal_tp'];
        $tot_tppkotor += $tpp_kotor;
        $tot_pajak += $pot_pajak;
        $tot_terima += $terima;

        /* BEGIN-komen kode ini jika tampilannya aneh */
        if (isset($download)) {
            //first page
            $exp = [11, 12, 25, 81];
            if (in_array($pegawai['count'], $exp) && $pegawai['count'] - 1 == $no) {
                echo '<tr class="white-row"><td>.<br><br><br><br><br><br><br><br><br><br><br><br></td></tr>';
            } elseif ($pegawai['count'] == 13 && $no == 12) {
                echo '<tr class="white-row"><td>.<br><br><br><br></td></tr>';
            } elseif (($pegawai['count'] - 13) % 14 == 0 && $no % ($pegawai['count'] - 1) == 0) {
                echo '<tr class="white-row"><td>.<br><br><br><br><br><br><br></td></tr>';
            } elseif ($pegawai['count'] % 13 == 0 && $no % ($pegawai['count'] - 1) == 0) {
                echo '<tr class="white-row"><td>.<br><br><br><br><br><br><br><br><br>' . ($pegawai['count'] == 39 ? '<br><br><br>' : '') . '</td></tr>';
            }
        }
        /* END-komen kode ini jika tampilannya aneh */
        $no++;
    }
    ?>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th class="right-align"><?= 'Rp ' . number_format($tot_tppkotor, 0, ",", ".") ?></th>
        <th class="right-align"><?= 'Rp ' . number_format($tot_pajak, 0, ",", ".") ?></th>
        <th class="right-align"><?= 'Rp ' . number_format($tot_terima, 0, ",", ".") ?></th>
        <th></th>
    </tr>
</tbody>
</table>
<br>
<?php
if (!isset($download)) {
    exit;
}

echo '</div>';

if ($bendahara != '') {
    ?>
    <div class="ttd-laporan">
        <table class="small-padding">
            <tr>
                <td width="50%">

                </td>
                <td width="50%" style="padding-left: 25mm">
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
$pdf = new mPDF('UTF8', 'F4');
$pdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents($this->link() . 'template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));
$filename = 'LaporanTPP14-Tahun' . $tahun . '.pdf';

$pdf->Output($filename, 'D');
?>
