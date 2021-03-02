<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_mod = url + "/../HusnanWModerasi/daftarVerMod/";
            url_tabelpresensi = url + "/tabelpresensi";
            url_tabelapel = url + "/tabelapel";
            url_tabelmasuk = url + "/tabelmasuk";
            url_tabelpulang = url + "/tabelpulang";
            url_tabelpersonil = url + "/tabelpersonil";
            url_tabelrekap = url + "/tabelrekap";
            url_tabeltpp = url + "/tabeltpp";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_checkmod = url + "/checkmod";
            url_updatever = url + "/updateVerifikasi";
            url_get_pil = url + "/getPilLaporan";
            url_loadVer = url + "/loadVerifikasi";
            url_tabelindex = url + "/tabelindex";
            url_tabelverifikasi = url + "/tabelverifikasi";
            url_tabelbatal = url + "/tabelbatal";
            url_prosesbatal = url + "/prosesbatal";
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
            } else if (format == 'TPP')
                var url = url_tabeltpp;
            else {
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
                $('#batas').on('change', function() {
                    $("#page").val(1);
                    app.loadTabel();
                });
                $('select').material_select();
            });            
        },

        checkMod: function () {
            var verified = $('#unverified').val();
            var kdlokasi = $('#kdlokasi').val();
            if (verified > 0) {
                alert('Maaf, Anda belum bisa mengesahkan laporan karena ada moderasi yang belum Anda verifikasi.');
                window.location.href = url_mod+kdlokasi;
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
            var satker = $('#satker').val();
            var namabulan = $('#namabulan').val();
            var tahun = $('#tahun').val();
            swal({   
                title: '',
                text: "Dengan ini, Saya menandatangani secara elektronik laporan kehadiran/ketidakhadiran (Masuk Kerja/Apel/Pulang Kerja) "+satker+" Bulan "+namabulan+" Tahun "+tahun+" sebagai tanda telah verifikasi dan pengesahan.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00c853",
                confirmButtonText: "Setuju, tanda tangani",
                cancelButtonText: "Batal, kembali ke laporan",
                closeOnConfirm: false,
            }, function(isConfirm){
                if (isConfirm) {
                    $('.btnVer').html('Proses ...');
                    $('.btnVer').removeClass("waves-effect waves-light btn orange").addClass("btn disabled").attr('disabled', true);

                    $.post(url_updatever, $("#frmVer").serialize(), function (data) {
                        $('#progress').attr('style', 'display: none');
                        //alert(data.message);
                        if (data.status == 'success') {
                            swal("Sukses!", ""+data.message+"", "success");
                            app.loadTabelverifikasi();
                        } else if (data.status == 'error')
                            swal("Gagal!", ""+data.message+"", "error");
                    });    
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

        loadIndex: function(lokasi) {
            var bulan = $('#pilihbulan option:selected').val();
            var tahun = $('#pilihtahun option:selected').val();

            $('#showIndex').html('');
            $('#progress').removeAttr('style');
            $.post(url_tabelindex, {bulan: bulan, tahun: tahun}, function (data) {
                setTimeout(function(){ 
                    $('#progress').attr('style', 'display: none');
                    $('#showIndex').html(data);

                    $('.btnLap').on('click', function(e) {
                        e.preventDefault();
                        var lokasi = $(this).data('lokasi');
                        var bulan = $(this).data('bulan');
                        var tahun = $(this).data('tahun');
                        app.loadVerifikasi(lokasi, bulan, tahun);
                    });
                }, 700);
            });    
        },

        loadVerifikasi: function(lokasi, bulan, tahun) {
            $('#showIndex').html('');
            $('#filterIndex').html('');
            $.post(url_loadVer, {kdlokasi: lokasi, bulan: bulan, tahun: tahun}, function (data) {
                $('#progress').attr('style', 'display: none');
                $('#showIndex').html(data);
                $('select').material_select();
            });    
        },

        loadTabelverifikasi: function () {
            $("#progress").removeAttr('style');
            $("#data-tabel").html("");
            $.post(url_tabelverifikasi, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
                $('.btnVer').on('click', function() {
                    app.verify();
                });
                $('select').material_select();
            }); 
        },

        loadTabelbatal: function () {
            $("#progress").removeAttr('style');
            $("#data-tabel").html("");
            $.post(url_tabelbatal, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
                $('select').material_select();
                $('.btnBatal').on('click', function() {
                    var input = {
                        'satker' : $(this).data('satker'),
                        'kdlokasi' : $(this).data('kdlokasi'),
                        'bulan' : $(this).data('bulan'),
                        'namabulan' : $(this).data('namabulan'),
                        'tahun' : $(this).data('tahun')
                    };
                    app.batalkan(input);
                });
            }); 
        },

        batalkan: function (input) {
            swal({   
                title: '',
                text: "Anda yakin akan membatalkan verifikasi laporan kehadiran/ketidakhadiran (Masuk Kerja/Apel/Pulang Kerja) "+input.satker+" Bulan "+input.namabulan+" Tahun "+input.tahun+"? Verifikasi Laporan akan diulang dari tingkat Admin OPD.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00c853",
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                closeOnConfirm: false,
            }, function(isConfirm){
                if (isConfirm) {
                    $.post(url_prosesbatal, input, function (data) {
                        $('#progress').attr('style', 'display: none');
                        //alert(data.message);
                        if (data.status == 'success') {
                            swal("Sukses!", ""+data.message+"", "success");
                            app.loadTabelbatal();
                        } else if (data.status == 'error')
                            swal("Gagal!", ""+data.message+"", "error");
                    });
                } 
            });
        }
    };
<!--</script>-->