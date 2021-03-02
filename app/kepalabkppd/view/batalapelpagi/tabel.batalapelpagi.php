<table class="bordered striped hoverable">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">PIN ABSEN</th>
            <th class="grey darken-3 white-text center-align">NAMA</th>
            <th class="grey darken-3 white-text center-align">TANGGAL APEL</th>
            <th class="grey darken-3 white-text center-align">ALASAN PEMBATALAN</th>
            <th class="grey darken-3 white-text center-align">PETUGAS INPUT</th>
            <th class="grey darken-3 white-text center-align">STATUS</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td class="center-align btnDetail"><?= $no; ?></td>
                    <td class="center-align btnDetail"><?= $kol['pin_absen']; ?></td>
                    <td>
                        <?= isset($personil[$kol['pin_absen']]) ? $personil[$kol['pin_absen']]['nama'] : '' ?>
                    </td>
                    <td class="center-align btnDetail"><?= $kol['tanggal_apel']; ?></td>
                    <td class="center-align btnDetail"><?= $kol['keterangan']; ?></td>
                    <td class="center-align btnDetail"><?= $kol['petugas_input']; ?></td>
                    <td class="center-align btnDetail">
                        <?php
                            if ($kol['status_batal'] == 1)
                                echo '<span class="badge custom-badge green">DITERIMA</span>';
                            elseif ($kol['status_batal'] == 2)
                                echo '<span class="badge custom-badge red">DITOLAK</span>';
                        ?>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
