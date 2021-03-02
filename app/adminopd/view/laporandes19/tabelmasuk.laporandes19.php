<?php
ob_start();
use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hitungtgl = 16;
?>
<br><br>
<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>1 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
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
Laporan Rekap Kehadiran/Ketidakhadiran Masuk Kerja Karyawan <br>
OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun?>
</b></h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="orange lighten-4 center-align" rowspan="2">No</th>
            <th class="orange lighten-4 center-align" rowspan="2">Nama</th>
            <th class="orange lighten-4 center-align" colspan="<?= $hitungtgl ?>">Tanggal</th>
        </tr>
        <tr>
            <?php
                for ($i = 1; $i <= $hitungtgl; $i++)
                    echo "<th class='orange lighten-4 center-align' width='50px'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; $pin_absen = ''; $allverified = true;
        foreach ($pegawai['value'] as $peg) { ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 200px"><?= $peg['nama_personil'] ?></td>
                <?php
                    $pin = $peg['pin_absen'];
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        $masuk = $rekap[$pin][$i]['mk'];
                        echo '<td class="center-align '.$masuk['color'].'">'.$masuk['kode'].'</td>';
                    }
                ?>
            </tr>
        <?php 
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
            $no++;
            /*
            if ($download == 1 && (count($pegawai['value']) > 17 && $no == 17))
                echo '<tr style="border-right: 1px solid #fff"><td style="border: 1px solid #fff">.<br><br><br><br><br><br><br><br><br><br><br><br>.</td></tr>';
                */
        } 
        ?>
    </tbody>
</table>
<br>
<input type="hidden" value="<?= $rekap['allverified'] ? 'yes' : 'no' ?>" id="allverified">
<?php if ($download == 0) {  ?>
<div class="ttd-laporan">
    <table class="ttd-tabel">
         <tr>
            <td width="50%">
                <?php 
                if ($tingkat == 3)
                    echo '<b>Mengesahkan '.$kepala['jabatan_pengguna'].' Kepala OPD</b><br>'.
                    $kepala['nama_personil'].'
                    <br><br><br><br>
                    NIP '.$kepala['nipbaru'].'<br>
                    (......................................)';
                ?>                
            </td>
            <td width="50%">
                <?php if ($tingkat > 1)
                    echo '<b>Telah diverifikasi '.$adminopd['jabatan_pengguna'].' Admin OPD</b><br>'.
                    $adminopd['nama_personil'].'
                    <br><br><br><br>
                    NIP '.$adminopd['nipbaru'].'<br>
                    (........................................)';
                ?>
            </td>
        </tr>
    </table>
</div>
<br>
<div class="center-align">
    <button class="btn waves-effect waves-light indigo" title="Cetak" type="button" id="<?= $rekap['allverified'] ? 'btnCetak' : 'btnMod' ?>">
         <i class="material-icons left">print</i> CETAK LAPORAN
     </button>
</div>
<?php
    exit;
}

if ($tingkat == 3) {
$ket = 'Mengesahkan '.$kepala['jabatan_pengguna'].' Kepala OPD';
echo '<div class="kiri-atas"><div class="teks-atas"><b>'.$ket.'</b></div>
        <div class="ttd-area"><br><br><br><br><br><br><br><br><br></div>
        <p class="teks-bawah">'
        .$kepala['nama_personil'].'<br>
        NIP '.$kepala['nipbaru'].'<br>
        (.........................................)</p></div>';
}

if ($tingkat > 1) {
$ket = 'Telah diverifikasi '.$adminopd['jabatan_pengguna'].' Admin OPD';
echo '<div class="kanan-atas"><div class="teks-atas"><b>'.$ket.'</b></div>
        <div class="ttd-area"><br><br><br><br><br><br><br><br><br></div>
        <p class="teks-bawah">'
        .$adminopd['nama_personil'].'<br>
        NIP '.$adminopd['nipbaru'].'<br>
        (.........................................)</p></div>';
}

require_once ('comp/mpdf60/mpdf.php');
$html = ob_get_contents();
ob_end_clean();

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$bagi = round((count($kode)-1) / 2);
$tambahan = '<div style="width: 48%; float:left">
<table class="bordered custom-border">
    <thead>
        <tr>
            <th width="20%">Kode Presensi</th>
            <th>Keterangan</th>
            <th width="20%">Potongan (%)</th>
        </tr>
    </thead>
';
for ($i = 0; $i <= $bagi; $i++) {
    $tambahan .= '<tr>
        <td align="center">'.$kode[$i]['kode_presensi'].'</td>
        <td>'.$kode[$i]['ket_kode_presensi'].'</td>
        <td align="center">'.($kode[$i]['pot_kode_presensi']*100).'</td>
    </tr>';
}
$tambahan .= '</table></div>';

$tambahan .= '<div style="width: 48%; float:right">
<table class="bordered custom-border">
    <thead>
        <tr>
            <th width="20%">Kode Presensi</th>
            <th>Keterangan</th>
            <th width="20%">Potongan (%)</th>
        </tr>
    </thead>
';
for ($i; $i < count($kode); $i++) {
    $tambahan .= '<tr>
        <td align="center">'.$kode[$i]['kode_presensi'].'</td>
        <td>'.$kode[$i]['ket_kode_presensi'].'</td>
        <td align="center">'.($kode[$i]['pot_kode_presensi']*100).'</td>
    </tr>';
}
$tambahan .= '</table></div>';
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

$pdf = new mPDF('UTF8', 'F4-L', 10);
$pdf->SetDisplayMode('fullpage');
//$stylesheet = file_get_contents($this->link().'template/theme_admin/assets/css/laporanpdf.css', true);
$stylesheet = file_get_contents('http://192.168.254.62/template/theme_admin/assets/css/laporanpdfcustom.css', true);
$pdf->WriteHTML($stylesheet, 1);
$pdf->WriteHTML(utf8_encode($html));

/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */
$pdf->AddPage();
$pdf->WriteHTML(utf8_encode($tambahan));
/* --CETAK HALAMAN KETERANGAN KODE PRESENSI-- */

if ($tingkat == 6)
    $tingkat = 'Final';
$filename = 'Laporan'.$format.'1-'.$satker.'-'.$namabulan[$bulan - 1].$tahun.'-tingkat'.$tingkat.'.pdf';

$pdf->Output($filename, 'D');
?>