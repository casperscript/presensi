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
            url_tabeltpp13 = url + "/tabeltpp13";
            url_tabeltpp14 = url + "/tabeltpp14";
            url_verifikasi = url + "/verifikasi";
            url_checkmod = url + "/checkmod";
            url_updatever = url + "/updateVerifikasi";
            url_updatebendahara = url + "/updateBendahara";
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        // Load Tabel
        loadTabel: function () {
            var format = $('#format option:selected').val();
            var jenis = $('#jenis option:selected').val();
            var tingkat = $('#tingkat option:selected').val();

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
                else if ($('#tpp').val() == 'tpp14')
                    var url = url_tabeltpp14;
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
                $('#btnMod').on('click', function() {
                    app.checkMod(tingkat);
                });
                $('#btnBendahara').on('click', function() {
                    app.ubahBendahara();
                });
                $('#batas').on('change', function() {
                    $("#page").val(1);
                    app.loadTabel();
                });
                $('select').material_select();
                $('#btnCetak').on('click', function() {
                    $('#frmData').submit();
                });
            });            
        },

        loadVerifikasi: function() {
            $("#progress").removeAttr('style');
            $("#data-tabel").html("");
            $.post(url_verifikasi, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
                $('.btnVer').on('click', function() {
                    app.verify();
                });
                $('.btnMod').on('click', function() {
                    app.checkMod();
                });
                $('select').material_select();
            }); 
        },

        checkMod: function (tingkat) {
            if (tingkat == 2) {
                alert('Maaf, Anda belum bisa cetak laporan DESEMBER 2018 karena ada moderasi s/d tgl 14 yang belum Anda verifikasi.');
                window.location.href = url_mod;
            } else (tingkat == 3)
                alert('Maaf, Anda belum bisa cetak laporan DESEMBER 2018 karena ada moderasi s/d tgl 14 yang belum KEPALA OPD verifikasi.');
        },

        loadRekap: function (id) {
            var data = {
                'pin_absen' : id,
                'bulan' : $('#bulan').val(),
                'tahun' : $('#tahun').val(),
                'jenis' : $('#jns').val(),
                'tingkat' : $('#tk').val(),
                'format' : $('#frmt').val(),
                'satker' : $('#lokasi').val()
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
                            //$('#tingkat').material_select();
                            swal("Sukses!", ""+data.message+"", "success");
                            $('.btnTampil').trigger('click');
                        } else if (data.status == 'error')
                            swal("Gagal!", ""+data.message+"", "error");
                    });    
                } 
            });
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
<!--</script>-->
