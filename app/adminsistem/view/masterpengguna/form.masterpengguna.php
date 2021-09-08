<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('username_lama', $username); ?>
<?= comp\MATERIALIZE::inputKey('session_id', $session_id); ?>
<?= comp\MATERIALIZE::inputKey('user_agent', $user_agent); ?>
<?= comp\MATERIALIZE::inputKey('last_login', $last_login); ?>
<?= comp\MATERIALIZE::inputKey('status_login', $status_login); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('username', 'text', $username, 'class="cekprimary" required'); ?>
        <label for="" class="">Username</label>
        <div id="cekprimary"></div>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('grup_pengguna_kd', $pil_grup_pengguna, $grup_pengguna_kd, ' required'); ?>
        <label for="grup_pengguna_kd" class="active">Grup Pengguna</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_lokasi, $kdlokasi, ' required'); ?>
        <label for="kdlokasi" class="active">Lokasi Kerja</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputSelect('nipbaru', $pil_nipbaru, $nipbaru, ' required'); ?>
        <label for="nipbaru" class="active">Nama Admin</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('password', 'text', $password, ' required'); ?>
        <label for="password" class="">Password</label>
    </div>
    <div class="input-field col s12">
        <div class="switch m-b-md">
            <label>
                DISABLE
                <input id="status_pengguna" name="status_pengguna" type="checkbox" value="<?= $status_pengguna;?>" />
                <span class="lever"></span>
                ENABLE
            </label>
        </div>
    </div>
</div>

<script>
    (function ($) {
        "use strict";
        
        $("#kdlokasi, #nipbaru").select2({width: '100%'});
        $("select").material_select();
        
        var aksi = "<?= $op;?>";
        
        <?= ($status_pengguna=='enable') ? '$("#status_pengguna").prop("checked", true);' : '$("#status_pengguna").prop("checked", false);';?>
                
        $("#status_pengguna").change(function() {
            ($(this).is(":checked")) ? $("#status_pengguna").prop("checked", true) : $("#status_pengguna").prop("checked", false);
        });
                
        if(aksi==="input"){
            $(".cekprimary").on("focusout", function () {
                app.cekPrimary($(this).val());
            });
        }
        else{
            $("#username").attr("readonly", true);
            $(".cekprimary").on("focusout", function () {
                var id_lama = $("#username_lama").val();
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