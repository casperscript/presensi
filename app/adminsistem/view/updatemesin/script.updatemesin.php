<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
            url_form = url + "/form";
            url_formImport = url + "/formImport";
            url_simpan = url + "/simpan";
            url_hapus = url + "/hapus";
            url_update = url + "/update";
            url_get_lokasi_kerja = url + "/getPilLokasiFromKelLokasi";
            url_importFile = url + "/import";
        },
        
        // Load Tabel Jadwal
        loadTabel: function () {
            $("#data-tabel").fadeOut().promise().then( function () {
                $("#showSpinner").fadeIn().promise().then( function () {
                    $.post(url_tabel, $("#frmData").serialize(), function (data) {
                        $("#showSpinner").fadeOut().promise().then( function() {
                            $("#data-tabel").html(data);
                            $("#data-tabel").fadeIn(300);
                        });
                    });
                });
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

        showFormImport: function (id_mesin) {
            $(".viewProgress").attr("class", "indeterminate");
            $.post(url_formImport, {id_mesin: id_mesin}, function (data) {
                $(".viewProgress").attr("class", "determinate");
                $("#frmInput #data-form").html(data);
                $("#modalInput").openModal();
                $(".btnFooter").show();
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
            $(".btnUpdate" + id_mesin).prop("disabled", true);
            $.post(url_update, {id_mesin: id_mesin}, function (data) {
                try {
                    var data = JSON.parse(data);
                    swal(data.info, data.message, data.status);
                } catch (e) {
                    swal("Gagal", "Koneksi ke mesin tidak dapat dilakukan", "error");
                }

                $(".progres_" + id_mesin).hide();
                $(".btnUpdate" + id_mesin).prop("disabled", false);

                if (data.status == 'success') {
                    //var last = data.last.last_update;
                    //yyyy-mm-dd
                    //var nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    //var bln = parseInt(last.substr(5, 2), 10) - 1;
                    //var thn = last.substr(0, 4);
                    //var tgl = last.substr(8, 2);

                    $("#lastUpdate" + id_mesin).html(data.last);
                    $(".success_" + id_mesin).show();
                } else {
                    $("#lastUpdate" + id_mesin).html(data.last);
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

        importFile: function (obj) {
            $("#viewProgress").attr("class", "indeterminate");
            var xhr = new XMLHttpRequest();
            var formData = new FormData();
            var file = $("#file_import").get(0).files[0];
            if (file !== undefined) {
                formData.append("file_import", $("#file_import").val());
                formData.append("id_mesin", $("#id_mesin").val());
                formData.append("file", file);
                xhr.open("POST", url_importFile);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        var response = JSON.parse(xhr.responseText);
                        swal({
                            title: response.title,
                            text: response.text,
                            type: response.type,
                            confirmButtonClass: "btn-primary",
                            confirmButtonText: "OK"
                        },
                        function () {
                            $("#modalInput").closeModal();
                        });
                    }
                };

                xhr.send(formData);
                $("#viewProgress").attr("class", "determinate");
            } else {
                alert("File tidak ada");
            }
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