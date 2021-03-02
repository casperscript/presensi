<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_navindex = url + "/navIndex";
            url_user_dinas = url + "/userDinas";
            url_user_finger = url + "/userFinger";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_get_backupfinger = url + "/getBackupUserFinger";
            
            widget_content = "#data-userdinas, #data-userfinger";
            widget_spinner = "#showSpinner";
            content_dinas = "#data-userdinas";
            content_finger = "#data-userfinger";
        },
        //show navigasi index
        showIndex: function () {
            $("#showTabel").fadeOut(300).promise().then(function () {
                $("#showIndex").fadeIn(300);
            });
        },
        showTabel: function () {
            $("#showIndex").fadeOut(300).promise().then(function () {
                var kdlokasi = $("#showIndex #kdlokasi").val();
                $("#showTabel #kdlokasi").val(kdlokasi);
                $("#showTabel").fadeIn(300);
                app.loadUserDinas();
            });
        },

        // Load Data
        loadSpinner: function () {
            $("#data-userdinas, #data-userfinger").fadeOut(300).promise().then(function () {
                $(widget_spinner).fadeIn(300);
            });
        },
        loadUserDinas: function () {
            $(widget_content).fadeOut(300).promise().then(function () {
                $(widget_spinner).fadeIn(300).promise().then(function () {
                    $.post(url_user_dinas, $("#showTabel #frmData").serialize(), function (data) {
                        $("#data-userdinas").html(data);
                        $(widget_spinner).fadeOut(300).promise().then(function () {
                            $("#data-userdinas").fadeIn(300);
                        });
                    });
                });
            });
        },
        loadUserFinger: function () {
            $(widget_content).fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $.post(url_user_finger, $("#showTabel #frmData").serialize(), function (data) {
                        $("#data-userfinger").html(data);
                        $("#showSpinner").fadeOut(300).promise().then(function () {
                            $("#data-userfinger").fadeIn(300);
                        });
                    });
                });
            });
        },
        tabelPagging: function (number) {
            $("#page").val(number);
            app.loadUserDinas();
        },

        // JSON / Get Data
        showPilSatker: function (id) {
            var dt = $(id).val();
            var $select = $("#kdlokasi");
            $.post(url_get_lokasi_kerja, {kd_kelompok_lokasi_kerja: dt}, function (data) {
                $("#kdlokasi").html("");
                $.each(data, function (i, value) {
                    $('#kdlokasi').append($('<option>').text(value).attr('value', i));
                });
                $("#kdlokasi").material_select();
            });
        },
        getBackupFinger: function () {
            $.post(url_get_backupfinger, $("#showTabel #frmData").serialize(), function (response) {
                var msg = JSON.parse(response);
                swal("Informasi", msg.message, msg.status);
                app.loadUserDinas();
            });
        }
    };