<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('kd_jenis_dinas_lama', $kd_jenis_dinas); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('kd_jenis_dinas', 'text', $kd_jenis_dinas, 'class="cekprimary"'); ?>
        <label for="" class="">Kode Jenis Dinas</label>
        <div id="cekprimary"></div>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_jenis_dinas', 'text', $nama_jenis_dinas, ''); ?>
        <label for="" class="">Jenis Dinas</label>
    </div>
    <div class="input-field col s12">
        <div id="resultForm"></div>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        
        var aksi = "<?= $op;?>";
        
        if(aksi==="input"){
            $(".cekprimary").on("focusout", function () {
                app.cekPrimary($(this).val());
            });
        }
        else{
            Materialize.updateTextFields();
            $(".cekprimary").on("focusout", function () {
                var id_lama = $("#kd_jenis_dinas_lama").val();
                var id_baru = $(".cekprimary").val();
                if(id_lama!==id_baru){
                    app.cekPrimary($(".cekprimary").val());
                }
                else{
                    $("#cekprimary").html('');
                }
            });
        }
        
    })(jQuery);
</script>