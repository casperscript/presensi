<?php
header('application/javascript');
?>
<!--<script>-->
    if (window.innerWidth >= 720) {
        app = {
            init: function (url) {
                url_tabel = url + "/tabelDesktop";
                url_info = url + "/detailModerasiDesktop/";
                url_form = url + "/formDesktop";
                url_hapus = url + "/hapus";
                url_hapus_dokumen = url + "/hapusDokumen";
                url_simpan = url + "/simpan";
                url_getJenisMod = url + "/getJenisModerasi/";
                url_getTahunMod = url + "/getTahunModerasi";
                url_getBulanMod = url + "/getBulanModerasi";

                modL = ["L1", "L2", "L3", "L4", "L5"];
            },

            loadTabel: function () {
                $("#progressView").attr("class", "indeterminate");
                $.post(url_tabel, $("#frmData").serialize(), function (data) {
                    $("#progressView").attr("class", "determinate");
                    $("#data-tabel").html(data);

                    $(".tooltipped").tooltip();
                });
            },

            infoModerasi: function (id) {
                $.get(url_info + id, function (result) {
                    $("#modalDetail .btnHapus").attr("id", id);
                    $("#modalDetail .btnEdit").attr("id", id);

                    $("#data-detail").html(result);
                    $("#modalDetail").openModal();
                    $(".tooltipped").tooltip();
                });
            },

            showForm: function (id) {
                $("#progressView").attr("class", "indeterminate");
                $.post(url_form, {id: id}, function (result) {
                    $("#progressView").attr("class", "deterimante");
                    $("#modalInput #data-form").html(result);
                    $("#modalInput").openModal();
                    Materialize.updateTextFields();
                    
                    app.toggleTglAkhir();
                });
            },

            showConfirm: function (id, title, msg) {
                swal({
                    title: title,
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Hapus data!",
                    closeOnConfirm: false
                }, function () {
                    $.post(url_hapus, {id: id}, function (response) {
                        if (response.status === "success") {
                            swal("Berhasil!", "Data telah terhapus.", "success");
                            $("#modalDetail").closeModal();
                            app.getTahunMod();
                            app.loadTabel();
                        } else {
                            swal(response.title, response.message, response.status);
                        }
                    }, "json");
                });
            },

            showConfirmDelDok: function (id, title, msg) {
                swal({
                    title: title,
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Hapus data!",
                    closeOnConfirm: false
                }, function () {
                    $.post(url_hapus_dokumen, {id: id}, function (response) {

                        if (response.status === "success") {
                            $(".listLampiran" + id).fadeOut(300);
                        }

                        swal(response.title, response.message, response.status);

                    }, "json");
                });
            },

            simpanFile: function (obj) {
                var data = new FormData($(obj)[0]);
                var fileLamp = $("#lampiran").get(0).files[0];

                if (fileLamp !== undefined && fileLamp.size > 2097152) { // Limiter file size before upload
                    swal({
                        title: "Peringatan Lampiran",
                        text: "Maksimal ukuran file lampiran adalah 2MB, silahkan ganti file yang lain atau perkecil ukuran",
                        type: "warning"
                    });
                } else { // Insert data to database

                    $.ajax({
                        type: "POST",
                        enctype: "multipart/form-data",
                        url: url_simpan,
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        timeout: 600000,
                        dataType: "JSON",
                        beforeSend: function () {
                            $("#simpanProgress").attr("class", "indeterminate");
                        },
                        success: function (response) {
                            console.log(response);
                            $("#simpanProgress").attr("class", "determinate");
                            if (response.status === "success") {
                                app.getTahunMod();
                                app.loadTabel();
                                $("#modalInput").closeModal();
                            }

                            swal({
                                title: response.title,
                                text: response.message,
                                type: response.status,
                                confirmButtonClass: "btn-primary",
                                confirmButtonText: "OK"
                            });
                        },
                        error: function (e) {
                            //
                        }
                    });
                }
            },

            pilJenisModerasi: function (kodeKatMod) {
                $.post(url_getJenisMod + kodeKatMod, function (result) {
                    $.each(result, function (i, item) {
                        $("#kode_presensi").append($("<option>", {
                            value: item.kode_presensi,
                            text: item.ket_kode_presensi
                        }));
                    });
                }, "json");
                $("#kode_presensi").material_select();
            },

            getTahunMod: function () {
                $("#tahun").empty();
                $.get(url_getTahunMod, function (result) {
                    $.each(result, function (i, item) {
                        $("#tahun").append($("<option>", {
                            value: item,
                            text: item
                        }));
                    });

                    //set default year
                    var tahun = new Date().getFullYear();
                    $("#tahun").val(tahun);
                    //alert($("#tahun").val());

                    $("select").material_select();
                    valTahun = $("#tahun").val();
                    app.getBulanMod(valTahun);

                }, "json");
            },

            getBulanMod: function (valTahun) {
                var active = $("#bulan").val();
                $("#bulan").empty();
                var tahun = new Date().getFullYear();
                var bulan = $("#selBulan").val();

                $.get(url_getBulanMod + "/" + valTahun, function (result) {
                    $.each(result, function (i, item) {
                        $("#bulan").append($("<option>", {
                            value: i,
                            text: item
                        }));
                    });
                    //if (tahun === valTahun) {
                        $("#bulan").val(bulan);
                    //}
                    $("select").material_select();
                }, "json");
            },

            toggleTglAkhir: function () {
                var valKodeMod = $("#kode_presensi").val();
                if (modL.includes(valKodeMod)) {
                    $(".tglAkhir").hide();
                    $("#tanggal_akhir").val("");
                } else {
                    $(".tglAkhir").show();
                }
            }
        };
    } else {
        app = {
            init: function (url) {
                url_tabel = url + "/tabelMobile";
                url_info = url + "/detailModerasiMobile/";
                url_form = url + "/formMobile";
                url_simpan = url + "/simpan";
                url_hapus = url + "/hapus";
                url_hapus_dokumen = url + "/hapusDokumen";
                url_getJenisMod = url + "/getJenisModerasi/";
                url_getTahunMod = url + "/getTahunModerasi";
                url_getBulanMod = url + "/getBulanModerasi";
            },

            loadTabel: function () {
                $("#progressView").attr("class", "indeterminate");
                $.post(url_tabel, $("#frmData").serialize(), function (data) {
                    $("#progressView").attr("class", "determinate");
                    $("#data-tabel").html(data);

                    $(".tooltipped").tooltip();
                });
            },

            infoModerasi: function (id) {
                $.get(url_info + id, function (result) {
                    $("#modalDetail .btnHapus").attr("id", id);
                    $("#modalDetail .btnEdit").attr("id", id);

                    $("#data-detail").html(result);
                    $("#modalDetail").openModal();
                    $(".tooltipped").tooltip();
                });
            },

            showForm: function (id) {
                $("#progressView").attr("class", "indeterminate");
                $.post(url_form, {id: id}, function (result) {
                    $("#progressView").attr("class", "deterimante");
                    $("#modalInput #data-form").html(result);
                    $("#modalInput").openModal();
                    Materialize.updateTextFields();
                });
            },

            showConfirm: function (id, title, msg) {
                swal({
                    title: title,
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Hapus data!",
                    closeOnConfirm: false
                }, function () {
                    $.post(url_hapus, {id: id}, function (response) {
                        if (response.status === "success") {
                            swal("Berhasil!", "Data moderasi telah terhapus.", "success");
                            $("#modalDetail").closeModal();
                            app.loadTabel();
                        } else {
                            swal("Gagal!", "Terjadi kesalahan ketika menghapus, coba lagi.", "warning");
                        }
                    }, "json");
                });
            },

            simpan: function (obj) {
                $("#simpanProgress").attr("class", "indeterminate");
                $.post(url_simpan, $(obj).serializeArray(), function (response) {

                    if (response.status === "success") {
                        app.loadTabel();
                        $("#modalInput").closeModal();
                    }

                    $("#simpanProgress").attr("class", "determinate");
                    swal(response.title, response.message, response.status);

                }, "json");
            },

            showConfirmDelDok: function (id, title, msg) {
                swal({
                    title: title,
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Hapus data!",
                    closeOnConfirm: false
                }, function () {
                    $.post(url_hapus_dokumen, {id: id}, function (response) {

                        if (response.status === "success") {
                            $(".listLampiran" + id).fadeOut(300);
                        }

                        swal(response.title, response.message, response.status);

                    }, "json");
                });
            },

            simpanFile: function (obj) {
                var data = new FormData($(obj)[0]);
                var fileLamp = $("#lampiran").get(0).files[0];

                if (fileLamp !== undefined && fileLamp.size > 2097152) { // Limiter file size before upload
                    swal({
                        title: "Peringatan Lampiran",
                        text: "Maksimal ukuran file lampiran adalah 2MB, silahkan ganti file yang lain atau perkecil ukuran",
                        type: "warning"
                    });
                } else { // Insert data to database

                    $.ajax({
                        type: "POST",
                        enctype: "multipart/form-data",
                        url: url_simpan,
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        timeout: 600000,
                        dataType: "JSON",
                        beforeSend: function () {
                            $("#simpanProgress").attr("class", "indeterminate");
                        },
                        success: function (response) {
                            console.log(response);
                            $("#simpanProgress").attr("class", "determinate");
                            if (response.status === "success") {
                                app.getTahunMod();
                                app.loadTabel();
                                $("#modalInput").closeModal();
                            }

                            swal({
                                title: response.title,
                                text: response.message,
                                type: response.status,
                                confirmButtonClass: "btn-primary",
                                confirmButtonText: "OK"
                            });
                        },
                        error: function (e) {
                            //
                        }
                    });
                }
            },

            pilJenisModerasi: function (kodeKatMod) {
                $.post(url_getJenisMod + kodeKatMod, function (result) {
                    $.each(result, function (i, item) {
                        $("#kode_presensi").append($("<option>", {
                            value: item.kode_presensi,
                            text: item.ket_kode_presensi
                        }));
                    });
                }, "json");
                $("#kode_presensi").material_select();
            },

            getTahunMod: function () {
                $("#tahun").empty();
                $.get(url_getTahunMod, function (result) {
                    $.each(result, function (i, item) {
                        $("#tahun").append($("<option>", {
                            value: item,
                            text: item
                        }));
                    });

                    //set default year
                    var tahun = new Date().getFullYear();
                    $("#tahun").val(tahun);
                    //alert($("#tahun").val());

                    $("select").material_select();
                    valTahun = $("#tahun").val();
                    app.getBulanMod(valTahun);

                }, "json");
            },

            getBulanMod: function (valTahun) {
                var active = $("#bulan").val();
                $("#bulan").empty();
                var tahun = new Date().getFullYear();
                var bulan = new Date().getMonth() + 1;

                $.get(url_getBulanMod + "/" + valTahun, function (result) {
                    $.each(result, function (i, item) {
                        $("#bulan").append($("<option>", {
                            value: i,
                            text: item
                        }));
                    });
                    if (tahun == valTahun) {
                        $("#bulan").val(active);
                    }
                    $("select").material_select();
                }, "json");
            }
        };
    }
