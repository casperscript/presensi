<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
        },
        
        // Load Tabel Tunjangan
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

        showForm: function (id_tunjangan) {
            $.post(url_form, {id_tunjangan: id_tunjangan}, function (data) {
                $("#data-form-input").html(data);
                $(".btnFooter").show();
                $('select').material_select();
            });
        },
        
        simpan: function () {
            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                alert(data.message);
                $("#frmInputModal").modal("hide");
                app.loadTabel();
            }, "json");
        },
        
        hapus: function () {
            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                $("#confirmModal").modal("hide");
                $(".errMsg").html(data);
                app.loadTabel();
            });
        },
    };
<!--</script>-->