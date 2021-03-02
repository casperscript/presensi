<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
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

        showForm: function (kd_panduan) {
            $("#resultForm").html("");
            $.post(url_form, {kd_panduan: kd_panduan}, function (data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
            console.clear();
        },
        
        showHapus: function (id, nama) {
            $("#myConfirmModalLabel").html("Konfirmasi Hapus");
            $("#data-confirm").html("Apakah anda ingin menghapus data <span class='blue-text' for=''><b>"+id+" - "+nama+"</b></span> ?");
            $("#id_confirm").val(id);
            $("#confirmModal").openModal();
        },
                
        simpan: function (obj) {
            $("#btnSubmitSimpan").removeClass("waves-effect waves-light btn green").addClass("btn disabled hide");
            $("#resultForm").html('<span class="blue-text">memproses data...</span>')
            var form = $(obj)[0];
            var data = new FormData(form);
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: url_simpan,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (response){
                    $("#frmInputModal").closeModal();
                    $("#btnSubmitSimpan").removeClass("btn disabled hide").addClass("waves-effect waves-light btn green");                   
                    app.loadTabel();
                },
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