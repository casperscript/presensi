function husnanw_moderasi_main_kepala_opd(route) {   
    $(".btn-batal").click(function () {
        window.location.reload();
    });

    $(document).on("click", ".btn-terima-mod", function () {
        var mid = $(this).attr("mid");
        var catatan = typeof $("#txtCatatan").val() === "undefined" ? "-" : $("#txtCatatan").val();
        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:1, catatan:catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip green white-text">DITERIMA</span>';
                alert("Anda telah melakukan verifikasi dengan status: DITERIMA!");
                $("#tdKepOpd").html(modStyle);
                $("#tdKepOpd-" + mid).html(modStyle);
                $("#tdKepOpdDate").text(result.date["dt_flag_kepala_opd"]);
                $("#tdCatatanKepOpd").text(catatan);                
            } else {
                if (result.reload === "1") {
                    alert("Sistem mendeteksi perubahan status pengesahan Admin Kota atau Kepala BKPPD. Sistem akan memuat ulang halaman...");
                    window.location.reload();
                } else {
                    alert("Sistem gagal merubah status verifikasi!");
                }            
            }
        }, "post", "json");
    });

    $(document).on("click", ".btn-sahkan-mod", function () {
        if (!confirm("Lanjutkan proses pengesahan final untuk moderasi ini?")) {
            return;
        }

        var mid = $(this).attr("mid");
        var catatan = typeof $("#txtCatatan").val() === "undefined" ? "-" : $("#txtCatatan").val();

        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:2, catatan:catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip orange white-text">DISAHKAN</span>';
                $("#tdKepOpd").html(modStyle);
                $("#tdKepOpd-" + mid).html(modStyle);
                $("#tdKepOpdDate").text(result.date["dt_flag_kepala_opd"]);
                alert("Anda telah melakukan verifikasi dengan status: DISAHKAN!");
                window.location.reload();
                         
            } else {
                alert("Sistem gagal mengesahkan moderasi!");
            }
        }, "post", "json");
    });

    $(document).on("click", ".btn-batalkan-mod", function () {
        if (!confirm("Lanjutkan proses pembatalan final moderasi ini?")) {
            return;
        }

        var mid = $(this).attr("mid");
        var catatan = typeof $("#txtCatatan").val() === "undefined" ? "-" : $("#txtCatatan").val();

        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:3, catatan:catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip black white-text">DIBATALKAN</span>';
                $("#tdKepOpd").html(modStyle);
                $("#tdKepOpd-" + mid).html(modStyle);
                $("#tdKepOpdDate").text(result.date["dt_flag_kepala_opd"]);
                alert("Anda telah melakukan verifikasi dengan status: DIBATALKAN!");
                window.location.reload();
            } else {
                alert("Sistem gagal membatalkan moderasi!");
            }
        }, "post", "json");
    });

    $(document).on("click", ".btn-tolak-mod", function () {
        var mid = $(this).attr("mid");
        var catatan = typeof $("#txtCatatan").val() === "undefined" ? "-" : $("#txtCatatan").val();
        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:0, catatan:catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip red white-text">DITOLAK</span>';
                alert("Anda telah melakukan verifikasi dengan status: DITOLAK!");
                $("#tdKepOpd").html(modStyle);
                $("#tdKepOpd-" + mid).html(modStyle);
                $("#tdKepOpdDate").text(result.date["dt_flag_kepala_opd"]);
                $("#tdCatatanKepOpd").text(catatan);                 
            } else {
                if (result.reload === "1") {
                    alert("Sistem mendeteksi perubahan status verifikasi Admin Kota atau Kepala BKPPD. Sistem akan memuat ulang halaman...");
                    window.location.reload();
                } else {
                    alert("Sistem gagal merubah status verifikasi!");
                }            
            }
        }, "post", "json");
    });

    $(".btn-info-mod").click(function () {
        var mid = $(this).attr("mid");
        var action = $(this).attr("isfinal") === "true" ? "infoModerasiHasil" : "infoModerasi";
        loadPage(route, action + "/" + mid, {}, "divModalBody", function (data) {
            $('#divModalInfoModerasi').openModal('open');
        });
    });

    $('a.modal-close').click(function (ev) {
        ev.preventDefault();
    });

    $(document).on("click", "#chkCheckAllMod", function () {
        if ($(this).is(":checked")) {            
            $(".check-all-mod").each(function (k, v) {
                if (!$(this).prop("disabled")) {
                    $(this).prop("checked", true);
                }
            });
        } else {
            $(".check-all-mod").prop("checked", false);
        }

        checkModHandler(".check-all-mod", ".btn-mass-verif");
        checkModHandler(".check-all-mod", ".btn-mass-legit");
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

        checkModHandler(".check-all-mod", ".btn-mass-verif");
        checkModHandler(".check-all-mod", ".btn-mass-legit");
    });

    /*
    $(".btn-verifikasi").click(function (e) {
        e.preventDefault();

        if ($(this).hasClass("disabled")) {
            return false;
        }  
        
        var flag = $(this).attr("flag");
        var status = flag === "1" ? "menerima" : "menolak";

        if (!confirm("Yakinkah Anda ingin "+ status +" semua moderasi yang telah Anda pilih sebelumnya?")) {
            return false;
        }

        var checkedMods = [];

        $(".check-all-mod").each(function (k, v) {
            if ($(this).is(":checked")) {
                checkedMods.push($(this).val());
            }
        });

        ajaxRequester(route + "/updateVerifikasiModerasiMassive", {checkedMods:checkedMods, flag:flag}, function (result) {
            if (result.status === "success") {
                alert("Selamat! Anda telah " + status + " beberapa moderasi yang telah dipilih secara masif!");
                window.location.reload();
            } else {
                alert("Sistem gagal melakukan verifikasi moderasi secara masif!");
            }
        }, "post", "json");
    });
    */

    $(".btn-mass-verif").click(function () {
        
        if ($(this).hasClass("disabled")) {
            return false;
        }

        var flag = $(this).attr("flag");
        var checkedMods = [];

        $(".check-all-mod").each(function (k, v) {
            if ($(this).is(":checked")) {
                checkedMods.push($(this).val());
            }
        });

        ajaxRequester(route + '/massVerifPage', {checkedMods: checkedMods, flag: flag}, function(html) {
            $("#divModalBodyMassVerif").html(html);
            $('#divModalMassVerif').openModal('open');
        }, "post", "html");
    });

    $("#btnTerapkanMassVerif").click(function (e) {
        e.preventDefault();

        var flag = $("#hidFlag").val();
        var jenisVerifikasi = flag === "1" ? "menerima" : "menolak";

        if (!confirm("Fasilitas "+jenisVerifikasi+" pengajuan moderasi ini hanya berlaku untuk daftar pengajuan moderasi yang belum disahkan Admin Kota. Lanjutkan proses?")) {
            return false;
        }

        var mids = $("#hidCheckedMods").val();
        var catatan = $("#txtCatatanMassVerif").val();

        swal({
            title: "Proses Pengesahan",
            type: "warning",
            text: "Sedang memproses.. Harap sabar menunggu",
            closeOnEsc: false,
            closeOnClickOutside: false,
            showCancelButton: false,
            showConfirmButton: false
        }); //added by daniek

        ajaxRequester(route + "/updateModerasiMassVerif", {mids:mids, flag:flag, catatan: catatan}, function (result) {
            swal.close();
            if (result.status === "success") {
                alert("Selamat! Anda telah " + jenisVerifikasi + " beberapa moderasi yang telah dipilih secara masal!");
                window.location.reload();
            } else {
                alert("Sistem gagal " + jenisVerifikasi + " moderasi secara masal! Pastikan Anda hanya memilih daftar pengajuan moderasi yang belum diverifikasi Admin Kota.");
            }
        }, "post", "json");
    });

    $(".btn-mass-legit").click(function () {        
        if ($(this).hasClass("disabled")) {
            return false;
        }

        var flag = $(this).attr("flag");
        var checkedMods = [];

        $(".check-all-mod").each(function (k, v) {
            if ($(this).is(":checked")) {
                checkedMods.push($(this).val());
            }
        });

        ajaxRequester(route + '/massLegitPage', {checkedMods: checkedMods, flag: flag}, function(html) {
            $("#divModalBodyMassLegit").html(html);
            $('#divModalMassLegit').openModal('open');
        }, "post", "html");
    });

    $("#btnTerapkanMassLegit").click(function (e) {
        e.preventDefault();

        var flag = $("#hidFlag").val();
        var jenisVerifikasi = flag === "2" ? "mengesahkan" : "membatalkan";

        if (!confirm("Fasilitas "+jenisVerifikasi+" ini hanya berlaku untuk daftar pengajuan moderasi yang telah disahkan Kepala BKPPD. Dengan menekan tombol ini berarti Anda mengakhiri proses pengajuan moderasi dari daftar yang dipilih dengan status final "+jenisVerifikasi+". Lanjutkan proses?")) {
            return false;
        }

        var mids = $("#hidCheckedMods").val();
        var catatan = $("#txtCatatanMassLegit").val();

        ajaxRequester(route + "/updateModerasiMassLegit", {mids:mids, flag:flag, catatan: catatan}, function (result) {
            if (result.status === "success") {
                alert("Selamat! Anda telah " + jenisVerifikasi + " beberapa moderasi yang telah dipilih secara masal!");
                window.location.reload();
            } else {
                alert("Sistem gagal " + jenisVerifikasi + " moderasi secara masal! Pastikan bahwa semua daftar pengajuan moderasi yang Anda pilih harus telah disahkan Kepala BKPPD");
            }
        }, "post", "json");
    });

    /*
    $(".btn-legitimasi").click(function (e) {
        e.preventDefault();

        if ($(this).hasClass("disabled")) {
            return false;
        }  
        
        var flag = $(this).attr("flag");
        var status = flag === "2" ? "mengesahkan" : "membatalkan";

        if (!confirm("Yakinkah Anda ingin "+ status +" semua moderasi yang telah Anda pilih sebelumnya?")) {
            return false;
        }

        var checkedMods = [];

        $(".check-all-mod").each(function (k, v) {
            if ($(this).is(":checked")) {
                checkedMods.push($(this).val());
            }
        });

        ajaxRequester(route + "/updateLegitimasiModerasiMassive", {checkedMods:checkedMods, flag:flag}, function (result) {
            if (result.status === "success") {
                alert("Selamat! Anda telah " + status + " beberapa moderasi yang telah dipilih secara masif!");
                window.location.reload();
            } else {
                alert("Sistem gagal melakukan legitimasi moderasi secara masif!");
            }
        }, "post", "json");
    });
    */
}

//loadPage(route, "welcome", {}, "divContent", function (data) {});

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
    }).always(function() {
        console.log("(C) 2017 - Developed by Husnan W");
    });
}
