<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_update = url + "/update";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
        },
        
        // Load Tabel Jadwal
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $("#showSpinner").show();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#showSpinner").hide();
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
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
                $("#kdlokasi").material_select("update");
            });
        },
        
        updateMesin: function (id_mesin) {
            $(".progres_" + id_mesin).show();
            $.post(url_update, {id_mesin: id_mesin}, function (data) {
                try {
                    var data = JSON.parse(data);
                    swal(data.info, data.message, data.status);
                } catch (e) {
                    swal("Gagal", "Koneksi ke mesin tidak dapat dilakukan", "error");
                }

                $(".progres_" + id_mesin).hide();
                if (data.status == 'success') {
                    var last = data.last.last_update;
                    //yyyy-mm-dd
                    var nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    var bln = parseInt(last.substr(5, 2), 10) - 1;
                    var thn = last.substr(0, 4);
                    var tgl = last.substr(8, 2);

                    var last_update = tgl + " " + nama_bulan[bln] + " " + thn;
                    $("#lastUpdate" + id_mesin).text(last_update);
                    $(".success_" + id_mesin).show();
                } else {
                    $("#lastUpdate" + id_mesin).text(data.message);
                }
                //alert(data.status);
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