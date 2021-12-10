<?php header('application/javascript');?>
<!-- <script> -->
    app = {
        init: function (url) {
            url_cetaktpp = url + "/cetaktpp";
            url_presensi = url + "/cetakpresensi";
            url_updatebendahara = url + "/updateBendahara";
            url_getJSON = url + "/getJSON";
        },

        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            //$.post(url_cetaktpp, {kd_tpp: $('#kd_tpp').val()}, function (data) {
            $.post(url_cetaktpp, {kd_tpp: $('#kd_tpp').val(), tahun: $("#pilihtahun").val()}, function (data) {
            //$.post(url_cetaktpp, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);

                $('#btnBendahara').on('click', function() {
                    app.ubahBendahara();
                });
                $('select').material_select();
            });
        },

        // Load Tabel
        loadTabelpresensi: function () {
            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            var form = {
                kd_tpp: $('#kd_tpp').val(),
                jenis: $('#jenis').val(),
                tahun: $("#tahun").val(),
                tpptingkat: $('#tpptingkat').val()
            };
            $.post(url_presensi, form, function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);

                $('select').material_select();
                $('#btnBendahara').on('click', function() {
                    app.ubahBendahara();
                });
                $('#btnMod').on('click', function() {
                    var data = {
                        tingkat : $(this).data('tingkat'),
                        tgl : $(this).data('tgl'),
                        periode : $(this).data('periode')
                    }
                    
                    app.checkMod(data);
                });
                $('#btnCetak').on('click', function() {
                    $('#frmData').submit();
                });
            });            
        },

        checkBc: function () {
            var data = $("#frmData").serialize() + "&op=checkInduk";
            $.post(url_getJSON, data, function (response) {
                if (response.status == false) {
                    $("#btnCetak").hide();
                    $("#btnMsg").show();
                } else {
                    $("#btnCetak").show();
                    $("#btnMsg").hide();
                }
            }, "json");
        },

        checkMod: function (data) {
            if (data.tingkat == 2) {
                alert('Maaf, Anda belum bisa cetak laporan '+ data.periode +' karena ada moderasi tgl '+ data.tgl +' yang belum Anda verifikasi.');
                window.location.href = url_mod;
            } else (data.tingkat == 3)
                alert('Maaf, Anda belum bisa cetak laporan '+ data.periode +' karena ada moderasi tgl '+ data.tgl +' yang belum KEPALA OPD verifikasi.');
        },

        ubahBendahara: function () {
            var pilih = $('#pilihbendahara option:selected').val();
            if (pilih == '')
                return;

            $('#btnBendahara').html('Proses ...');
            $('#btnBendahara').removeClass("waves-effect waves-light btn orange").addClass("btn disabled").attr('disabled', true);

            var id = $('#ini-bendahara #bendahara').val();
            $.post(url_updatebendahara, {nipbaru : pilih, id: id}, function (data) {
                alert(data.message);
                if (data.status == 'success')
                    $('#bendahara').val(1);

                $('#format').val('TPP');
                app.loadTabel();
            });   
        }
    };