<?php
header('application/javascript');
?>
<!--<script>-->
    if (window.innerWidth >= 720) {
        app = {
            init: function (url) {
                url_tabel = url + "/daftarVerModDesktop";
                url_changeStat = url + "/updateModerasi";
                url_massVerif = url + "/massVerifPage";
                url_simpan = url + "/simpanVerif";
                url_simpanDesktop = url + "/simpanVerifDesktop";
                url_info = url + "/detailModerasiDesktop/";
            },
            loadTabel: function () {
                $("#progressView").attr("class", "indeterminate");
                $.post(url_tabel, $("#frmData").serialize(), function (data) {
                    $("#divTabelVerMod").html(data);
                    $("#progressView").attr("class", "determinate");
                    
                    $(".tooltipped").tooltip();
                    app.chkModHandler(".check-all-mod", ".btn-mass-verif");
                    //Material.updateTextFields();
                });
            },
            infoModerasi: function (id) {
                $.get(url_info + id, function (result) {
                    $(".detailAction").hide();
                    $("#data-detail").html(result);
                    $("#modalDetail").openModal();
                    $(".tooltipped").tooltip();
                });
            },
            changeStatMod: function (id, flag) {
                $.post(url_changeStat, {mid: id, flag: flag, catatan: "-"}, function (result) {
                    if (result.status === "success") {
                        $("#tdOpOpd-" + id).html(result.symbolMod);
                        $(".tooltipped").tooltip();
                        Materialize.toast(result.message, 1500);
                    } else {
                        Materialize.toast(result.message, 1500);
                    }
                }, "json");
            },
            chkModHandler: function (className, targetEl) {
                var nChecked = 0;
                $(className).each(function (id) {
                    if ($(this).is(":checked")) {
                        nChecked++;
                    }
                });

                if (nChecked > 1) {
                    $(targetEl).removeClass("disabled");
                } else {
                    $(targetEl).removeClass("disabled").addClass("disabled");
                }
            },
            verifMassal: function (flag) {
                if ($(this).hasClass("disabled")) {
                    return false;
                } else {
                    var listMods = [];
                    $(".check-all-mod").each(function () {
                        if ($(this).is(":checked")) {
                            listMods.push($(this).val());
                        }
                    });
                    $.post(url_massVerif, {checkedMods: listMods, flag: flag}, function (html) {
                        $("#data-form").html(html);
                        $("#simpanProgress").attr("class", "determinate");
                        $('#modalInput').openModal('open');
                        //Materialize.updateTextFields();
                    });
                }
            },
            simpanVerif: function (obj) {
                $("#simpanProgress").attr("class", "indeterminate");
                $.post(url_simpan, $("#frmInput").serialize(), function (result) {
                    app.loadTabel();

                    $("#simpanProgress").attr("class", "determinate");
                    Materialize.toast(result.message, 1500);
                    $("#modalInput").closeModal();
                }, "json");
            },
            simpanVerifMob: function (obj) {
                $.post(url_simpanDesktop, $(obj).serialize(), function (result) {
                    Materialize.toast(result.message, 1500);
                    $("#tdOpOpd-" + result.id).html(result.badge);
                    $(".tooltipped").tooltip();
                }, "json");
            }
        };

    } else {
        app = {
            init: function (url) {
                url_tabel = url + "/daftarVerModMobile";
                url_detail = url + "/detailModerasiMobile";
                url_simpan = url + "/simpanVerifMobile";
            },
            loadTabel: function () {
                $("#progressView").attr("class", "indeterminate");
                $.post(url_tabel, $("#frmData").serialize(), function (data) {
                    $("#divTabelVerMod").html(data);
                    $("#progressView").attr("class", "determinate");
                });
            },
            detail: function (id) {
                $.post(url_detail, {id: id}, function (result) {
                    $(".detailAction").hide();

                    $("#data-detail").html(result);
                    $('#modalDetail').openModal('open');
                    $(".tooltipped").tooltip();
                });
            },
            simpanVerifMob: function (obj) {
                $.post(url_simpan, $(obj).serialize(), function (result) {
                    Materialize.toast(result.message, 1500);
                    $("#" + result.id + " .badge").replaceWith(result.badge);
                }, "json");
            }
        };
    }