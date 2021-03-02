<?php
ob_start();

use comp\FUNC;

$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$path_stempel = $this->new_simpeg_url . "/simpeg/upload/stempel/";
$path_ttd = $this->new_simpeg_url . "/simpeg/upload/ttd/";
/*
  $path_stempel = $this->link()."upload/stempel/";
  $path_ttd = $this->link()."upload/ttd/";
 */
?>
<div class="alert-verifikasi alert-verifikasi-ok <?= isset($laporan['kepala_bkppd']) ? '' : 'hide' ?>">
    <span class="blink">
        <i class="fa fa-check-circle"></i>
        Laporan Telah Disahkan Kepala BKPPD
    </span>
</div>
<div class="alert-verifikasi <?= !isset($laporan['kepala_bkppd']) || !$rekap['allverified'] ? '' : 'hide' ?>">
    <span class="blink">
        <i class="fa fa-angle-double-down"></i>
        Anda belum melakukan <?= !$rekap['allverified'] ? 'verifikasi' : 'pengesahan' ?>
        <i class="fa fa-angle-double-down"></i>
    </span>
</div>
<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>1 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
    </div>
</div>
<h5 class="center-align" style="color: #e65100"><b>
        Laporan Rekap Kehadiran/Ketidakhadiran Masuk Kerja Karyawan <br>
        OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun ?>
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
                echo "<th class='orange lighten-4 center-align' width='25'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $pin_absen = '';
        $allverified = true;
        foreach ($pegawai['value'] as $peg) {
            ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 180px"><?= $peg['nama_personil'] ?></td>
                <?php
                $pin = $peg['pin_absen'];
                for ($i = 1; $i <= $hitungtgl; $i++) {
                    $masuk = $rekap[$pin][$i]['mk'];
                    echo '<td class="center-align ' . $masuk['color'] . '">' . $masuk['kode'] . '</td>';
                }
                ?>
            </tr>
            <?php
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
            $no++;
        }
        ?>
    </tbody>
</table>
<br><br>

<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>2 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
    </div>
</div>
<h5 class="center-align" style="color: #01579b"><b>
        Laporan Rekap Kehadiran/Ketidakhadiran Apel Pagi Karyawan <br>
        OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun ?>
    </b></h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="light-blue lighten-4 center-align" rowspan="2">No</th>
            <th class="light-blue lighten-4 center-align" rowspan="2">Nama</th>
            <th class="light-blue lighten-4 center-align" colspan="<?= $hitungtgl ?>">Tanggal</th>
        </tr>
        <tr>
            <?php
            for ($i = 1; $i <= $hitungtgl; $i++)
                echo "<th class='light-blue lighten-4 center-align' width='25'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $pin_absen = '';
        $allverified = true;
        foreach ($pegawai['value'] as $peg) {
            ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 180px"><?= $peg['nama_personil'] ?></td>
                <?php
                $pin = $peg['pin_absen'];
                for ($i = 1; $i <= $hitungtgl; $i++) {
                    $apel = $rekap[$pin][$i]['ap'];
                    echo '<td class="center-align ' . $apel['color'] . '">' . $apel['kode'] . '</td>';
                }
                ?>
            </tr>
            <?php
            $no++;
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
        }
        ?>
    </tbody>
</table>
<br><br>

<div class="row lap">
    <div class="format-lap">
        Format <?= $format ?>3 - <?= $tingkat == 6 ? 'Final' : $tingkat ?>
    </div>
</div>
<h5 class="center-align" style="color: #33691e"><b>
        Laporan Rekap Finger Print Pulang Kerja Karyawan <br>
        OPD/Unit Kerja: <?= $satker ?> Bulan: <?= $namabulan[$bulan - 1] ?> Tahun: <?= $tahun ?>
    </b></h5>
<table class="bordered hoverable custom-border scrollable">
    <thead>
        <tr>
            <th class="light-green lighten-4 center-align" rowspan="2">No</th>
            <th class="light-green lighten-4 center-align" rowspan="2">Nama</th>
            <th class="light-green lighten-4 center-align" colspan="<?= $hitungtgl ?>">Tanggal</th>
        </tr>
        <tr>
            <?php
            for ($i = 1; $i <= $hitungtgl; $i++)
                echo "<th class='light-green lighten-4 center-align' width='25'>$i</th>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $pin_absen = '';
        $allverified = true;
        foreach ($pegawai['value'] as $peg) {
            ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td style="min-width: 180px"><?= $peg['nama_personil'] ?></td>
                <?php
                $pin = $peg['pin_absen'];
                for ($i = 1; $i <= $hitungtgl; $i++) {
                    $pulang = $rekap[$pin][$i]['pk'];
                    echo '<td class="center-align ' . $pulang['color'] . '">' . $pulang['kode'] . '</td>';
                }
                ?>
            </tr>
            <?php
            $pin_absen .= $peg['pin_absen'] . (count($pegawai['value']) != $no ? ',' : '');
            $no++;
        }
        ?>
    </tbody>
</table>
<br>

<input type="hidden" value="<?= $rekap['allverified'] ? 0 : 1 ?>" id="unverified">
<div class="ttd-laporan">
    <table class="ttd-tabel">
        <tr>
            <td width="50%">
                <?php if ($tingkat == 3) : ?>
                    <?php if (isset($laporan['kepala_opd'])) : ?>
                        <b>Mengesahkan <?= $laporan['kepala_opd']['jabatan_pengguna'] ?> Kepala OPD</b><br>
                        <?= $laporan['kepala_opd']['nama_personil'] ?><br>
                        NIP <?= $laporan['kepala_opd']['nipbaru'] ?><br>
                        (<?= FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date') ?>)
                    <?php else: ?>
                        <b>[Belum disahkan Kepala OPD]</b>
                    <?php endif; ?>

                <?php elseif ($tingkat > 3) : ?>
                    <?php if (isset($laporan['admin_kota'])) : ?>
                        <b>Telah diverifikasi <?= $laporan['admin_kota']['jabatan_pengguna'] ?> Admin Kota</b><br>
                        <?= $laporan['admin_kota']['nama_personil'] ?><br>
                        NIP <?= $laporan['admin_kota']['nipbaru'] ?><br>
                        (<?= FUNC::tanggal($laporan['dt_ver_admin_kota'], 'short_date') ?>)
                    <?php else: ?>
                        <b>[Belum diverifikasi Admin Kota]</b>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td width="50%">
                <?php if ($tingkat > 1) : ?>
                    <?php if (isset($laporan['admin_opd'])) : ?>
                        <b>Telah diverifikasi <?= $laporan['admin_opd']['jabatan_pengguna'] ?> Admin OPD</b><br>
                        <?= $laporan['admin_opd']['nama_personil'] ?><br>
                        NIP <?= $laporan['admin_opd']['nipbaru'] ?><br>
                        (<?= FUNC::tanggal($laporan['dt_ver_admin_opd'], 'short_date') ?>)
                    <?php else: ?>
                        <b>[Belum diverifikasi Admin OPD]</b>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php if ($tingkat > 4) : ?>
                    <?php if (isset($laporan['kepala_bkppd'])) : ?>
                        <b>Mengesahkan <?= $laporan['kepala_bkppd']['jabatan_pengguna'] ?> Kepala BKPPD</b><br>
                        <?= $laporan['kepala_bkppd']['nama_personil'] ?><br>
                        NIP <?= $laporan['kepala_bkppd']['nipbaru'] ?><br>
                        (<?= FUNC::tanggal($laporan['dt_sah_kepala_bkppd'], 'short_date') ?>)
                    <?php else: ?>
                        <b>[Belum disahkan Kepala BKPPD]</b>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($tingkat > 3 && $tingkat < 6) : ?>
                    <?php if (isset($laporan['kepala_opd'])) : ?>
                        <b>Mengesahkan <?= $laporan['kepala_opd']['jabatan_pengguna'] ?> Kepala OPD</b><br>
                        <?php $laporan['kepala_opd']['nama_personil'] ?><br>
                        NIP <?= $laporan['kepala_opd']['nipbaru'] ?><br>
                        (<?= FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date') ?>)
                    <?php else: ?>
                        <b>[Belum disahkan Kepala OPD]</b>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($tingkat == 6) : ?>
                    <?php if (isset($laporan['final'])) : ?>
                        <b>Mengesahkan <?= $laporan['final']['jabatan_pengguna'] ?> Kepala OPD</b><br>
                        <?= $laporan['kepala_opd']['nama_personil'] ?><br>
                        NIP <?= $laporan['kepala_opd']['nipbaru'] ?><br>
                        (<?= FUNC::tanggal($laporan['dt_sah_kepala_opd'], 'short_date') ?>)
                    <?php else: ?>
                        <b>[Belum disahkan Kepala OPD]</b>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>  
<?php
/*
  if ($download == 0 && $format == 'A' && $tingkat == '1')
  echo '<div class="center-align">
  <a href="'.$this->link
  ('adminopd/HusnanWModerasi/daftarVerMod/').'" class="waves-effect waves-light btn green"><i class="material-icons left">offline_pin</i> Verifikasi Moderasi</a>
  </div>';
 */

//sahkan laporan bisa dilakukan pada tanggal 4 bulan berikutnya
$batas_sahkan = strtotime($tahun . '-' . ($bulan + 1) . '-1');
if ($tingkat == 5 && !isset($laporan['kepala_bkppd']) && strtotime(date('Y-m-d')) >= $batas_sahkan
) {
    ?>
    <div class="center-align">
        <a class="waves-effect waves-light blue btn" href="<?= $this->link('kepalabkppd/HusnanWModerasi/daftarVerMod/' . $periode) ?>" style="margin-bottom: 10px; width: 250px"><i class="material-icons left">view_list</i>Verifikasi Moderasi</a>
        <form id="frmVer">
            <?= comp\MATERIALIZE::inputKey('pin_absen', $pin_absen); ?>
            <?= comp\MATERIALIZE::inputKey('bulan', $bulan); ?>
            <?= comp\MATERIALIZE::inputKey('tahun', $tahun); ?>
            <?= comp\MATERIALIZE::inputKey('jenis', $jenis); ?>
            <?= comp\MATERIALIZE::inputKey('tingkat', $tingkat); ?>
            <?= comp\MATERIALIZE::inputKey('format', $format); ?>
            <?= comp\MATERIALIZE::inputKey('kdlokasi', $kdlokasi); ?>
            <?= comp\MATERIALIZE::inputKey('catatan', $rekap['catatan']); ?>
            <?= comp\MATERIALIZE::inputKey('namabulan', $namabulan[$bulan - 1]); ?>
            <button class="btn waves-effect waves-light <?= $rekap['allverified'] ? 'orange' : 'disabled' ?> btnVer" type="button" style="width: 250px"  <?= $rekap['allverified'] ? '' : 'disabled' ?>>
                <i class="material-icons left">verified_user</i>
                Pengesahan
            </button>
        </form>
    </div>
    <?php
}
?>