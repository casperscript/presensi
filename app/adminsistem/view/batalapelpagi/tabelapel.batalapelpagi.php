<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Jam Apel</th>
            <th>Jam Finger Apel</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
        ?>
            <tr>
                <td><?= comp\FUNC::tanggal($kol['tanggal_log_presensi'], 'long_date') ?></td>
                <td><?= comp\FUNC::tanggal($kol['tanggal_log_presensi'], 'day') ?></td>
				<td><?= $jam_apel ?></td>
                <td><?= $kol['jam_log_presensi'] ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>