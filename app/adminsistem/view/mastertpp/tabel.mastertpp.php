<?php
$namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">JENIS TPP</th>
            <th class="grey darken-3 white-text center-align">LABEL</th>
            <th class="grey darken-3 white-text center-align">TAHUN</th>
            <th class="grey darken-3 white-text center-align">BULAN</th>
            <th class="grey darken-3 white-text center-align">TGL</th>
            <th class="grey darken-3 white-text center-align">TINGKAT LAP. PRESENSI</th>
            <th class="grey darken-3 white-text center-align">POT. TPP</th>
            <th class="grey darken-3 white-text center-align">TAMPILKAN</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php                    
            foreach ($dataTabel as $kol) {
                ?>
                <tr style="cursor: pointer;">
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><?= $no ?></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><b style="color: #f44286; font-size: 16px"><?= $kol['jenis_tpp']; ?></b></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><b><?= $kol['label'] ?></b></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><b><?= $kol['tahun'] ?></b></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><b><?= $namabulan[$kol['bulan'] - 1] ?></b></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><b><?= $kol['tgl_awal'] . ' - ' . $kol['tgl_akhir']; ?></b></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><b><?= ($kol['tingkat'] == 6 ? 'Final' : $kol['tingkat']) ?></b></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><?= $kol['potongan'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align"><?= $kol['tampil'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                    <td id="<?= $kol['kd_tpp']; ?>" class="center-align">
                        <i id="<?= $kol['kd_tpp']; ?>" class="small material-icons green-text modal-trigger btnNot" data-target="frmInputModal">report_off</i>
                        <i id="<?= $kol['kd_tpp']; ?>" class="small material-icons orange-text modal-trigger btnForm" data-target="frmInputModal">loop</i>
                        <i id="<?= $kol['kd_tpp']; ?>" nama="<?= $kol['jenis_tpp'].'-'.$kol['tahun'] ?>" class="small material-icons red-text btnHapus">delete</i>
                    </td>
                </tr>
                <?php
                $no++;
            }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::Pagging($page, $batas, $jmlData) ?>
