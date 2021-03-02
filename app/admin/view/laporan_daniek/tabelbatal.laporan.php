<?php 
extract($dataTabel);
$namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align" rowspan="2">No</th>
            <th class="center-align" rowspan="2">OPD</th>
            <th class="center-align" colspan="5">Verifikasi</th>
            <th class="center-align" rowspan="2">Aksi</th>
        </tr>
        <tr>
        	<th>Admin OPD</th>
        	<th>Kepala OPD</th>
        	<th>Admin Kota</th>
        	<th>Kepala BKPPD</th>
        	<th>Final</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($dataTabel as $kol) {
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td class="center-align"><?= $satker[$kol['kdlokasi']] ?></td>
                <td class="center-align"><?= $kol['ver_admin_opd'] ? '<i class="material-icons green-text md-24">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                <td class="center-align"><?= $kol['sah_kepala_opd'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                <td class="center-align"><?= $kol['ver_admin_kota'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                <td class="center-align"><?= $kol['sah_kepala_bkppd'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                <td class="center-align"><?= $kol['sah_final'] ? '<i class="material-icons green-text">check_circle</i>' : '<i class="material-icons red-text">cancel</i>' ?></td>
                <td class="right-align">
                    <button class="waves-effect waves-light btn blue xs btnBatal" data-satker="<?= $satker[$kol['kdlokasi']] ?>" data-kdlokasi="<?= $kol['kdlokasi'] ?>" data-bulan="<?= $bulan ?>" data-namabulan="<?= $namabulan[$bulan - 1] ?>" data-tahun="<?= $tahun ?>"><i class="material-icons left">history</i>batalkan</button>
                </td>
            </tr>
            <?php } ?>
    </tbody>
</table>