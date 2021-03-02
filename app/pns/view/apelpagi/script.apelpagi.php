<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
        },
        
        // Load Tabel
        loadTabel: function () {
            $("#data-detail").html("");
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-detail").html(data);
            });
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },
    };