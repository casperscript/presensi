<?php header('application/javascript'); ?>
<!-- <script> -->
    app = {
        init: function (url) {
            url_tabelpresensi = url + "/tabelpresensi";
            url_tabelapel = url + "/tabelapel";
            url_tabelmasuk = url + "/tabelmasuk";
            url_tabelpulang = url + "/tabelpulang";
            url_tabelpersonil = url + "/tabelpersonil";
            url_tabelrekap = url + "/tabelrekap";
            url_tabeltpp = url + "/tabeltpp";
            url_tabellist = url + "/tabellist";
            url_tabellisttpp = url + "/tabellisttpp";
            url_backup = url + "/dobackup";
            url_hapus = url + "/hapus";
            url_backuppresensi = url + "/savePresensi";
            url_saveLogTPP = url + "/saveLogTPP";
        },

        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        loadTabelList: function (page, tipe) {
            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            $.post(url_tabellist, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);

                $('.btnBackup').on('click', function () {
                    app.doBackup($(this));
                });
                $('.btnHapus').on('click', function () {
                    app.hapus($(this));
                });

                $('.btnPresensi').on('click', function () {
                    app.backuppresensi($(this));
                });

                var ini = $('#listTable').DataTable();
                var itu = $('#listTablenot').DataTable();

                if (page !== undefined) {
                    //$('#listTable_paginate').find('.paginate_button [data-dt-idx='+page+']').click();
                    for (var i = 1; i < page; i++) {
                        if (tipe === 'listTable') {
                            ini.page('next').draw('page');
                        } else if (tipe === 'listTablenot') {
                            itu.page('next').draw('page');
                        }
                    }
                }
            });
        },

        loadTabelListTPP: function () {
            $("#data-tabel").html("");
            $("#progress").removeAttr('style');
            $.post(url_tabellisttpp, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);

                if (page !== undefined) {
                    //$('#listTable_paginate').find('.paginate_button [data-dt-idx='+page+']').click();
                    for (var i = 1; i < page; i++) {
                        if (tipe === 'listTable') {
                            ini.page('next').draw('page');
                        } else if (tipe === 'listTablenot') {
                            itu.page('next').draw('page');
                        }
                    }
                }
            });
        },

        // Load Tabel
        loadTabel: function () {
            var format = $('#format option:selected').val();
            var jenis = $('#jenis option:selected').val();

            $('#download').val(0);
            var form = $("#frmData").serialize();
            if (format === 'C') {
                var url = url_tabelpersonil;
                var batas = $('#batas option:selected').val();
                if (batas === undefined)
                    batas = 10;

                var cari = $('#cari').val();
                if (cari === undefined)
                    cari = '';

                form += '&batas=' + batas + '&cari=' + cari;
            } else if (format === 'TPP') {
                if ($('#tpp').val() === 'tpp13')
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
                $('#batas').on('change', function () {
                    $("#page").val(1);
                    app.loadTabel();
                });
                $('select').material_select();
            });
        },

        loadRekap: function (id) {
            var data = {
                'pin_absen': id,
                'bulan': $('#bulan').val(),
                'tahun': $('#tahun').val(),
                'jenis': $('#jns').val(),
                'tingkat': $('#tk').val(),
                'format': $('#frmt').val(),
                'kdlokasi': $('#kdlokasi').val(),
                'status': $('#status').val()
            };

            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            var url = url_tabelrekap + 'c' + data.jenis;
            $.post(url, data, function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
            });
        },

        doBackup: function (obj) {
            var frm = {
                kdlokasi: obj.data('kdlokasi'),
                bulan: $('#bln').val(),
                tahun: $('#thn').val(),
                page: $("#listTablenot_paginate .paginate_button.current").html()
            };

            swal({
                title: "Proses Backup",
                text: "Sedang memproses... harap tunggu..",
                showConfirmButton: false
            });

            $.post(url_backup, frm)
                    .done(function (data) {
                        if (data.status === 'success') {
                            swal("Sukses!", "" + data.message + "", "success");
                        } else if (data.status === 'error') {
                            swal("Gagal!", "" + data.message + "", "error");
                        } else {
                            swal("Gagal!", "Terjadi kesalahan respon data", "error");
                        }
                        app.loadTabelList(data.page, 'listTablenot');
                    })
                    .error(function () {
                        swal("Error", "Permintaan gagal diproses", "error");
                    });
        },

        saveLogTPP: function (id) {
            swal({
                title: "Proses Backup",
                text: "Sedang memproses... harap tunggu..",
                showConfirmButton: false
            });

            $.post(url_saveLogTPP, {id: id})
                .done(function (data) {
                    swal("Berhasil", "Data telah dibackup", "success");
                    app.loadTabelListTPP();
                })
                .error(function () {
                    swal("Error", "Permintaan gagal diproses", "error");
                });
        },

        hapus: function (obj) {
            var frm = {
                kdlokasi: obj.data('kdlokasi'),
                lokasi: obj.data('lokasi'),
                bulan: $('#bln').val(),
                tahun: $('#thn').val()
            };

            var namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            swal({
                title: '',
                text: "Anda yakin akan menghapus backup laporan " + frm.lokasi + " Bulan " + namabulan[frm.bulan - 1] + " Tahun " + frm.tahun + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00c853",
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                closeOnConfirm: false
            }, function (isConfirm) {
                if (isConfirm) {
                    swal({
                        title: "Proses Hapus Backup",
                        text: "Sedang memproses... harap tunggu..",
                        showConfirmButton: false
                    });

                    $.post(url_hapus, frm, function (data) {
                        if (data.status === 'success') {
                            swal("Sukses!", "" + data.message + "", "success");
                        } else if (data.status === 'error') {
                            swal("Gagal!", "" + data.message + "", "error");
                        }

                        app.loadTabelList();
                    });
                }
            });
        },

        hapusLogTPP: function (id) {
            alert(id);
        },

        backuppresensi: function (obj) {
            var frm = {
                kdlokasi: obj.data('kdlokasi'),
                bulan: $('#bln').val(),
                tahun: $('#thn').val(),
                page: $("#listTable_paginate .paginate_button.current").html()
            };

            swal({
                title: "Proses Backup Data Presensi",
                text: "Sedang memproses... harap tunggu..",
                showConfirmButton: false
            });

            $.post(url_backuppresensi, frm, function (data) {
                if (data.status === 'success') {
                    swal("Sukses!", "" + data.message + "", "success");
                } else if (data.status === 'error') {
                    swal("Gagal!", "" + data.message + "", "error");
                }

                app.loadTabelList(data.page, 'listTable');
            });
        }
    };