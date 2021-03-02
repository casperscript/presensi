<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_login_submit = url + "/submit";
        },

        // Submit Login
        submitLogin: function () {
            $("#showBtnSubmit").fadeOut(300).promise().then(function () {
                $("#showLoader").fadeIn(300).promise().then(function () {
                    $.post(url_login_submit, $("#frmLogin").serialize(), function (data) {
                        if (data.status === 1) {
                            var targetApp = "pns";

                            switch (data.data.grup_pengguna_kd.toUpperCase()) {
                                case "KDGRUP99":
                                    targetApp = "adminsistem";
                                    break;
                                case "KDGRUP01":
                                    targetApp = "adminopd";
                                    break;
                                case "KDGRUP02":
                                    targetApp = "kepalaopd";
                                    break;
                                case "KDGRUP03":
                                    targetApp = "admin";
                                    break;
                                case "KDGRUP04":
                                    targetApp = "kepalabkppd";
                                    break;
                                case "KDGRUP06":
                                    targetApp = "pengawas";
                                    break;
                                case "KDGRUP07":
                                    targetApp = "anggaran";
                                    break;
                                default:
                                    targetApp = "pns";
                                    break;
                            }
                            $("#showMessage").html(data.pesan);
                            $("#showLoader").fadeOut(300).promise().then(function () {
                                $("#showMessage").fadeIn(1000).promise().then(function () {
                                    targetApp = url_login_submit.replace("login/submit", targetApp);
                                    window.location.href = targetApp;
                                });
                            });
                        } else {
                            $("#showLoader").fadeOut(300).promise().then(function () {
                                $("#showMessage").html(data.pesan);
                                $("#showMessage").fadeIn(1000).promise().then(function () {
                                    $("#showMessage").fadeOut(1000).promise().then(function () {
                                        $("#showBtnSubmit").fadeIn(300);
                                    });
                                });
                            });
                        }
                    }, "json");

                });
            });
        }
    };