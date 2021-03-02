<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <?= comp\MATERIALIZE::inputKey('kd_teks', $kd_teks); ?>
    <div class="input-field col s12" style="margin-bottom: 10px">
        <label for="" class="active">Isi Teks</label><br>
        <?= comp\MATERIALIZE::inputTextArea('isi_teks', $isi_teks, 'style="width: 50vw; height: 100px; margin-top: 10px" '); ?> 
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('bg_color', $pil_warna, $bg_color, ' required'); ?>
        <label for="" class="">Warna Latar</label>
    </div>
    <div class="col s6">
        <div class="input-field" style="padding-bottom: 5px">
            <?= comp\MATERIALIZE::inputSelect('lokasi', $pil_lokasi, $lokasi, ' required'); ?>
            <label for="" class="">Lokasi</label>
        </div>
        <div class="input-field">
            <?= comp\MATERIALIZE::inputSelect('bentuk', $pil_bentuk, $bentuk, ' required'); ?>
            <label for="" class="">Bentuk</label>
        </div>
    </div>
    <div class="input-field col s6">
        <span class="p-v-xs">
            <input class="lokasi_user" type="checkbox" name="pns" id="pns" value="1" <?= ($pns ? 'checked' : '') ?>>
            <label for="pns">PNS</label>
        </span><br>
        <span class="p-v-xs">
            <input class="lokasi_user" type="checkbox" name="admin_opd" id="admin_opd" value="1" <?= ($admin_opd ? 'checked' : '') ?>>
            <label for="admin_opd">Admin OPD</label>
        </span><br>
        <span class="p-v-xs">
            <input class="lokasi_user" type="checkbox" name="kepala_opd" id="kepala_opd" value="1" <?= ($kepala_opd ? 'checked' : '') ?>>
            <label for="kepala_opd">Kepala OPD</label>
        </span><br>
        <span class="p-v-xs">
            <input class="lokasi_user" type="checkbox" name="admin" id="admin" value="1" <?= ($admin ? 'checked' : '') ?> >
            <label for="admin">Admin BKPPD</label>
        </span><br>
        <span class="p-v-xs">
            <input class="lokasi_user" type="checkbox" name="kepala_bkppd" id="kepala_bkppd" value="1" <?= ($kepala_bkppd ? 'checked' : '') ?>>
            <label for="kepala_bkppd">Kepala BKPPD</label>
        </span><br>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('tampil', [0 => 'TIDAK', 1 => 'YA'], $tampil, ' required'); ?>
        <label for="" class="">Tampilkan</label>
    </div>
    <div class="input-field col s12">
        <div id="resultForm"></div>
    </div>
</div>

<script>
    $(function() {
        $('#lokasi').on('change', function() {
            var lokasi =  $(this).val();
            if (lokasi == 'LOGIN') {
                $('.lokasi_user').attr('checked', true);
                $('.lokasi_user').attr('onclick', "return false;");
            } else {
                $('.lokasi_user').removeAttr('onclick');
            }
        }).change();

        CKEDITOR.replace('isi_teks');
    });
</script>