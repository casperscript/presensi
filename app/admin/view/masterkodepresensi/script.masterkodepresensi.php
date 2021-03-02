<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_cek_primary = url + "/cekprimary";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
        },
        
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showForm: function (id_kode_presensi) {
            $.post(url_form, {id_kode_presensi: id_kode_presensi}, function (data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
        },
        
        cekPrimary: function (kode_presensi) {
            $.post(url_cek_primary, {kode_presensi:kode_presensi}, function (data) {
                if($("#kode_presensi").val()==""){
                    $("#cekprimary").html('<span class="red-text" for="">Isikan Kode Presensi</span>');
                }
                else{
                    if(data=="ada"){
                        $("#cekprimary").html('<span class="red-text" for="">Kode Presensi sudah ada.</span>');
                    }
                    else{
                        $("#cekprimary").html('<span class="blue-text">Kode Presensi tersedia.</span>');
                    }
                }
            }, "json");
        },
                
        simpan: function () {
            var form = $('#frmInput').serializeArray();
            form.push({name: 'moderasi_kode_presensi', value: $("#moderasi_kode_presensi").val()});
            $.post(url_simpan, form, function(data){
                $("#frmInputModal").closeModal();
                app.loadTabel();
            }, "json");
        },
                
        hapus: function () {
            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                $("#confirmModal").closeModal();
                app.loadTabel();
            }, "json");
        },
    };
<!--</script>-->