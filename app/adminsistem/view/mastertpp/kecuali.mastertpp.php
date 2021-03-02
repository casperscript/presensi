<?php
$namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tpp = $dataTpp[0];
?>
<div class="title-page">
    <h5 class="center-align">[<?= $tpp['jenis_tpp'] ?>] <b><?= $tpp['label'] ?></b> </h5>
    <p class="center-align">Bulan: <?= $namabulan[$tpp['bulan'] - 1] ?> Tahun: <?= $tpp['tahun'] ?></p>
</div>
<br>
<button class="btn pink btnFormKecuali" style="width: 100%" id="<?= $tpp['kd_tpp'] ?>">Tambah Pegawai yang Dikecualikan</button>
<table class="bordered striped hoverable table-list">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">NO</th>
            <th class="grey darken-3 white-text center-align">NIP BARU</th>
            <th class="grey darken-3 white-text center-align">NAMA</th>
            <th class="grey darken-3 white-text center-align">OPD</th>
            <th class="grey darken-3 white-text center-align">ALASAN</th>
            <th class="grey darken-3 white-text center-align">AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if ($dataKecuali['count'] == 0) {
            echo '<tr> 
                <td class="center-align" colspan="6">--- Data Kosong ---</td>
            </tr>';
        }

        $no = 1;
        foreach ($dataKecuali['value'] as $kol) { 
            $get = (isset($pegawai[$kol['nipbaru']]) ? $pegawai[$kol['nipbaru']] : []);
        ?>
            <tr class="<?= ($no%2 == 0 ? 'red lighten-4':'deep-purple lighten-5') ?>" style="cursor: pointer;">
                <td class="center-align"><?= $no ?></td>
                <td><?= $kol['nipbaru'] ?></td>
                <td><?= (isset($get['nama_personil']) ? $get['nama_personil'] : '{NAMA}') ?></td>
                <td><?= (isset($get['singkatan_lokasi']) ? $get['singkatan_lokasi'] : '{OPD}') ?></td>
                <td><?= $kol['alasan'] ?></td>
                <td class="center-align">
                     <i id="<?= $kol['kd_tpp_not']; ?>" data-tpp="<?= $kol['kd_tpp'] ?>" class="small material-icons red-text btnHapusNot">delete</i>
                </td>
            </tr>
        <?php
            $no++;
        } ?>
    </tbody>
</table>