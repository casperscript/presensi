<?php
//comp\FUNC::showPre($data);
extract($dataTabel);
use app\admin\model\apelpagi_service;
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Jam Apel</th>
            <th>Jam Finger Apel</th>
            <th>Status Apel</th>
            <th width="150">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            $jadwal_apel = $default;
            $tgl = $kol['tanggal_apel'];
            if (isset($jadwal[$tgl])) {
                $jadwal_apel = $jadwal[$tgl]['awal'] . ' - ' . $jadwal[$tgl]['akhir'];
            }
        ?>
        <tr>
            <td class="center-align"><?= $no++ ?></td>
            <td><?= comp\FUNC::tanggal($kol['tanggal_apel'], 'long_date') ?></td>
            <td><?= comp\FUNC::tanggal($kol['tanggal_apel'], 'day') ?></td>
            <td><?= $jadwal_apel ?></td>
            <td><?= $kol['jam_apel'] ?></td>
            <td><?= $kol['status_apel'] ? '<span class="badge custom-badge green">APEL</span>' : '<span class="badge custom-badge red">TIDAK APEL</span>' ?></td>
            <td>
                <?php
                    $compare = $this->apelpagi_service->compare($kol['tanggal_apel'], $kol['jam_apel'], $jadwal);

                    if ($compare == 1 && $kol['status_apel'] == 0) {
                        $get = $this->apelpagi_service->getData('SELECT * FROM tb_batal_apel WHERE (pin_absen = ? AND tanggal_apel = ?)', array($kol['pin_absen'], $kol['tanggal_apel']));

                        if ($get['count'] > 0) 
                            foreach ($get['value'] as $i)
                                if ($i['status_batal'] == 1) {
                                    echo '<span class="badge custom-badge light-blue accent-3">PEMBATALAN APEL</span>';
                                    break;
                                }
                    }
                ?>
            </td>
        </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php echo comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>
