<?php
if ($this->login['username'] == 'acilCaspers1') {
//    comp\FUNC::showPre($dataTabel);
}
extract($dataTabel);
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Nama Shift</th>
            <th class="center">Jam Kerja</th>
            <th class="center">Scan Masuk</th>
            <th class="center">Scan Pulang</th>
            <th class="center">Id Scan</th>
            <th class="center">Jam Perekaman</th>
            <th class="center">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            $tgl = $kol['tanggal_log_presensi'];
            $pin = $kol['pin_absen'];
            $jam = $kol['jam_log_presensi'];
            $status = $kol['status_log_presensi'];
            $status_verified = $kol['status_log_verified'];
            $log_masuk = (!empty($dataLaporan[$pin][$tgl]['masuk'])) ? $dataLaporan[$pin][$tgl]['masuk'] : '';
            $log_pulang = (!empty($dataLaporan[$pin][$tgl]['pulang'])) ? $dataLaporan[$pin][$tgl]['pulang'] : '';
            if ($log_masuk == $jam) {
                $stat = '<span class="chip white-text blue">Masuk</span>';
            } else if ($log_pulang == $jam) {
                $stat = '<span class="chip white-text red">Pulang</span>';
            } else if ($status == '2') {
                $stat = '<span class="chip white-text orange darken-3">Apel</span>';
            } else {
                $stat = '-';
            }
            $statLog['class'] = ($status != $status_verified) ? 'brown darken-4 white-text' : 'brown lighten-4';
            $statLog['idScan'] = ($status != $status_verified) ? $status_verified . '<i class="tiny material-icons">forward</i>' . $status : $status;
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td><?= comp\FUNC::tanggal($kol['tanggal_log_presensi'], 'long_date') ?></td>
                <td><?= comp\FUNC::tanggal($kol['tanggal_log_presensi'], 'day') ?></td>
                <td><?= (!empty($kol['id_shift'])) ? $kol['nama_shift'] : '-' ?></td>
                <td class="center"><?= (!empty($kol['id_shift'])) ? date('H:i', strtotime($kol['masuk'])) . ' - ' . date('H:i', strtotime($kol['pulang'])) : '-' ?></td>
                <td class="center"><?= (!empty($kol['id_shift'])) ? date('H:i', strtotime($kol['mulai_masuk'])) . ' - ' . date('H:i', strtotime($kol['akhir_masuk'])) : '-' ?></td>
                <td class="center"><?= (!empty($kol['id_shift'])) ? date('H:i', strtotime($kol['mulai_pulang'])) . ' - ' . date('H:i', strtotime($kol['akhir_pulang'])) : '-' ?></td>
                <td class="center">
                    <!--<div class="chip <?= $statLog['class'] ?>"><?= $statLog['idScan'] ?></div>-->
                    <?php
                    if ($status != $status_verified) {
                        ?>
                        <a onclick="app.showForm('<?= $pin ?>', '<?= $tgl ?>', '<?= $jam ?>', '<?= $status ?>')" href="javascript:void(0)" class="chip hoverable orange white-text">
                            <?= $status_verified ?>
                            <i class="tiny material-icons">forward</i>
                            <?= $status ?>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a onclick="app.showForm('<?= $pin ?>', '<?= $tgl ?>', '<?= $jam ?>', '<?= $status ?>')" href="javascript:void(0)" class="chip hoverable green white-text">
                            <?= $status_verified ?>
                        </a>
                        <?php
                    }
                    ?>
                </td>
                <td class="green-text center"><?= $kol['jam_log_presensi'] ?></td>
                <td class="center"><?= $stat ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php echo comp\MATERIALIZE::pagging2($page, $batas, $jmlData); ?>