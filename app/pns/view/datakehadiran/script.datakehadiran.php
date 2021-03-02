<?php
header('application/javascript');
?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
        },

        loadTabel: function () {
            $("#data-tabel").fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $("#data-tabel").html("");
                    $.post(url_tabel, $("#frmData").serialize(), function (data) {
                        $("#data-tabel").html(data);
                        $("#showSpinner").fadeOut(300).promise().then(function () {
                            $("#data-tabel").fadeIn(300);
                        });
                    });
                });
            });
        },

        showForm: function (pin, tanggal, jam, status) {
            $.post(url_form, {pin_absen: pin, tanggal_log_presensi: tanggal, jam_log_presensi: jam, status_log_presensi: status}, function (data) {
                app.modalLoading("prepare");
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },
        
        simpan: function (obj) {
            app.modalLoading("progress");
            $.post(url_simpan, $(obj).serializeArray(), function (data) {
                $.when(app.loadTabel()).done(function () {
                    app.modalLoading("done");
                    $("#frmInputModal").closeModal();
                });
            });
        },
        
        modalLoading: function (msg) {
            if (msg == "prepare") {
                $(".progress #loading").attr("class", "determinate");
                $(".progress #loading").attr("style", "width: 0%");
            } else if (msg == "progress") {
                $(".progress #loading").toggleClass("determinate", "indetermiante");
            } else if (msg == "done") {
                $(".progress #loading").toggleClass("indeterminate determinate");
                $(".progress #loading").attr("style", "width: 100%");
            }
        },
    };