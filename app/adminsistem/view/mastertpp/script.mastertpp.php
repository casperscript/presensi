<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_kecuali = url + "/kecuali";
            url_form_kecuali = url + "/formKecuali";
            url_pil_pegawai = url + "/pilpegawai";
            url_simpan_kecuali = url + "/simpanKecuali";
            url_hapus_kecuali = url + "/hapusKecuali";
        },
        
        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $(".btnBack").addClass("hide");
            $("#progressView").attr("class", "indeterminate");
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $(".btnForm").removeClass("hide");
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
                $("#progressView").attr("class", "determinate");
            });
            console.clear();
        },

        loadNot: function (id) {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $(".btnForm").addClass("hide");
            $("#progressView").attr("class", "indeterminate");
            $.post(url_kecuali, {kd_tpp: id}, function (data) {
                $(".btnBack").removeClass("hide");
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
                $("#progressView").attr("class", "determinate");
            });
            console.clear();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
            console.clear();
        },

        showForm: function (kd_tpp) {
            $.post(url_form, {kd_tpp: kd_tpp}, function (data) {
                $("#data-form-input").html(data);
                $('select').material_select();
                $("#frmInputModal").openModal();
            });
            console.clear();
        },

        showFormKecuali: function (kd_tpp) {
            $.post(url_form_kecuali, {kd_tpp: kd_tpp}, function (data) {
                $("#data-form-input-kecuali").html(data);
                $('select').material_select();
                $("#frmKecualiModal").openModal();
            });
            console.clear();
        },
        
        simpan: function () {
            $("#resultForm").html('<span class="green-text">Proses menyimpan ...</span>');
            $('#btnBatal').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);
            $('#btnSubmitSimpan').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);

            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                window.setTimeout(function(){$("#resultForm").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                
                window.setTimeout(function(){
                    $("#frmInputModal").closeModal();
                    $('#btnBatal').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $('#btnSubmitSimpan').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
                    $("#resultForm").html('');
                }, 2500);

                app.loadTabel();
            }, "json");
            console.clear();
        },
        
        hapus: function () {
            $("#resultHapus").html('<span class="green-text">Proses menghapus ...</span>');
            $('#btnBatalHapus').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);
            $('#btnConfirmHapus').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);

            $.post(url_hapus, {id : $("#id_confirm").val()}, function(data){
                window.setTimeout(function(){$("#resultHapus").html('<span class="blue-text">'+data.message+'</span>');}, 2000);

                window.setTimeout(function(){
                    $("#confirmModal").closeModal();
                    $('#btnBatalHapus').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
                    $('#btnConfirmHapus').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $("#resultHapus").html('');
                }, 2500);
                app.loadTabel();
            }, "json");
            console.clear();
        },

        pilPegawai: function (kdlokasi) {
            var $select = $("#kdlokasi");
            $("#nipbaru").html("<option value='' disabled>-- PILIH PEGAWAI --</option>").material_select("update");
            $("#nipbaru").attr("disabled",true).material_select("update");
            
            $("#progressPersonil").removeClass("hide");
            $('#btnBatalKecuali').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);
            $('#btnSimpanKecuali').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);

            $.post(url_pil_pegawai, {kdlokasi: kdlokasi}, function (data) {
                $("#nipbaru").removeAttr("disabled");
                $("#nipbaru").html(data).material_select("update");
                $("#progressPersonil").addClass("hide");
                $('#btnBatalKecuali').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                $('#btnSimpanKecuali').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
            });
            console.clear();
        },

        simpanKecuali: function () {
            $("#resultFormKecuali").html('<span class="green-text">Proses menyimpan ...</span>');
            $('#btnBatalKecuali').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);
            $('#btnSimpanKecuali').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);

            var id = $('#kd_tpp').val();
            $.post(url_simpan_kecuali, $("#frmInputKecuali").serialize(), function(data){
                window.setTimeout(function(){$("#resultFormKecuali").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                
                window.setTimeout(function(){
                    $("#frmKecualiModal").closeModal();
                    $('#btnBatalKecuali').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $('#btnSimpanKecuali').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
                    $("#resultFormKecuali").html('');
                }, 2500);

                app.loadNot(id);
            }, "json");
            console.clear();
        },

        hapuskecuali: function (id, kd_tpp) {
            $.post(url_hapus_kecuali, {id : id, kd_tpp: kd_tpp}, function(data){
                app.loadNot(kd_tpp);
            }, "json");
            console.clear();
        },
    };
<!--</script>-->