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

        showForm: function (id_shift) {
            $.post(url_form, {id_shift: id_shift}, function (data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
            console.clear();
        },
                
        simpan: function () {
            var form = $('#frmInput').serializeArray();
            form.push({name: 'status_shift', value: $("#status_shift").val()});
            $.post(url_simpan, form, function(data){
                $("#frmInputModal").closeModal();
                app.loadTabel();
            }, "json");
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