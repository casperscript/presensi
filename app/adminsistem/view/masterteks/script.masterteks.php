<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
        },
        
        // Load Tabel
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
            console.clear();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
            console.clear();
        },

        showForm: function (kd_teks) {
            $.post(url_form, {kd_teks: kd_teks}, function (data) {
                $("#data-form-input").html(data);
                $('select').material_select();
                $("#frmInputModal").openModal();
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
    };
<!--</script>-->