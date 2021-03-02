<?php
extract($moderasi);

function tanggal($date = '') {
    $limit = strtotime('2017-01-01');
    $str_date = strtotime($date);
    $viewDate = !empty($date) && $str_date > $limit ? comp\FUNC::tanggal($date, 'long_date') : '-';
    return $viewDate;
}

//comp\FUNC::showPre($data);
?>
<style>
    #modalDetail td, #modalDetail th {
        padding: 5px !important;
    }
    .modal {
        width: 60%;
    }
    ul li {
        display: block;
        padding: 3px 0px;
    }
    ul li:hover {
        display: block;
        background-color: #bbdefb ;
    }
</style>

<div class="row">
    <div id="detail-info" class="col s12">
        <table class="highlight bordered stripped" width="100%">
            <tr>
                <td width="160px">NIP</td>
                <td width="5px">:</td>
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
                <td><?= $kode_presensi . ' (' . $kodeMod['ket_kode_presensi'] . ')' ?></td>
            </tr>
            <tr>
                <td>Dokumen Pendukung</td>
                <td>:</td>
                <td>
                    <?php //comp\FUNC::showPre($lampiran) ?>
                    <ul class="no-s">
                        <?php $numb_doc = 1; ?>
                        <?php foreach ($lampiran as $val): ?>
                            <li class="listLampiran<?= $val['id'] ?>">
                                <a href="<?= $this->link('upload/moderasi/dokumen/' . $val['filename']) ?>" target="blank">Unduh dokumen <?= $numb_doc++ ?></a>
                                <span class="badge">
                                    <a href="javascript:void(0)" id="<?= $val['id'] ?>" class="btnDelLamp">
                                        <i class="material-icons red-text">close</i>
                                    </a>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Potongan</td>
                <td>:</td>
                <td><?= $kodeMod['pot_kode_presensi'] * 100 ?> %</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td><?= $keterangan ?></td>
            </tr>
        </table>
        <br>
        <table class="bordered center-align stripped">
            <thead>
                <tr class="grey darken-3 white-text">
                    <th></th>
                    <th class="center-align" width="20%">Adm OPD</th>
                    <th class="center-align" width="20%">Kep OPD</th>
                    <th class="center-align" width="20%">Adm Kota</th>
                    <th class="center-align" width="20%">Kep BKPPD</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="grey darken-1 white-text">Status</td>
                    <td class="center-align"><?= comp\FUNC::modSymbol($flag_operator_opd) ?></td>
                    <td class="center-align"><?= comp\FUNC::modSymbol($flag_kepala_opd) ?></td>
                    <td class="center-align"><?= comp\FUNC::modSymbol($flag_operator_kota) ?></td>
                    <td class="center-align"><?= comp\FUNC::modSymbol($flag_kepala_kota) ?></td>
                </tr>
                <tr>
                    <td class="grey darken-1 white-text">Tgl Verif.</td>
                    <td class="center-align"><?= tanggal($dt_flag_operator_opd) ?></td>
                    <td class="center-align"><?= tanggal($dt_flag_kepala_opd) ?></td>
                    <td class="center-align"><?= tanggal($dt_flag_operator_kota) ?></td>
                    <td class="center-align"><?= tanggal($dt_flag_kepala_kota) ?></td>
                </tr>
                <tr>
                    <td class="grey darken-1 white-text">Catatan</td>
                    <td class="hoverable"><?= $catatan_operator_opd ?></td>
                    <td class="hoverable"><?= $catatan_kepala_opd . $flag_kepala_opd ?></td>
                    <td class="hoverable"><?= $catatan_operator_kota ?></td>
                    <td class="hoverable"><?= $catatan_kepala_kota ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="detail-form" class="col s12" style="display: none">
        <?= comp\MATERIALIZE::inputKey('flag_operator_opd', '') ?>
        <?= comp\MATERIALIZE::inputKey('id', $id) ?>
        <div class="input-field">
            <label for="catatanAdmOPD" class="active">Catatan admin OPD</label>
            <textarea id="catatanAdmOPD" name="catatan_operator_opd" class="materialize-textarea"><?= $catatan_operator_opd ?></textarea>
        </div>
    </div>
</div>

<script>
<?php
if (!is_null($flag_kepala_opd) || $lockMod == true) {
    ?>
        $("#modalDetail .btnEdit").hide();
        $("#modalDetail .btnHapus").hide();
    <?php
} else {
    ?>
        $("#modalDetail .btnEdit").show();
        $("#modalDetail .btnHapus").show();
    <?php
}
?>

    $(document).on("click", ".btnDelLamp", function () {
        var varTitle = 'PERHATIAN!';
        var varMessage = 'Anda yakin akan menghapus dokumen tersebut?';
        app.showConfirmDelDok(this.id, varTitle, varMessage);
    });
</script>