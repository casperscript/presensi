function husnanw_moderasi_main_kepala_bkppd(route, verModKodeLokasi) {   
    $(".btn-batal").click(function () {
        window.location.reload();
    });

    $(document).on("click", ".btn-sahkan-mod", function () {
        var mid = $(this).attr("mid");
        var catatan = $("#txtCatatan").val();
        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:2, catatan: catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip orange white-text">DISAHKAN</span>';
                alert("Anda telah melakukan verifikasi dengan status: DISAHKAN!");
                $("#tdKepKota").html(modStyle);
                $("#tdKepKota-" + mid).html(modStyle);
                $("#tdKepKotaDate").text(result.date["dt_flag_kepala_kota"]);
                $("#tdPerubahanTerakhir").text(result.date["dt_flag_kepala_kota"]);
                $("#tdCatatanKepKota").text(catatan);

                if ($.trim(catatan) === "") {
                    window.location.reload();
                }

                ajaxRequester(route + "/getTotalBelumVerifikasi/" + verModKodeLokasi, {}, function (result) {
                    $("#bTotal").text(result.total);
                }, "get", "json");
            } else {
                if (result.reload === "1") {
                    alert("Sistem mendeteksi perubahan status pengesahan Kepala OPD. Sistem akan memuat ulang halaman...");
                    window.location.reload();
                } else {
                    alert("Sistem gagal melakukan pengesahan moderasi!");
                }
            }
        }, "post", "json");
    });

    ajaxRequester(route + "/getDaftarOpd", {}, function (result) {
        $.each(result, function(index, val) {
            $('#selDaftarOpdVerMod').append($('<option>', { 
                value: val.kdlokasi,
                text : val.singkatan_lokasi
            }));
        });
    
        $('select').material_select();
    }, "get", "json");

    $("#selDaftarOpdVerMod").change(function () {
        var kdlokasi = $(this).val();
        var status = $(this).attr("isfinal") === "1" ? "final" : "";
        loadPage(route, "loadTabelVerMod/" + kdlokasi + "X1X"+status, {}, "divTabelVerMod", function (data) {
            
        });
    });

    $(document).on("click", ".btn-info-mod", function () {
        var mid = $(this).attr("mid");
        var action = $(this).attr("isfinal") === "true" ? "infoModerasiHasil" : "infoModerasi";
        loadPage(route, action + "/" + mid, {}, "divModalBody", function (data) {
            $('#divModalInfoModerasi').openModal('open');
        });
    });

    $("#btnTerapkanMassLegit").click(function (e) {
        e.preventDefault();

        if (!confirm("Yakinkah Anda ingin mengesahkan semua moderasi yang telah Anda pilih sebelumnya?")) {
            return false;
        }

        var mids = $("#hidCheckedMods").val();
        var catatan = $("#txtCatatanMassLegit").val();
        var kdlokasi = $("#kdlokasi").val(); //added by daniek

        swal({
            title: "Proses Pengesahan",
            type: "warning",
            text: "Sedang memproses.. Harap sabar menunggu",
            closeOnEsc: false,
            closeOnClickOutside: false,
            showCancelButton: false,
            showConfirmButton: false
        }); //added by daniek

        //ajaxRequester(route + "/updateModerasiMassLegit", {mids:mids, flag:2, catatan: catatan}, function (result) { == edited by daniek
        ajaxRequester(route + "/updateModerasiMassLegit", {mids:mids, flag:2, catatan: catatan, kdlokasi: kdlokasi}, function (result) {
            swal.close();
            if (result.status === "success") {
                alert("Selamat! Anda telah mengesahkan beberapa moderasi yang telah dipilih secara massal!");
                window.location.reload();
            } else {
                alert("Sistem gagal melakukan pengesahan moderasi secara massal! Hal ini kemungkinan diakibatkan moderasi yang telah Anda sahkan sebelumnya telah diproses Kepala OPD. Sistem akan memuat ulang halaman untuk memastikan Anda mendapatkan pembaharuan terbaru...");
                window.location.reload();
            }
        }, "post", "json");
    });

    $("#btnMassLegit").click(function () {
        
        if ($(this).hasClass("disabled")) {
            return false;
        }

        var checkedMods = [];

        $(".check-all-mod").each(function (k, v) {
            if ($(this).is(":checked")) {
                checkedMods.push($(this).val());
            }
        });

        ajaxRequester(route + '/massLegitPage', {checkedMods: checkedMods}, function(html) {
            $("#divModalBodyMassLegit").html(html);
            $('#divModalMassLegit').openModal('open');
        }, "post", "html");
    });

    $(document).on("click", "#chkCheckAllMod", function () {
        if ($(this).is(":checked")) {
            //hide by daniek -- alert("Perhatian: Fitur Check All hanya berlaku untuk tiap halamannya. Anda perlu melakukan check All untuk tiap halaman jika Anda menghendaki Check All untuk semua daftar yang ada. Untuk pindah halaman klik nomor halaman di bagian bawah halaman ini.");
            $(".check-all-mod").each(function (k, v) {
                if (!$(this).prop("disabled")) {
                    $(this).prop("checked", true);
                }
            });       
        } else {
            $(".check-all-mod").prop("checked", false);
        }

        checkModHandler(".check-all-mod", "#btnMassLegit");
    });

    $(document).on("click", ".check-all-mod", function () {
        var isCheckedAll = true;

        if (!$(this).is(":checked")) {
            $("#chkCheckAllMod").prop("checked", false);
        } else {
            $(".check-all-mod").each(function (k, v) {
                if (!$(this).is(":checked") && !$(this).prop("disabled")) {
                    $("#chkCheckAllMod").prop("checked", false);
                    isCheckedAll = false;
                    return false;
                }
            });

            if (isCheckedAll) {
                $("#chkCheckAllMod").prop("checked", true);
            }
        }

        checkModHandler(".check-all-mod", "#btnMassLegit");
    });

    $('a.modal-close').click(function (ev) {
        ev.preventDefault();
    });

    $("#divTabelVerMod").on("click", ".paging", function () {
        $("#page").val($(this).attr("number-page"));
        $("#btnMassLegit").removeClass("disabled").addClass("disabled");
        loadPage(route, "loadTabelVerMod/" + verModKodeLokasi + "X" +$("#page").val(), {}, "divTabelVerMod", function (data) {
                
        });
    });
}

function checkModHandler(className, targetEl) {
    var nChecked = 0;
    $(className).each(function (k, v) {
        if ($(this).is(":checked")) {
            nChecked++;
        }
    });

    if (nChecked > 1) {
        $(targetEl).removeClass("disabled");
    } else {
        $(targetEl).removeClass("disabled").addClass("disabled");
    }
}

//loadPage(route, "welcome", {}, "divContent", function (data) {});

function loadPage(route, action, data, elTargetId, callback) {
    ajaxRequester(route + '/' + action, data, function(html) {
        $("#" + elTargetId).html(html);
        callback(data);
    }, "get", "html");
}

function ajaxRequester(route, data, callback, type, dataType) {
    if (typeof type === "undefined") {
        type = "get";
    }

    if (typeof dataType === "undefined") {
        dataType = "json";
    }
    
    $.ajax({
        url: route,
        data: data,
        type: type,
        dataType: dataType
    }).done(function(result) {
        callback(result);
    }).fail(function() {
        console.log("WARNING: error on ajax request");
        if (confirm("Sistem mengalami gangguan teknis. Disarankan untuk memuat ulang halaman. Lanjutkan untuk memuat ulang halaman?")) {
            window.location.reload();
        }
    }).always(function() {
        console.log("(C) 2017 - Developed by Husnan W");
    });
}
