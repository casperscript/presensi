<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabelpresensi = url + "/tabelpresensi";
            url_tabelapel = url + "/tabelapel";
            url_tabelmasuk = url + "/tabelmasuk";
            url_tabelpulang = url + "/tabelpulang";
            url_tabelpersonil = url + "/tabelpersonil";
            url_tabelrekap = url + "/tabelrekap";
            url_tabeltpp = url + "/tabeltpp";
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        // Load Tabel
        loadTabel: function () {
            var format = $('#format option:selected').val();
            var jenis = $('#jenis option:selected').val();

            $('#download').val(0);
            var form = $("#frmData").serialize();
            if (format == 'C') {
                var url = url_tabelpersonil;
                var batas = $('#batas option:selected').val();
                if (batas == undefined)
                    batas = 10;
                    
                var cari = $('#cari').val();
                if (cari === undefined)
                    cari = '';

                form += '&batas='+batas+'&cari='+cari;
            } else if (format == 'TPP') {
                if ($('#tpp').val() == 'tpp13')
                    var url = url_tabeltpp13;
                else
                    var url = url_tabeltpp;
            } else {
                /*if (jenis == 1)
                    var url = url_tabelmasuk;
                else if (jenis == 2)
                    var url = url_tabelapel;
                else
                    var url = url_tabelpulang;
                */
                var url = url_tabelpresensi;
            }

            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            $.post(url, form, function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
                $('#download').val(1);
                $('.btnVer').on('click', function() {
                    app.verify();
                });
                $('.btnMod').on('click', function() {
                    app.checkMod();
                });
                $('#btnBendahara').on('click', function() {
                    app.ubahBendahara();
                });
                $('#batas').on('change', function() {
                    $("#page").val(1);
                    app.loadTabel();
                });
                $('select').material_select();
            });            
        },

        loadRekap: function (id) {
            var data = {
                'pin_absen' : id,
                'bulan' : $('#bulan').val(),
                'tahun' : $('#tahun').val(),
                'jenis' : $('#jns').val(),
                'tingkat' : $('#tk').val(),
                'format' : $('#frmt').val(),
                'satker' : $('#lokasi').val(),
                'status' : $('#status').val()
            };

            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            var url = url_tabelrekap + 'c' + data.jenis;
            $.post(url, data, function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
                $('.btnVer').on('click', function() {
                    app.verify();
                });
                $('.btnMod').on('click', function() {
                    app.checkMod();
                });
            });
        },

    };
<!--</script>-->
