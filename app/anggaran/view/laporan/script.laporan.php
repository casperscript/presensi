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
            url_tabelrekaptpp = url + "/tabelrekaptpp";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_checkmod = url + "/checkmod";
            url_updatever = url + "/updateVerifikasi";
            url_get_pil = url + "/getPilLaporan";
            url_updatebendaharabc = url + "/updateBendaharabc";
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showPilSatker: function (id_kelompok, lokasi) {
            var obj = $('#kdlokasi');
            $.post(url_get_lokasi_kerja, {kd_kelompok_lokasi_kerja: id_kelompok}, function (data) {
                obj.html('');
                obj.append($('<option>').text('-- Pilih Satuan Kerja --').attr('value', ''));
                $.each(data, function(i, value){
                    obj.append($('<option>').text(value).attr('value', i));
                });
                obj.material_select();
            });
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
                var url = url_tabeltpp;
            } else if (format == 'REKAPTPP') {
                var url = url_tabelrekaptpp;
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
                $('#btnBendaharabc').on('click', function() {
                    app.ubahBendaharabc();
                });
                $('#batas').on('change', function() {
                    $("#page").val(1);
                    app.loadTabel();
                });
                $('select').material_select();
            });            
        },

        checkMod: function () {
            var verified = $('#unverified').val();
            if (verified > 0) {
                alert('Maaf, Anda belum bisa mengesahkan laporan karena ada moderasi yang belum Anda verifikasi.');
                window.location.href = url_mod;
            }
        },

        loadRekap: function (id) {
            var data = {
                'pin_absen' : id,
                'bulan' : $('#bulan').val(),
                'tahun' : $('#tahun').val(),
                'jenis' : $('#jns').val(),
                'tingkat' : $('#tk').val(),
                'format' : $('#frmt').val(),
                'kdlokasi' : $('#lokasi').val()
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

        verify: function () {
            $('.btnVer').html('Proses ...');
            $('.btnVer').removeClass("waves-effect waves-light btn orange").addClass("btn disabled").attr('disabled', true);

            $.post(url_updatever, $("#frmVer").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                alert(data.message);
                if (data.status == 'success') {
                    //$('#tingkat').material_select();
                    $('.btnTampil').trigger('click');
                }
            });    
        },

        loadPil: function () {
            var bulan = $('#pilihbulan option:selected').val();
            var tahun = $('#pilihtahun option:selected').val();

            $.post(url_get_pil, {bulan: bulan, tahun: tahun}, function (data) {
                $('#kdlokasi').html('');
                $('#kdlokasi').append($('<option>').text('-- Pilih Satuan Kerja --').attr('value', ''));
                $.each(data, function(i, value){
                    $('#kdlokasi').append($('<option>').text(value).attr('value', i));
                });
                $('#kdlokasi').material_select();
            });
        },

        ubahBendaharabc: function () {
            var pilih = $('#pilihbendahara option:selected').val();
            if (pilih == '')
                return;

            $('#btnBendahara').html('Proses ...');
            $('#btnBendahara').removeClass("waves-effect waves-light btn orange").addClass("btn disabled").attr('disabled', true);

            var id = $('#ini-bendahara #id_tpp').val();
            var nama = $('#pilihbendahara option:selected').html();

            $.post(url_updatebendaharabc, {nip_bendahara : pilih, nama_bendahara: nama, id: id}, function (data) {
                alert(data.message);
                $('#format').val('TPP');
                app.loadTabel();
            });   
        }
    };
<!--</script>-->
