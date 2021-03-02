<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_kode_presensi', $id_kode_presensi); ?>
<?= comp\MATERIALIZE::inputKey('kode_presensi_lama', $kode_presensi); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kode_presensi', 'text', $kode_presensi, 'class="cekprimary" required'); ?>
        <label for="" class="">Kode Presensi</label>
        <div id="cekprimary"></div>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('ket_kode_presensi', 'text', $ket_kode_presensi, ' required'); ?>
        <label for="" class="">Keterangan</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('pot_kode_presensi', 'text', $pot_kode_presensi, ' required'); ?>
        <label for="" class="">Potongan TPP</label>
    </div>
    <div class="input-field col s12">
        <div class="switch m-b-md">
            <label>
                MODERASI OFF
                <input id="moderasi_kode_presensi" name="moderasi_kode_presensi" type="checkbox" value="<?= $moderasi_kode_presensi;?>" />
                <span class="lever"></span>
                MODERASI ON
            </label>
        </div>
    </div>
</div>

<script>
    (function ($) {
        "use strict";
        
        var aksi = "<?= $op;?>";
        
        <?= ($moderasi_kode_presensi==1) ? '$("#moderasi_kode_presensi").prop("checked", true);' : '$("#moderasi_kode_presensi").prop("checked", false);';?>
                
        $("#moderasi_kode_presensi").change(function() {
            ($(this).is(":checked")) ? $("#moderasi_kode_presensi").prop("checked", true) : $("#moderasi_kode_presensi").prop("checked", false);
        });
                        
        if(aksi==="input"){
            $(".cekprimary").on("focusout", function () {
                app.cekPrimary($(this).val());
            });
        }
        else{
            $(".cekprimary").on("focusout", function () {
                var id_lama = $("#kode_presensi_lama").val();
                var id_baru = $(".cekprimary").val();
                if(id_lama!==id_baru){
                    app.cekPrimary($(".cekprimary").val());
                }
                else{
                    $("#cekprimary").html('');
                }
            });
        }
        
        Materialize.updateTextFields();
        
    })(jQuery);
</script>