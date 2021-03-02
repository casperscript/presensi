<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_detail = url + "/detail";
            url_form = url + "/form";

            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
        },

        // Load Tabel Shift
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
        
        loadDetail: function (id) {
            $("#data-tabel, .navSatker, #detail-tabel").fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $("#detail-tabel").html("");
                    $.post(url_detail, {pin_absen: id}, function (data) {
                        $("#detail-tabel").html(data);
                        $("#showSpinner").fadeOut(300).promise().then(function () {
                            $("#detail-tabel").fadeIn(300);
                            $(".navSatker").fadeOut(300);
                        });
                    });
                });
            });
        },
        
        showForm: function (id_jadwal, pin_absen, kdlokasi) {
            $.post(url_form, {id_jadwal: id_jadwal, pin_absen: pin_absen, kdlokasi: kdlokasi}, function (data) {
                app.modalLoading("prepare");
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal({
                    dismissible: false
                });
            });
            //console.clear();
        },
        
        showConfirm: function (id, op, field, title, msg) {
            swal({
                title: title,
                text: msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Hapus data!",
                closeOnConfirm: false
            }, function () {
                $.post(url_hapus, {op: op, field: field, id: id}, function (data) {
                    if (data.status === "success") {
                        swal("Berhasil!", "Data shift telah terhapus.", "success");
                        app.loadDetail();
                    } else {
                        swal("Gagal!", "Terjadi kesalahan ketika menghapus, coba lagi.", "warning");
                    }
                }, "json");
            });
        },

        simpan: function (formId) {
            var form = $(formId).serializeArray();
            app.modalLoading("progress");
            var pin_absen = $("#pin_absen").val();
            $.post(url_simpan, form, function (data) {
                if (data.status === "error") {
                    swal(data.title, data.message, data.status);
                } else {
                    $.when(app.loadDetail(pin_absen)).then(function () {
                        app.modalLoading("done");
                        $("#frmInputModal").closeModal();
                        swal(data.title, data.message, data.status);
                    });
                }
            }, "json");
        },
        
        hapus: function (id) {
            var pin_absen = $("#pin_absen").val();
            swal({
                title: "Hapus!",
                text: "Yakin akan menghapus jadwal ini?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Hapus data!",
                closeOnConfirm: false
            }, function () {
                $.post(url_hapus, {op: "hapus", field: "id_jadwal", id: id}, function (data) {
                    if (data.status === "success") {
                        swal("Berhasil!", "Data shift telah terhapus.", "success");
                        app.loadDetail(pin_absen);
                    } else {
                        swal("Gagal!", "Terjadi kesalahan ketika menghapus, coba lagi.", "warning");
                    }
                }, "json");
            });
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showPilSatker: function (id_kelompok) {
            var $select = $("#kdlokasi");
            $.post(url_get_lokasi_kerja, {id_kelompok: id_kelompok}, function (data) {
                $("#kdlokasi").html("");
                $.each(data, function (i, value) {
                    $('#kdlokasi').append($('<option>').text(value).attr('value', i));
                });
                $("#kdlokasi").material_select();
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

