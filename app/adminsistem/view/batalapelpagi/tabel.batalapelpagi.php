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
            <th class="grey darken-3 white-text center-align">AKSI</th>
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
                    <td class="center-align btnDetail">
                        <?php
                        if ($kol['status_batal'] == 0) {
                            echo '<a id="'.$kol['id_batal_apel'].'" class="btn-terima btn-floating waves-effect waves-light green" mid="4" data-nama="'.(isset($personil[$kol['pin_absen']]) ? $personil[$kol['pin_absen']]['nama'] : '').'" data-tanggal="'.$kol['tanggal_apel'].'" data-pin="'.$kol['pin_absen'].'"><i class="material-icons">check</i></a>';
                            echo '<a id="'.$kol['id_batal_apel'].'" class="btn-tolak btn-floating waves-effect waves-light red" mid="4" data-nama="'.(isset($personil[$kol['pin_absen']]) ? $personil[$kol['pin_absen']]['nama'] : '').'" data-tanggal="'.$kol['tanggal_apel'].'" data-pin="'.$kol['pin_absen'].'"><i class="material-icons">close</i></a>';
                            echo '<a id="'.$kol['id_batal_apel'].'" class="btn-hapus btn-floating waves-effect waves-light grey" mid="4" data-nama="'.(isset($personil[$kol['pin_absen']]) ? $personil[$kol['pin_absen']]['nama'] : '').'" data-tanggal="'.$kol['tanggal_apel'].'"><i class="material-icons">delete</i></a>';
                        }
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
