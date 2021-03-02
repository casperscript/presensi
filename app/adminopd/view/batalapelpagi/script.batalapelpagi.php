<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
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

                //bind
                $('#pilihtahun').on('change', function() {
                    app.loadTabel();
                });
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
                app.showPersonil();
                $("#frmInputModal").openModal();
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

            $(document).on("change", "#pin_absen", function () {
                var pin_absen = $('#pin_absen :selected').val();
                var kdlokasi = $('#kdlokasi').val();
                var tanggal_apel = $('#tanggal_apel').val();
                if (pin_absen && tanggal_apel)
                    app.showDataApel(pin_absen, tanggal_apel, kdlokasi);
            });
        },
        
        simpan: function () {
            $("#resultForm").html('<span class="green-text">Proses menyimpan ...</span>');
            $('#btnBatal').removeClass("waves-effect waves-light btn grey").addClass("btn disabled").attr('disabled', true);
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

        showPersonil: function () {
            var obj = $('#pin_absen');
            var kdlokasi = $('#kdlokasi').val();
            $.post(url_get_personil, {kdlokasi: kdlokasi}, function (data) {
                obj.html('');
                obj.append($('<option>').text('-- Pilih Personil --').attr('value', ''));
                $.each(data, function(i, value){
                    obj.append($('<option>').text(value).attr('value', i));
                });
                obj.select2();
            });
        },

        showDataApel (pin_absen, tanggal_apel, kdlokasi) {
            $.post(url_get_apel, {pin_absen: pin_absen, tanggal_apel: tanggal_apel, kdlokasi: kdlokasi}, function (data) {
                if (data == '1') {
                    $('#data-apel').html('<span class="red-text text-darken-2">Pembatalan apel sudah pernah diajukan.</span>');
                    $('#input-keterangan').hide();
                    $('#btnSubmitSimpan').hide();
                } else if (data == '2') {
                    $('#data-apel').html('<span class="red-text text-darken-2">Tidak ada record finger apel.</span>');
                    $('#input-keterangan').hide();
                    $('#btnSubmitSimpan').hide();
                } else if (data == '3') {
                    $('#data-apel').html('<span class="red-text text-darken-2">Tidak dapat mengajukan pembatalan apel karena Laporan Presensi bulan bersangkutan telah diverifikasi Admin OPD.</span>');
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