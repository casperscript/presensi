<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
        },
        
        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showPilSatker: function (id_kelompok, lokasi) {
            var obj = $(lokasi);
            $.post(url_get_lokasi_kerja, {id_kelompok: id_kelompok}, function (data) {
                obj.html('');
                obj.append($('<option>').text('-- Pilih Satuan Kerja --').attr('value', ''));
                $.each(data, function(i, value){
                    obj.append($('<option>').text(value).attr('value', i));
                });
                obj.material_select();
            });
        },
    };