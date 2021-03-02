<?php
extract($moderasi);
// comp\FUNC::showPre($data);
?>

<style>
    #modalDetail td, #modalDetail th {
        padding: 5px !important;
    }
</style>

<div class="row" style="font-size: 0.8em">
    <div id="detail-info" class="col s12 m12 l6">
        <table class="highlight bordered striped" width="100%">
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td><?= $pegawai['nipbaru'] ?></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><?= $pegawai['nama_personil'] ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?= comp\FUNC::mergeDate($tanggal_awal, $tanggal_akhir) ?></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>:</td>
                <td><?= $jenisMod['nama_jenis'] ?></td>
            </tr>
            <tr>
                <td>Jenis Moderasi</td>
                <td>:</td>
                <td><?= $kode_presensi ?></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td><?= $keterangan ?></td>
            </tr>
        </table>
        <br>

        <table class="bordered">
            <thead>
                <tr class="grey darken-3 white-text center-align">
                    <th>Adm OPD</th>
                    <th>Kep OPD</th>
                    <th>Adm Kota</th>
                    <th>Kep BKPPD</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= comp\FUNC::modSymbol($flag_operator_opd, $catatan_operator_opd) ?></td>
                    <td><?= comp\FUNC::modSymbol($flag_kepala_opd, $catatan_kepala_opd) ?></td>
                    <td><?= comp\FUNC::modSymbol($flag_operator_kota, $catatan_operator_kota) ?></td>
                    <td><?= comp\FUNC::modSymbol($flag_kepala_kota, $catatan_kepala_kota) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
<?php
if (!is_null($flag_kepala_opd) || $lockMod == true) {
    ?>
        $("#modalDetail .btnEdit").hide();
        $("#modalDetail .btnHapus").hide();
        $("#modalDetail .btnClose").show();
    <?php
} else {
    ?>
        $("#modalDetail .btnEdit").show();
        $("#modalDetail .btnHapus").show();
        $("#modalDetail .btnClose").hide();
    <?php
}
?>
</script>