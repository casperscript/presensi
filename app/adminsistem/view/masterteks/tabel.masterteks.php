<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">ISI TEKS</th>
            <th class="grey darken-3 white-text center-align">WARNA LATAR - BENTUK</th>
            <th class="grey darken-3 white-text center-align">LOKASI</th>
            <th class="grey darken-3 white-text center-align">TAMPILKAN</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_teks']; ?>" class="center-align"><?= $no ?></td>
                    <td id="<?= $kol['kd_teks']; ?>"><b><?= $kol['isi_teks'] ?></b></td>
                    <td id="<?= $kol['kd_teks']; ?>" class="center-align <?= $kol['bg_color'] ?>"><?= $kol['bentuk'] ?></td>
                    <td id="<?= $kol['kd_teks']; ?>" class="center-align">
                        <b>
                            <?= $pil_lokasi[$kol['lokasi']] ?><br>
                            <?php if ($kol['lokasi'] == 'BERANDA') { ?>
                                <?= $kol['pns'] ? '<i>PNS</i> -' : '' ?>
                                <?= $kol['admin_opd'] ? '<i>Admin OPD</i> -' : '' ?>
                                <?= $kol['kepala_opd'] ? '<i>Kepala OPD</i> -' : '' ?>
                                <?= $kol['admin'] ? '<i>Admin BKPPD</i> -' : '' ?>
                                <?= $kol['kepala_bkppd'] ? '<i>Kepala BKPPD</i>' : '' ?>
                            <?PHP } ?>
                        </b>
                    </td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><?= $kol['tampil'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                    <td id="<?= $kol['kd_teks']; ?>" class="center-align">
                        <i id="<?= $kol['kd_teks']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['kd_teks']; ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
