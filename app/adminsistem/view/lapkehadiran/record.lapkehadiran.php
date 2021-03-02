<?php
//comp\FUNC::showPre($data);
extract($dataTabel);
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Nama Shift</th>
            <th>Shift Masuk</th>
            <th>Shift Pulang</th>
            <th>Jam Perekaman</th>
            <th>Status</th>
            <th class="center-align">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            ?>
        <tr>
            <td class="center-align"><?= $no++ ?></td>
            <td><?= comp\FUNC::tanggal($kol['tanggal_log_presensi'], 'long_date') ?></td>
            <td><?= comp\FUNC::tanggal($kol['tanggal_log_presensi'], 'day') ?></td>
            <td> - </td>
            <td> - </td>
            <td> - </td>
            <td><?= $kol['jam_log_presensi'] ?></td>
            <td></td>
            <td class="center-align"></td>
        </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php echo comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>
