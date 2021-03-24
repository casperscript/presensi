<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabeltpp";
        },
        loadTabel: function () {
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
            });
        }
    };