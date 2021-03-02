<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_simpan = url + "/simpan";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_detail = url + "/getDetailPersonil";
            url_get_record = url + "/getDetailRecord";
        },
        
        // Load Tabel
        loadTabel: function () {
            if ($('#kdlokasi option:selected').val() == '')
                return true;

            $("#data-tabel").html("");
            $("#progress").removeAttr('style');
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
            });
        },

        // Load Tabel Detail
        loadRecord: function () {
            $("#data-detail").html("");
            $.post(url_get_record, $("#frmDataDetail").serialize(), function (data) {
                $("#data-detail").html(data);
                $("#data-detail").show();
            });
        },

        showTabel: function (id_absen) {
            $("#showDetail").hide().promise().then(function () {
                $("#showTabel").show();
            });
        },

        showDetail: function (id_absen) {
            $("#showTabel").hide();
            $('#data-detail').html('');
            $.post(url_detail, {pin_absen: id_absen}, function (data) {
                $("#pin_absen").val(data.pin_absen);
                $("#nama_personil").html(data.nama_personil);
                $("#nip_personil").html("NIP. " + data.nipbaru);
                $("#showDetail").show();
            }, "json");
        },

        detailPagging: function (number) {
            $("#pageDetail").val(number);
            this.loadRecord();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showPilSatker: function (id_kelompok, lokasi) {
            var obj = $(lokasi);
            $.post(url_get_lokasi_kerja, {kd_kelompok_lokasi_kerja: id_kelompok}, function (data) {
                obj.html('');
                obj.append($('<option>').text('-- Pilih Satuan Kerja --').attr('value', ''));
                $.each(data, function(i, value){
                    obj.append($('<option>').text(value).attr('value', i));
                });
                obj.material_select();
            });
        },
    };
<!--</script>-->