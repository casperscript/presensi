<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_pil_admin = url + "/piladmin";
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
            console.clear();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
            console.clear();
        },

        showForm: function (username) {
            $.post(url_form, {username: username}, function (data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
            console.clear();
        },
        
        cekPrimary: function (username) {
            $.post(url_cek_primary, {username:username}, function (data) {
                if($("#username").val()==""){
                    $("#cekprimary").html('<span class="red-text" for="">Isikan Username</span>');
                }
                else{
                    if(data=="ada"){
                        $("#cekprimary").html('<span class="red-text" for="">Username sudah ada.</span>');
                    }
                    else{
                        $("#cekprimary").html('<span class="blue-text">Username tersedia.</span>');
                    }
                }
            }, "json");
            console.clear();
        },
        
        simpan: function () {
            var form = $('#frmInput').serializeArray();
            form.push({name: 'status_pengguna', value: $("#status_pengguna").val()});
            $.post(url_simpan, form, function(data){
                $("#frmInputModal").closeModal();
                app.loadTabel();
            }, "json");
            console.clear();
        },
        
        pilAdmin: function (kdlokasi) {
            var $select = $("#kdlokasi");
            $.post(url_pil_admin, {kdlokasi: kdlokasi}, function (data) {
                $("#nipbaru").html(data).material_select("update");
            });
            console.clear();
        },
                
        hapus: function () {
            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                $("#confirmModal").closeModal();
                app.loadTabel();
            }, "json");
            console.clear();
        },
    };
<!--</script>-->