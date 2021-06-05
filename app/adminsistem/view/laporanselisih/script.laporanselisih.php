<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabeltpp";
            delay = (function () {
                var timer = 0;
                return function (callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();
        },
        loadTabel: function () {
            $("#showTabel").fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    delay(function () {
                        $("#data-tabel").html("");
                        $.post(url_tabel, $("#frmData").serialize(), function (data) {
                            $("#data-tabel").html(data);
                            $("#showSpinner").fadeOut(300).promise().then(function () {
                                $("#showTabel").fadeIn(300);
                            });
                        });
                    }, 1000);
                });
            });
        },
    };