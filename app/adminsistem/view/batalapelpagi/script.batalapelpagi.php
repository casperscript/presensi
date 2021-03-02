<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_verifikasi = url + "/verifikasi";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_get_personil = url + "/getPersonilFromLokasi";
            url_get_apel = url + "/getDataApel";
        },
        
        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $("#progress").show();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#progress").hide();
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showForm: function (id_batal_apel) {
            $.post(url_form, {id_batal_apel: id_batal_apel}, function (data) {
                $("#data-form-input").html(data);
                app.bind();
                $("#frmInputModal").openModal({
                    dismissible: false
                });
            });
            
        },

        bind: function () {
            $('select').material_select();
            $('#pin_absen').select2();
            $('#tanggal_apel').pickadate({
                selectMonths: true,
                selectYears: true,
                format: 'yyyy-mm-dd',
                onSet: function(context) {
                    $('#pin_absen').trigger('change');
                    if(context.select)
                        this.close();
                }
            });

            $(document).on("change", "#kelompok", function () {
                var dt = $('#kelompok :selected').val();
                app.showPilSatker(dt, '#kdlokasi');
            });

            $(document).on("change", "#kdlokasi", function () {
                var dt = $('#kdlokasi :selected').val();
                app.showPersonil(dt);
            });

            $(document).on("change", "#pin_absen", function () {
                var pin_absen = $('#pin_absen :selected').val();
                var tanggal_apel = $('#tanggal_apel').val();
                if (pin_absen && tanggal_apel)
                    app.showDataApel(pin_absen, tanggal_apel);
            });
        },
        
        simpan: function () {
            $("#resultForm").html('<span class="green-text">Proses menyimpan ...</span>');
            $('#btnBatal').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);
            $('#btnSubmitSimpan').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);

            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                window.setTimeout(function(){
                $("#resultForm").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){
                    $("#frmInputModal").closeModal();
                    $('#btnBatal').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $('#btnSubmitSimpan').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
                }, 3000);
                app.loadTabel();
            }, "json");
        },
        
        hapus: function (id) {
            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                window.setTimeout(function(){$("#resultConfirm").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){
                    $("#confirmModal").closeModal();
                    $("#btnConfirm").removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $("#btnBatalConfirm").removeClass("btn disabled").addClass("waves-effect waves-light btn grey").removeAttr('disabled');
                }, 3000);
                app.loadTabel();
            }, "json");
        },

        verifikasi: function (confirm) {
            if (confirm == 'terima')
                var status = '1';
            else if (confirm == 'tolak')
                var status = '2';

            obj = $("#id_confirm");
            $.post(url_verifikasi, {id : obj.val(), status: status, 'pin': obj.data('pin'), 'tanggal' : obj.data('tanggal')}, function(data){
                window.setTimeout(function(){
                $("#resultConfirm").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){
                    $("#confirmModal").closeModal();
                    $("#btnConfirm").removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $("#btnBatalConfirm").removeClass("btn disabled").addClass("waves-effect waves-light btn grey").removeAttr('disabled');
                }, 3000);
                app.loadTabel();
            }, "json");
        },

        showPilSatker: function (id_kelompok) {
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

        showPersonil: function (kdlokasi) {
            var obj = $('#pin_absen');
            $.post(url_get_personil, {kdlokasi: kdlokasi}, function (data) {
                obj.html('');
                obj.append($('<option>').text('-- Pilih Personil --').attr('value', ''));
                $.each(data, function(i, value){
                    obj.append($('<option>').text(value).attr('value', i));
                });
                obj.select2();
            });
        },

        showDataApel (pin_absen, tanggal_apel) {
            $.post(url_get_apel, {pin_absen: pin_absen, tanggal_apel: tanggal_apel}, function (data) {
                if (data == '1') {
                    $('#data-apel').html('<span class="red-text text-darken-2">Pembatalan apel sudah pernah diajukan.</span>');
                    $('#input-keterangan').hide();
                    $('#btnSubmitSimpan').hide();
                } else if (data == '2') {
                    $('#data-apel').html('<span class="red-text text-darken-2">Tidak ada record finger apel.</span>');
                    $('#input-keterangan').hide();
                    $('#btnSubmitSimpan').hide();
                } else {
                    $('#data-apel').html(data);
                    $('#input-keterangan').show();
                    $('#btnSubmitSimpan').show();
                }
            });
        }
    };