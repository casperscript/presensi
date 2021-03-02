<?php 
extract($dataTabel);
$namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
?>
<div class="row lap">
    <div class="format-lap">
        Format C<?= $jenis ?> <?= ' - ' . ($tingkat == 6 ? 'Final' : $tingkat) ?>
    </div>
</div>
<h5 class="center-align"><b>
<?php
if ($jenis == 1)
    echo 'Rekap Kehadiran/Ketidakhadiran Masuk Kerja, Apel Pagi dan Pulang Kerja';
else
    echo 'Rekap Bulanan Daftar Kehadiran Individu/Pegawai';
?>
</b></h5>
<?= comp\MATERIALIZE::inputKey('bulan', $bulan); ?>
<?= comp\MATERIALIZE::inputKey('tahun', $tahun); ?>
<?= comp\MATERIALIZE::inputKey('jns', $jenis); ?>
<?= comp\MATERIALIZE::inputKey('tk', $tingkat); ?>
<?= comp\MATERIALIZE::inputKey('frmt', $format); ?>
<div class="row right" style="margin-bottom: 0">
    <div class="input-field col s9">
        <i class="material-icons prefix">search</i>
        <?= comp\MATERIALIZE::inputText('cari', 'text', $cari, 'onchange="app.loadTabel()"') ?>
        <label class="<?= !empty($cari) ? 'active' : '' ?>" for="cari">Cari nama personil</label>
    </div>
    <div class="input-field col s3"">
        <label for="batas" class="active">Tampilkan</label>
        <?= comp\MATERIALIZE::inputSelect('batas', array(10 => 10, 25 => 25, 50 => 50, 100 => 100, 1000 => 'Semua'), $batas) ?>
    </div>
</div>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>
                <span class="p-v-xs">
                    <input type="checkbox" id="checkAll" class="filled-in">
                    <label for="checkAll"></label>
                </span>
            </th>
            <th>NIP/NIK</th>
            <th>Nama</th>
            <th class="center-align">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td>
                    <?= comp\MATERIALIZE::inputCheckbox('pin_absen[' . $kol['pin_absen'] . ']', array($kol['pin_absen']), '', 'class="check-cetak"') ?>
                </td>
                <td><?= $kol['nipbaru'] ?></td>
                <td><?= $kol['nama_personil'] ?></td>
                <td class="center-align">
                    <a id="<?= $kol['pin_absen'] ?>" class="btn-floating waves-effect waves-light blue-grey btn btnDetail">
                        <i class="material-icons">info_outline</i>
                    </a>
                </td>
            </tr>
            <?php } ?>
    </tbody>
</table>
<!--pagging($aktif, $batas, $jml_data)-->
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>

<script>
    $(function() {
        $('.check-cetak').on('change', function() {
            var checked = false;
            $('.check-cetak').each(function() {
                if ($(this).prop('checked')) {
                    checked = true;
                    $('#btnCetak').attr('disabled', false);
                }
            });

            if (!checked)
                $('#btnCetak').attr('disabled', true);
        }).change();

        $('#checkAll').change(function () {
            $('.check-cetak').prop('checked', $(this).prop('checked'));
            $('.check-cetak').trigger('change');
        });
    });
</script>