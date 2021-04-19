<?php
header('application/javascript');
?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_detail = url + "/getDetailPersonil";
            url_update = url + "/update";
            url_get_record = url + "/getDetailRecord";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            delay = (function () {
                var timer = 0;
                return function (callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();
        },

        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").fadeOut(300).promise().then(function () {
                $("#showTabel #showSpinner").fadeIn(300).promise().then(function () {
                    delay(function () {
                        $("#data-tabel").html("");
                        $.post(url_tabel, $("#showTabel #frmData").serialize(), function (data) {
                            $("#data-tabel").html(data);
                            $(".dropdown-button").dropdown();
                            $("#showTabel #showSpinner").fadeOut(300).promise().then(function () {
                                $("#data-tabel").fadeIn(300);
                            });
                        });
                    }, 1000);
                });
            });
        },

        // Load Record
        loadRecord: function () {
            $("#data-detail").fadeOut(300).promise().then(function () {
                $("#showDetail #showSpinner").fadeIn(300).promise().then(function () {
                    $.post(url_get_record, $("#showDetail #frmData").serialize(), function (data) {
                        $("#data-detail").html(data);
                        $("#showDetail #showSpinner").fadeOut(300).promise().then(function () {
                            $("#data-detail").fadeIn(300);
                        });
                    });
                });
            });
        },

        showTabel: function (id_absen) {
            $("#showDetail").fadeOut(300).promise().then(function () {
                $("#showTabel").fadeIn(300);
            });
        },

        showDetail: function (id_absen) {
            $("#showTabel, #showDetail #data-detail").fadeOut(300).promise().then(function () {
                $("#sdate").val("");
                $("#edate").val("");
                $("#showDetail").fadeIn(300).promise().then(function () {
                    $.post(url_detail, {pin_absen: id_absen}, function (data) {
                        $("#sdate").val(data.sdate);
                        $("#edate").val(data.edate);
                        Materialize.updateTextFields();

                        $("#showDetail #showSpinner").hide();
                        app.loadRecord();
                    }, "json");
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

        updateMesin: function (id) {
            $(".indeterminate").show();
            $.post(url_update, {id_mesin: id}, function (data) {
                try {
                    var data = JSON.parse(data);
                    swal(data.info, data.message, data.status);
                } catch (e) {
                    swal("Gagal", "Koneksi ke mesin tidak dapat dilakukan", "error");
                }
                /*
                 if (typeof data.status !== "undefined") {
                 swal(data.info, data.message, data.status);
                 } else {
                 swal("Gagal", "Terjadi kesalahan ketika mengambil data", "error");
                 }
                 */
                $(".determinate").show();
                $(".btnUpdate").prop('disabled', false);
            });
        },

        simpan: function (obj) {
            app.modalLoading("progress");
            $.post(url_simpan, $(obj).serializeArray(), function (data) {
                $.when(app.loadRecord()).done(function () {
                    app.modalLoading("done");
                    $("#frmInputModal").closeModal();
                });
            });
        },
        
        tabelPagging: function (number) {
            $("#showTabel #page").val(number);
            this.loadTabel();
        },

        detailPagging: function (number) {
            $("#showDetail #page").val(number);
            this.loadRecord();
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