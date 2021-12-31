<?php
ob_start();
$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
// comp\FUNC::showPre($data);exit;
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
<h5 class="center-align">
    <b>
        <?= $judul_tpp ?><br>
        <?= $satker ?><br>
        <small>Bulan <?= $namabulan[$bulan - 1] ?> Tahun <?= $tahun ?></small>
    </b>
</h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="grey lighten-2 center-align" rowspan="3">No</th>
            <th class="grey lighten-2 center-align" rowspan="3">Nama / NIP / NPWP / Jabatan</th>
            <th class="grey lighten-2 center-align" rowspan="3">Kelas Jab.</th>
            <th class="grey lighten-2 center-align" colspan="3">Tambahan Penghasilan<br />(Rp)</th>
            <th class="grey lighten-2 center-align" colspan="4">Persentase Potongan<br />(%)</th>
            <th class="grey lighten-2 center-align" rowspan="3">Total Potongan<br />(Rp)</th>
            <th class="grey lighten-2 center-align" rowspan="3">TPP Kotor (TPP - Tot Pot)<br />(Rp)</th>
            <th class="grey lighten-2 center-align" rowspan="3">Pajak<br />(Rp)</th>
            <th class="grey lighten-2 center-align" rowspan="3">TPP Bersih (TPP Kotor - Pajak)<br />(Rp)</th>
            <th class="grey lighten-2 center-align" rowspan="3">Pot Kepesertaan BPJS Kesehatan<br />(Rp)</th>
            <th class="grey lighten-2 center-align" rowspan="3">Diterimakan (TPP Bersih - BPJS Kes)<br />(Rp)</th>
            <th class="grey lighten-2 center-align" rowspan="3">Tanda Tangan</th>
        </tr>
        <tr>
            <th class="grey lighten-2 center-align" rowspan="2">Beban Kerja (40%)</th>
            <th class="grey lighten-2 center-align" colspan="2">Prestasi Kerja (60%)</th>
            <th class="grey lighten-2 center-align" colspan="3">e-Presensi</th>
            <th class="grey lighten-2 center-align" rowspan="2">e-Ki nerja</th>
        </tr>
        <tr>
            <th class="grey lighten-2 center-align">e-Presensi (36%)</th>
            <th class="grey lighten-2 center-align">e-Kinerja (24%)</th>
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
        $tot_tpp40 = 0;
        $tot_tpp36 = 0;
        $tot_tpp24 = 0;
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

            $sum['text'] = $sum['all'];
            $sum['all'] = (!is_numeric($sum['all']) ? 100 : $sum['all']);
            $sum['all'] = ($sum['all'] > 100 ? 100 : $sum['all']);
            $pot_kinerja = 100 - $find['poin_kinerja'];

            $nominal_tp40 = $peg['nominal_tp'] * 40 / 100;
            $nominal_tp36 = $peg['nominal_tp'] * 36 / 100;
            $nominal_tp24 = $peg['nominal_tp'] * 24 / 100;

            $pot = round($sum['all'] / 100 * $nominal_tp36 + ($pot_kinerja / 100 * $nominal_tp24), 0);
            $tpp_kotor = $peg['nominal_tp'] - $pot;
            //remove whitespace-- ambil % pajak
            $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);

            $terima = round($tpp_kotor - $pot_pajak);
            $pot_bpjs = $find['pot_bpjskes'];
            $terima_potbpjs = $find['tpp_terima'];

            if (isset($sum['tk']) && $sum['tk'] > 10) {
                $ket = 'TK > 10 kali';
                $pot = $peg['nominal_tp'];
                $tpp_kotor = 0;
                $pot_pajak = 0;
                $terima = 0;
                $pot_bpjs = 0;
                $terima_potbpjs = 0;
            }
        ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td>
                    <?= $peg['nama_personil'] ?>
                    <br><?= $peg['nipbaru'] ?>
                    <br><?= ($peg['npwp'] ? $peg['npwp'] : '-') ?>
                    <br>Golongan <?= $peg['golruang'] ?>
                </td>
                <td class="center-align"><?= $peg['kelas'] ?></td>
                <td class="right-align"><?= ($nominal_tp40 > 0 ? number_format($nominal_tp40, 0, ",", ".") : '0') ?></td>
                <td class="right-align"><?= ($nominal_tp36 > 0 ? number_format($nominal_tp36, 0, ",", ".") : '0') ?></td>
                <td class="right-align"><?= ($nominal_tp24 > 0 ? number_format($nominal_tp24, 0, ",", ".") : '0') ?></td>
                <?php if (isset($sum['tk']) && $sum['tk'] > 10) : ?>
                    <td class="center-align" colspan="4"><?= $ket ?></td>
                <?php
                elseif ($pot_penuh) :
                    $sum['all'] = 100;
                ?>
                    <td class="center-align" colspan="3"><?= $sum['text'] ?></td>
                    <td class="center-align"><?= $pot_kinerja ?></td>
                <?php else : ?>
                    <td class="center-align"><?= $sum['mk'] ?></td>
                    <td class="center-align"><?= $sum['ap'] ?></td>
                    <td class="center-align"><?= $sum['pk'] ?></td>
                    <td class="center-align"><?= $pot_kinerja ?></td>
                <?php endif; ?>

                <td class="right-align"><?= ($pot > 0 ? number_format($pot, 0, ",", ".") : '0') ?></td> <!-- total potongan kehadiran -->
                <td class="right-align"><?= ($tpp_kotor ? number_format($tpp_kotor, 0, ",", ".") : '0') ?></td> <!-- tpp kotor -->
                <td class="right-align"><?= ($pot_pajak ? number_format($pot_pajak, 0, ",", ".") : '0') ?></td> <!-- pajak -->
                <td class="right-align"><?= ($terima ? number_format($terima, 0, ",", ".") : '0') ?></td> <!-- tpp bersih -->
                <td class="right-align"><?= ($pot_bpjs ? number_format($pot_bpjs, 0, ",", ".") : '0') ?></td> <!-- potongan bpjs -->
                <td class="right-align"><?= ($terima_potbpjs ? number_format($terima_potbpjs, 0, ",", ".") : '0') ?></td> <!-- diterimakan dipotong bpjs -->
                <td></td>
            </tr>
        <?php
            $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
            $tot_tpp += $peg['nominal_tp'];
            $tot_tpp40 += $nominal_tp40;
            $tot_tpp36 += $nominal_tp36;
            $tot_tpp24 += $nominal_tp24;
            $tot_pot += $pot;
            $tot_tppkotor += $tpp_kotor;
            $tot_pajak += $pot_pajak;
            $tot_terima += $terima;
            $tot_potbpjs += $pot_bpjs; //acil
            $tot_terimapotbpjs += $terima_potbpjs; //acil

            $no++;
        }
        ?>
        <tr>
            <th colspan="3"></th>
            <th class="right-align"><?= number_format($tot_tpp40, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_tpp36, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_tpp24, 0, ",", ".") ?></th>
            <th colspan="4"></th>
            <th class="right-align"><?= ($tot_pot > 0 ? number_format($tot_pot, 0, ",", ".") : '0') ?></th>
            <th class="right-align"><?= number_format($tot_tppkotor, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_pajak, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_terima, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_potbpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_terimapotbpjs, 0, ",", ".") ?></th>
            <th></th>
        </tr>
    </tbody>
</table>
<br /><br /><br />
<?php
if (!isset($download)) {
    echo '<div style="color: #ddd;">source: db_backup_desember-tabeltpp2021potbc</div>';
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
                <td width="50%" style="padding-left: 48mm">
                    Pekalongan, <?= comp\FUNC::tanggal(date("Y-m-d"), 'long_date') ?>
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

require_once('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('UTF8', 'F4-L');
$pdf->SetDisplayMode('fullpage');
$stylesheet = file_get_contents($this->link() . 'template/theme_admin/assets/css/laporanpdf.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));
$filename = 'LaporanTPP14-Tahun' . $tahun . '.pdf';

$pdf->Output($filename, 'D');
?>