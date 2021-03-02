<?php
extract($moderasi);
echo comp\MATERIALIZE::inputKey('id', $id);
//comp\FUNC::showPre($kodeMod);
?>
<div class="card-content no-s">

    <div class="row no-s">
        <div class="input-field col s6">
            <label for="kd_jenis" class="active">Kategori</label>
            <?php
            if ($id > 0) {
                echo comp\MATERIALIZE::inputSelect('kd_jenis[]', $jenisMod, $kd_jenis, 'class="selKategoriModerasi"');
            } else {
                ?>
                <select class="selKategoriModerasi" name="kd_jenis[]" cursor="pointer" multiple>
                    <option value="" disabled=""> Pilih jenis moderasi</option>
                    <?php foreach ($jenisMod as $key => $val): ?>
                        <?php $selected = ($key === $kd_jenis) ? 'selected' : '' ?>
                        <?php $kategori = ($key === "JNSMOD04") ? "semua" : "individual" ?>
                        <option value="<?= $key ?>" kategori="<?= $kategori ?>" <?= $selected ?>><?= $val ?></option>
                    <?php endforeach; ?>
                </select>
            <?php } ?>
        </div>
        <div class="input-field col s6">
            <label for="kode_presensi" class="active">Jenis Moderasi</label>
            <?php
            if ($id > 0) {
                echo comp\MATERIALIZE::inputSelect('kode_presensi', $kodeMod, $kode_presensi, 'class="browser-default"');
            } else {
                ?>
                <select id="kode_presensi" name="kode_presensi" class="browser-default">
                    <option value="" disabled selected>Aktif setelah pilih kategori moderasi..</option>
                </select>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="row no-s">
        <div class="input-field col s12 m6 tglAwal">
            <i class="fa fa-calendar prefix"></i>
            <label for="tanggal_awal" class="active">Tanggal Awal</label>
            <?= comp\MATERIALIZE::inputText('tanggal_awal', 'date', $tanggal_awal, 'required') ?>
        </div>
        <div class="input-field col s12 m6 tglAkhir">
            <i class="fa fa-calendar prefix"></i>
            <label for="tanggal_akhir" class="active">Tanggal Akhir</label>
            <?= comp\MATERIALIZE::inputText('tanggal_akhir', 'date', $tanggal_akhir, 'required') ?>
        </div>
    </div>

    <div class="file-field input-field">
        <div class="btn">
            <span>Lampiran</span>
            <?= comp\MATERIALIZE::inputText('lampiran', 'file', '') ?>
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Upload berkas lampiran moderasi">
        </div>
    </div>

    <div class="input-field col s12">
        <label for="keterangan">Keterangan pengajuan moderasi</label>
        <?= comp\MATERIALIZE::inputTextArea('keterangan', $keterangan, 'class="materialize-textarea validate" required') ?>
    </div>

</div>

<script>
    var kodeKatMod = $(".selKategoriModerasi").val();
    var idMod = $("#id").val();
    var modalHeader = "<?= empty($id) ? 'Input Moderasi' : 'Edit Moderasi' ?>";

    $("#modalInput #modalHeader").text(modalHeader);
    $(".selKategoriModerasi").not(".disabled").material_select();
    $("#kode_presensi").not(".disabled").material_select();

    if (idMod > 0) {
        $("select").material_select();
    }

    $("#tanggal_awal").pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 4, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });
    $("#tanggal_akhir").pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 4, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        container: 'body'
    });

</script>