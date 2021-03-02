<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_detail = url + "/getDetailPersonil";
            url_get_record = url + "/getDetailRecord";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
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
                    $.post(url_get_record, $("#showDetail #frmDataDetail").serialize(), function (data) {
                        $("#data-detail").html(data);
                        $("#showDetail #showSpinner").fadeOut(300).promise().then(function () {
                            $("#data-detail").fadeIn(300);
                        });
                    });
                });
            });
        },

        showDetail: function (id_absen) {
            $("#showTabel, #showDetail #data-detail").fadeOut(300).promise().then(function () {
                $("#pin_absen").val("");
                $("#nama_personil").html("");
                $("#nip_personil").html("NIP. ");
                $("#sdate").val("");
                $("#edate").val("");
                $("#showDetail").fadeIn(300).promise().then(function () {
                    $.post(url_detail, {pin_absen: id_absen}, function (data) {
                        $("#pin_absen").val(data.pin_absen);
                        $("#nama_personil").html(data.nama_personil);
                        $("#nip_personil").html("NIP. " + data.nipbaru);
                        $("#sdate").val(data.sdate);
                        $("#edate").val(data.edate);
                        Materialize.updateTextFields();
                        app.loadRecord();
                    }, "json");
                });
            });
        },
        
        showTabel: function (id_absen) {
            $("#showDetail").hide().promise().then(function () {
                $("#showTabel").show();
            });
        },
        
        detailPagging: function (number) {
            $("#pageDetail").val(number);
            this.loadRecord();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showForm: function (id_jadwal) {
            $.post(url_form, {id_jadwal: id_jadwal}, function (data) {
                $("#data-form-input").html(data);
                $(".btnFooter").show();
                $('select').material_select();
            });
        },
        
        showPilSatker: function (id_kelompok) {
            var $select = $("#kdlokasi");
            $.post(url_get_lokasi_kerja, {id_kelompok: id_kelompok}, function (data) {
                $("#kdlokasi").html("");
                $.each(data, function(i, value){
                    $('#kdlokasi').append($('<option>').text(value).attr('value', i));
                });
                $("#kdlokasi").material_select();
            });
        },
        
        simpan: function () {
            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                alert(data.message);
                $("#frmInputModal").modal("hide");
                app.loadTabel();
            }, "json");
        },
        
        hapus: function () {
            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                $("#confirmModal").modal("hide");
                $(".errMsg").html(data);
                app.loadTabel();
            });
        },
    };
<!--</script>-->