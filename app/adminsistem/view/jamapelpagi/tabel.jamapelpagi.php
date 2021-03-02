<table class="bordered striped hoverable">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">NAMA</th>
            <th class="grey darken-3 white-text center-align">JAM APEL</th>
            <th class="grey darken-3 white-text center-align">TANGGAL MULAI</th>
            <th class="grey darken-3 white-text center-align">TANGGAL AKHIR</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td class="center-align btnDetail"><?= $no; ?></td>
                    <td class="center-align btnDetail"><?= $kol['nama_jam_apel']; ?></td>
                    <td class="center-align btnDetail"><?= $kol['mulai_apel'] . ' - ' . $kol['akhir_apel'] ?></td>
                    <td class="center-align btnDetail"><?= $kol['tanggal_mulai'] == '0000-00-00' ? '-' : comp\FUNC::tanggal($kol['tanggal_mulai'], 'long_date'); ?></td>
                    <td class="center-align btnDetail"><?= $kol['tanggal_akhir'] == '0000-00-00' ? '-' : comp\FUNC::tanggal($kol['tanggal_akhir'], 'long_date'); ?></td>
                    <td class="center-align btnDetail">
                        <?php 
                        if (!$kol['is_default']) { ?>
                            <i id="<?= $kol['id_jam_apel']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                            <i id="<?= $kol['id_jam_apel']; ?>" data-mulai="<?= $kol['tanggal_mulai'] ?>" data-akhir="<?= $kol['tanggal_akhir'] ?>" nama="<?= $kol['nama_jam_apel']; ?>" class="small material-icons red-text btnHapus">delete</i>
                        <?php 
                        } else
                            echo "<b>DEFAULT</b>";
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
