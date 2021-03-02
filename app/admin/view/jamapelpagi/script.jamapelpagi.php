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
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
        },

        showForm: function (id_jam_apel) {
            $.post(url_form, {id_jam_apel: id_jam_apel}, function (data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal({
                    dismissible: false
                });
            });
        },
        
        simpan: function () {
            $("#resultForm").html('<span class="green-text">Proses menyimpan ...</span>');
            $('#btnBatal').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);
            $('#btnSimpan').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);

            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                window.setTimeout(function(){
                $("#resultForm").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){
                    $("#frmInputModal").closeModal();
                    $('#btnBatal').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                    $('#btnSimpan').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
                }, 3000);
                app.loadTabel();
            }, "json");
        },
        
        hapus: function () {
            $("#resultHapus").html('<span class="green-text">Proses menghapus ...</span>');
            $('#btnBatalHapus').removeClass("waves-effect waves-light btn green").addClass("btn disabled").attr('disabled', true);
            $('#btnConfirmHapus').removeClass("waves-effect waves-light btn red").addClass("btn disabled").attr('disabled', true);

            $.post(url_hapus, $("#frmHapus").serialize(), function(data){
                window.setTimeout(function(){$("#resultHapus").html('<span class="blue-text">'+data.message+'</span>');}, 2000);
                window.setTimeout(function(){
                    $("#confirmModal").closeModal();
                    $('#btnBatalHapus').removeClass("btn disabled").addClass("waves-effect waves-light btn green").removeAttr('disabled');
                    $('#btnConfirmHapus').removeClass("btn disabled").addClass("waves-effect waves-light btn red").removeAttr('disabled');
                }, 3000);
                app.loadTabel();
            }, "json");
        },
    };
<!--</script>-->