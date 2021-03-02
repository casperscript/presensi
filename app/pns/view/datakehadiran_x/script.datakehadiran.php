<?php
header('application/javascript');
?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
        },
        loadTabel: function () {
            $("#data-tabel").fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $("#data-tabel").html("");
                    $.post(url_tabel, $("#frmData").serialize(), function (data) {
                        $("#data-tabel").html(data);
                        $("#showSpinner").fadeOut(300).promise().then( function () {
                            $("#data-tabel").fadeIn(300);
                        });
                    });
                });
            });
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },
    };