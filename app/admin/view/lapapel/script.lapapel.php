<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_buatlaporan = url + "/buatlaporan";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_root = url;
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showPilSatker: function (id_kelompok, lokasi) {
            var obj = $('#kdlokasi');
            $.post(url_get_lokasi_kerja, {id_kelompok: id_kelompok}, function (data) {
                obj.html('');
                obj.append($('<option>').text('-- Pilih Satuan Kerja --').attr('value', ''));
                $.each(data, function(i, value){
                    obj.append($('<option>').text(value).attr('value', i));
                });
                obj.material_select();
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

        // Show Navigasi
        showIndex: function () {
            $("#showTabel").fadeOut(300).promise().then(function () {
                $("#showIndex").fadeIn(300);
            });
        },

        // Show Data
        loadTabel: function () {
            $("#data-tabel").hide();
            $("#progress").show();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#progress").fadeOut(500).promise().then(function () {
                    $("#data-tabel").fadeIn(500);
                });
            });
        },

        buatLap: function (obj) {
            var form = $("#frmData").serialize();
            if (form.search('pin_absen') == -1)
                return true;

            $("#tombol").hide();
            $('#preloader').removeAttr('style');
            $.post(url_buatlaporan, form, function (data) {
                window.open(url_root + data);
                $("#preloader").fadeOut(500).promise().then(function () {
                    $("#tombol").fadeIn(500);
                });
            });
        },
    };
<!--</script>-->