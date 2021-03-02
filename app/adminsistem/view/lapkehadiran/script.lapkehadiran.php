<?php header('application/javascript'); ?>
<!--<script>-->
    app = {
        init: function (url) {
            url_navindex = url + "/navIndex";
            url_navtabel = url + "/navTabel";
            url_tabel = url + "/tabel";
            url_reg_kehadiran = url + "/pdfRegister";
            url_pdf_kehadiran = url + "/pdfKehadiran";

            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_detail = url + "/getDetailPersonil";
            url_get_record = url + "/getDetailRecord";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
        },

        // Show Navigasi
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
                app.loadTabel();
            });
        },

        // Show Data
        loadTabel: function () {
            $("#data-tabel").fadeOut(300).promise().then(function () {
                $("#showSpinner").fadeIn(300).promise().then(function () {
                    $.post(url_tabel, $("#showTabel #frmData").serialize(), function (data) {
                        $("#data-tabel").html(data);
                        $("#showSpinner").fadeOut(300).promise().then(function () {
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
        printKehadiran: function (obj) {
            $.post(url_reg_kehadiran, obj.serialize(), function (data) {
                window.open(url_pdf_kehadiran);
            });
        },

        showNavigation: function (id_absen) {
            $("#showTabel").hide().promise().then(function () {
                $("#showIndex").show();
            });
        },

        showForm: function (id_jadwal) {
            $.post(url_form, {id_jadwal: id_jadwal}, function (data) {
                $("#data-form-input").html(data);
                $(".btnFooter").show();
                $('select').material_select();
            });
        },

        showPilSatker: function (id) {
            var $select = $("#kdlokasi");
            $.post(url_get_lokasi_kerja, {kd_kelompok_lokasi_kerja: id}, function (data) {
                $("#kdlokasi").html("");
                $.each(data, function (i, value) {
                    $('#kdlokasi').append($('<option>').text(value).attr('value', i));
                });
                $("#kdlokasi").material_select("update");
            });
        }
    };