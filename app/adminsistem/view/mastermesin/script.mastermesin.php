<?php header('application/javascript'); ?>
<!-- <script> -->
    app = {
        init: function(url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
        },

        loadTabel: function() {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $.post(url_tabel, $("#frmData").serialize(), function(data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
            console.clear();
        },

        tabelPagging: function(number) {
            $("#page").val(number);
            this.loadTabel();
            console.clear();
        },

        showForm: function(id_mesin) {
            $.post(url_form, {
                id_mesin: id_mesin
            }, function(data) {
                $("#data-form-input").html(data);
                $("#frmInputModal").openModal();
            });
            console.clear();
        },

        simpan: function() {
            $.post(url_simpan, $("#frmInput").serialize(), function(data) {
                $("#frmInputModal").closeModal();
                app.loadTabel();
            }, "json");
            console.clear();
        },

        hapus: function(id, title, msg) {
            swal({
                title: title,
                text: msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Hapus data",
                closeOnConfirm: false
            }, function() {
                $.post(url_hapus, {
                    id: id
                }, function(response) {
                    swal(response.title, response.message, response.status);
                    if (response.status === "success") {
                        app.loadTabel();
                    }
                }, "json");
            });
        },

        xhapus: function() {
            $.post(url_hapus, {
                id: $("#id_confirm").val()
            }, function(data) {
                $("#confirmModal").closeModal();
                app.loadTabel();
            }, "json");
            console.clear();
        },
    }; 
    <!--</script>-->