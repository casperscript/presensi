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
        
        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
            console.clear();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
            console.clear();
        },

        showForm: function (kd_aturan) {
            $.post(url_form, {kd_aturan: kd_aturan}, function (data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
            console.clear();
        },
        
        cekPrimary: function (kd_aturan) {
            $.post(url_cek_primary, {kd_aturan:kd_aturan}, function (data) {
                if($("#kd_aturan").val()==""){
                    $("#cekprimary").html('<span class="red-text" for="">Isikan Kode Aturan</span>');
                }
                else{
                    if(data=="ada"){
                        $("#cekprimary").html('<span class="red-text" for="">Kode Aturan sudah ada.</span>');
                    }
                    else{
                        $("#cekprimary").html('<span class="blue-text">Kode Aturan tersedia.</span>');
                    }
                }
            }, "json");
            console.clear();
        },
        
        simpan: function () {
            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                window.setTimeout(function(){$("#resultForm").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){$("#frmInputModal").closeModal();}, 3000);
                app.loadTabel();
            }, "json");
            $("#btnSubmitSimpan").removeClass("btn disabled").addClass("waves-effect waves-light btn green");
            console.clear();
        },
        
        hapus: function () {
            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                window.setTimeout(function(){$("#resultHapus").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){$("#confirmModal").closeModal();}, 3000);
                app.loadTabel();
            }, "json");
            $("#btnConfirmHapus").removeClass("btn disabled").addClass("waves-effect waves-light btn red");
            console.clear();
        },
    };
<!--</script>-->