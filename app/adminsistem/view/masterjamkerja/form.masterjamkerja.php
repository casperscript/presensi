<?php extract($dataTabel);?>
<?= comp\MATERIALIZE::inputKey('id_jam_kerja', $id_jam_kerja); ?>
<?= comp\MATERIALIZE::inputKey('author', $author); ?>
<?= comp\MATERIALIZE::inputKey('ip', $ip); ?>
<h4><?= $form_title ?></h4>
<div class="row">
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_jam_kerja', 'text', $nama_jam_kerja, 'required'); ?>
        <label for="" class="">Jam Kerja</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('jam_masuk', 'text', $jam_masuk, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s&apos;" required'); ?>
        <label for="" class="">Jam Masuk</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('jam_pulang', 'text', $jam_pulang, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s&apos;" required'); ?>
        <label for="" class="">Jam Pulang</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('mulai_masuk', 'text', $mulai_masuk, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s&apos;" required'); ?>
        <label for="" class="">Mulai Masuk</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('akhir_masuk', 'text', $akhir_masuk, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s&apos;" required'); ?>
        <label for="" class="">Akhir Masuk</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('mulai_pulang', 'text', $mulai_pulang, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s&apos;" required'); ?>
        <label for="" class="">Mulai Pulang</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('akhir_pulang', 'text', $akhir_pulang, ' class="masked" data-inputmask="&apos;mask&apos;: &apos;h:s&apos;" required'); ?>
        <label for="" class="">Akhir Pulang</label>
    </div>
</div>
<script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
<script>
    (function ($) {
        "use strict";
        
        $(".masked").inputmask();
        //$("#jam_pulang").attr("readonly", true);
        
        $("#jam_masuk").on("focusout", function () {
            
            if(($("#jam_masuk").val()!=="") && ($("#jam_pulang").val()==="")){
                
                // jam masuk
                var masuk = " "+$(this).val();
                var d = new Date();
                var theDate = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate();
                var theTime = theDate + masuk;

                // mulai masuk (satu setengah jam sebelum jam masuk)
                var mulaiMasuk = new Date(Date.parse(theTime) - 90*60*1000 );
                
                var jamMulaiMasuk = mulaiMasuk.getHours(); //returns 0-23
                var menitMulaiMasuk = mulaiMasuk.getMinutes(); //returns 0-59
                var detikMulaiMasuk = mulaiMasuk.getSeconds(); //returns 0-59
                if (jamMulaiMasuk < 10) jamMulaiMasuk = '0' + jamMulaiMasuk;
                if (menitMulaiMasuk < 10) menitMulaiMasuk = '0' + menitMulaiMasuk;
                if (detikMulaiMasuk < 10) detikMulaiMasuk = '0' + detikMulaiMasuk;
                
                // set waktu
                var waktuMulaiMasuk = jamMulaiMasuk + ':' + menitMulaiMasuk + ':' + detikMulaiMasuk;
                $("#mulai_masuk").val(waktuMulaiMasuk);
                //$("#jam_pulang").attr("readonly", false);
                
                Materialize.updateTextFields();                
            }
            else if(($("#jam_masuk").val()!=="") && ($("#jam_pulang").val()!=="")){
                
                // jam pulang
                var pulang = " "+$("#jam_pulang").val();
                var d_pulang = new Date();
                var theDatePulang = d_pulang.getFullYear() + '-' + (d_pulang.getMonth() + 1) + '-' + d_pulang.getDate();
                var theTimePulang = theDatePulang + pulang;
                var waktuPulang = new Date(Date.parse(theTimePulang));
                
                // jam masuk
                var masuk = " "+$(this).val();
                var d_masuk = new Date();
                var theDate = d_masuk.getFullYear() + '-' + (d_masuk.getMonth() + 1) + '-' + d_masuk.getDate();
                var theTimeMasuk = theDate + masuk;
                var waktuMasuk = new Date(Date.parse(theTimeMasuk));
                
                // mulai masuk (satu setengah jam sebelum jam masuk)
                var mulaiMasuk = new Date(Date.parse(theTimeMasuk) - 90*60*1000 );
                var jamMulaiMasuk = mulaiMasuk.getHours(); //returns 0-23
                var menitMulaiMasuk = mulaiMasuk.getMinutes(); //returns 0-59
                var detikMulaiMasuk = mulaiMasuk.getSeconds(); //returns 0-59
                if (jamMulaiMasuk < 10) jamMulaiMasuk = '0' + jamMulaiMasuk;
                if (menitMulaiMasuk < 10) menitMulaiMasuk = '0' + menitMulaiMasuk;
                if (detikMulaiMasuk < 10) detikMulaiMasuk = '0' + detikMulaiMasuk;
                var waktuMulaiMasuk = jamMulaiMasuk + ':' + menitMulaiMasuk + ':' + detikMulaiMasuk;
                
                // waktu nilai tengah
                var midpoint = new Date((waktuPulang.getTime() + waktuMasuk.getTime()) / 2);
                
                // akhir masuk (tengah-tengah antara jam masuk dan jam pulang)
                var jamAkhirMasuk = midpoint.getHours(); //returns 0-23
                var menitAkhirMasuk = midpoint.getMinutes(); //returns 0-59
                var detikAkhirMasuk = midpoint.getSeconds(); //returns 0-59
                if (jamAkhirMasuk < 10) jamAkhirMasuk = '0' + jamAkhirMasuk;
                if (menitAkhirMasuk < 10) menitAkhirMasuk = '0' + menitAkhirMasuk;
                if (detikAkhirMasuk < 10) detikAkhirMasuk = '0' + detikAkhirMasuk;
                var waktuAkhirMasuk = jamAkhirMasuk + ':' + menitAkhirMasuk + ':' + detikAkhirMasuk;
                
                // mulai pulang (tengah-tengah antara jam masuk dan jam pulang + 1 detik)
                var detikMulaiPulang = midpoint.getSeconds(); //returns 0-59
                var menitMulaiPulang = midpoint.getMinutes()+1; //returns 0-59
                if (detikMulaiPulang < 10) detikMulaiPulang = '0' + detikMulaiPulang;
                if (menitMulaiPulang < 10) menitMulaiPulang = '0' + menitMulaiPulang;
                var waktuMulaiPulang = jamAkhirMasuk + ':' + menitMulaiPulang + ':' + detikMulaiPulang;
                
                // akhir pulang (4 jam setelah jam pulang)
                var setWaktuAkhirPulang = new Date(Date.parse(theTimePulang) + 240*60*1000 );
                var jamAkhirPulang = setWaktuAkhirPulang.getHours(); //returns 0-23
                var menitAkhirPulang = setWaktuAkhirPulang.getMinutes(); //returns 0-59
                var detikAkhirPulang = setWaktuAkhirPulang.getSeconds(); //returns 0-59
                if (jamAkhirPulang < 10) jamAkhirPulang = '0' + jamAkhirPulang;
                if (menitAkhirPulang < 10) menitAkhirPulang = '0' + menitAkhirPulang;
                if (detikAkhirPulang < 10) detikAkhirPulang = '0' + detikAkhirPulang;
                var waktuAkhirPulang = jamAkhirPulang + ':' + menitAkhirPulang + ':' + detikAkhirPulang;
                
                // set waktu
                $("#mulai_masuk").val(waktuMulaiMasuk);
                $("#akhir_masuk").val(waktuAkhirMasuk);
                $("#mulai_pulang").val(waktuMulaiPulang);
                $("#akhir_pulang").val(waktuAkhirPulang);
                
                Materialize.updateTextFields();
            }
            else if(($("#jam_masuk").val()==="") || ($("#jam_pulang").val()==="")){
                $("#mulai_masuk").val("");
                $("#akhir_masuk").val("");
                $("#mulai_pulang").val("");
                $("#akhir_pulang").val("");
            }
            
        });
        
        $("#jam_pulang").on("focusout", function () {
            
            if($("#jam_pulang").val()!==""){
                
                // jam pulang
                var pulang = " "+$(this).val();
                var d_pulang = new Date();
                var theDatePulang = d_pulang.getFullYear() + '-' + (d_pulang.getMonth() + 1) + '-' + d_pulang.getDate();
                var theTimePulang = theDatePulang + pulang;
                var waktuPulang = new Date(Date.parse(theTimePulang));
                
                // jam masuk
                var masuk = " "+$("#jam_masuk").val();
                var d_masuk = new Date();
                var theDate = d_masuk.getFullYear() + '-' + (d_masuk.getMonth() + 1) + '-' + d_masuk.getDate();
                var theTimeMasuk = theDate + masuk;
                var waktuMasuk = new Date(Date.parse(theTimeMasuk));
                                
                // waktu nilai tengah
                var midpoint = new Date((waktuPulang.getTime() + waktuMasuk.getTime()) / 2);
                
                // akhir masuk (tengah-tengah antara jam masuk dan jam pulang)
                var jamAkhirMasuk = midpoint.getHours(); //returns 0-23
                var menitAkhirMasuk = midpoint.getMinutes(); //returns 0-59
                var detikAkhirMasuk = midpoint.getSeconds(); //returns 0-59
                if (jamAkhirMasuk < 10) jamAkhirMasuk = '0' + jamAkhirMasuk;
                if (menitAkhirMasuk < 10) menitAkhirMasuk = '0' + menitAkhirMasuk;
                if (detikAkhirMasuk < 10) detikAkhirMasuk = '0' + detikAkhirMasuk;
                var waktuAkhirMasuk = jamAkhirMasuk + ':' + menitAkhirMasuk + ':' + detikAkhirMasuk;
                
                // mulai pulang (tengah-tengah antara jam masuk dan jam pulang + 1 detik)
                var detikMulaiPulang = midpoint.getSeconds(); //returns 0-59
                var menitMulaiPulang = midpoint.getMinutes()+1; //returns 0-59
                if (detikMulaiPulang < 10) detikMulaiPulang = '0' + detikMulaiPulang;
                if (menitMulaiPulang < 10) menitMulaiPulang = '0' + menitMulaiPulang;
                var waktuMulaiPulang = jamAkhirMasuk + ':' + menitMulaiPulang + ':' + detikMulaiPulang;
                
                // akhir pulang (4 jam setelah jam pulang)
                var setWaktuAkhirPulang = new Date(Date.parse(theTimePulang) + 240*60*1000 );
                var jamAkhirPulang = setWaktuAkhirPulang.getHours(); //returns 0-23
                var menitAkhirPulang = setWaktuAkhirPulang.getMinutes(); //returns 0-59
                var detikAkhirPulang = setWaktuAkhirPulang.getSeconds(); //returns 0-59
                if (jamAkhirPulang < 10) jamAkhirPulang = '0' + jamAkhirPulang;
                if (menitAkhirPulang < 10) menitAkhirPulang = '0' + menitAkhirPulang;
                if (detikAkhirPulang < 10) detikAkhirPulang = '0' + detikAkhirPulang;
                var waktuAkhirPulang = jamAkhirPulang + ':' + menitAkhirPulang + ':' + detikAkhirPulang;
                
                // set waktu Akhir Masuk
                $("#akhir_masuk").val(waktuAkhirMasuk);
                $("#mulai_pulang").val(waktuMulaiPulang);
                $("#akhir_pulang").val(waktuAkhirPulang);
                
                Materialize.updateTextFields();                
            }
            
        });
        
        Materialize.updateTextFields();
        
    })(jQuery);
</script>