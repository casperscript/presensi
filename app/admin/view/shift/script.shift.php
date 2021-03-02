<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/formInputShift";
            url_formEditShift = url + "/formEditShift";
            url_formjamkerja = url + "/formInputJamKerja";
            url_jamkerja = url + "/jamkerja";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_detail = url + "/getDetailPersonil";
            url_get_record = url + "/getDetailRecord";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            content_tabel = "#data-tabel, #form-tabel";
            form_shift = ".navShift #frmData";
        },

        // Load Tabel Shift
        loadTabel: function () {
            $(content_tabel).fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $("#data-tabel").html("");
                    $.post(url_tabel, $(".navSatker #frmData").serialize(), function (data) {
                        $("#data-tabel").html(data);
                        $("#showSpinner, .navSatker").fadeOut(300).promise().then(function () {
                            $("#data-tabel").fadeIn(300);
                            $(".navShift").fadeIn(300);
                        });
                    });
                });
            });
        },
        loadJamKerja: function (id) {
            $("#showJamKerja").fadeOut(300).promise().then(function () {
                $.post(url_jamkerja, {id_shift: id}, function (data) {
                    $("#idShift").val(id);
                    $(".collection-item").removeClass("active");
                    $("#list" + id).addClass("active");
                    $.when($("#showJamKerja").html(data)).then(function () {
                        $("#showJamKerja").fadeIn(300);
                    });
                });
            });
        },
        loadForm: function (id) {
            $(content_tabel).fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $("#form-tabel").html("");
                    var kdlokasi = $("#kdlokasi").val();
                    $.post(url_form, {id_shift: id, kdlokasi: kdlokasi}, function (data) {
                        $("#form-tabel").html(data);
                        $("#showSpinner").fadeOut(300).promise().then(function () {
                            $("#form-tabel").fadeIn(300);
                        });
                    });
                });
            });
        },

        showForm: function (id_shift, id_satker) {
            $.post(url_form, {id_shift: id_shift, id_satker: id_satker}, function (data) {
                app.modalLoading("prepare");
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal({
                    dismissible: false
                });
            });
            //console.clear();
        },
        showFormEditShift: function (id_shift) {
            $.post(url_formEditShift, {id_shift: id_shift}, function (data) {
                app.modalLoading("prepare");
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
        },
        showFormJam: function (id) {
            $.post(url_formjamkerja, {id_shift_detail: id}, function (data) {
                app.modalLoading("prepare");
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
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
                        app.loadTabel();
                    } else {
                        swal("Gagal!", "Terjadi kesalahan ketika menghapus, coba lagi.", "warning");
                    }
                }, "json");
            });
        },

        simpan: function (formId) {
            var form = $(formId).serializeArray();
            form.push({name: 'status_shift', value: $("#status_shift").val()});
            app.modalLoading("progress");
            $.post(url_simpan, form, function (data) {
                $.when(app.loadTabel()).then(function () {
                    app.modalLoading("done");
                    window.setTimeout(function () {
                        $("#frmInputModal").closeModal();
                        swal("ok", "okkk", "success");
                    }, 600);
                });
            }, "json");
        },
        simpanJam: function (formId) {
            var form = $(formId).serializeArray();
            form.push({name: 'status_shift', value: $("#status_shift").val()});
            app.modalLoading("progress");
            $.post(url_simpan, form, function (data) {
                var idShift = $("#idShift").val();
                $.when(app.loadJamKerja(idShift)).then(function () {
                    app.modalLoading("done");
                    window.setTimeout(function () {
                        $("#frmInputModal").closeModal();
                    }, 600);
                });
            }, "json");
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

