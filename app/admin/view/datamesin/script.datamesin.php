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
        },
        
        // Load Tabel Jadwal
        loadTabel: function () {
            $("#data-tabel").html("");
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
            });
        },

        // Load Tabel Jadwal
        loadRecord: function () {
            $("#data-detail").html("");
            $.post(url_get_record, $("#frmDataDetail").serialize(), function (data) {
                $("#data-detail").html(data);
                $("#data-detail").show();
            });
        },
        
        showDetail: function (id_absen) {
            $("#showTabel").hide();
            $.post(url_detail, {pin_absen: id_absen}, function (data) {
                $("#pin_absen").val(data.pin_absen);
                $("#nama_personil").html(data.nama_personil);
                $("#nip_personil").html("NIP. " + data.nipbaru);
                $("#showDetail").show();
            }, "json");
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