function husnanw_moderasi_main_admin_opd(route, dateLimit) {
    disableInputs(true);
    $('select').material_select();
    Dropzone.autoDiscover = false;

    var fileDokumenPendukung = new Dropzone("#frmDokumenPendukung", {
        paramName: "fileDokumenPendukung",
        maxFiles: 5,
        acceptedFiles: "image/*, .pdf, .PDF, .doc, .DOC, .docx, .DOCX, .odt, .ODT",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 6,
        dictDefaultMessage: "Klik untuk memilih berkas yang ingin diunggah..."
    });

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 1, // Creates a dropdown of 15 years to control year,
        today: 'Hari Ini',
        clear: false,
        close: 'OK Pilih',
        closeOnSelect: false, // Close upon selecting a date,
        format: "dd-mm-yyyy",
        //min: getLimitDateAwal(dateLimit),
        //max: parseInt(dateLimit),
        onSet: function() {
            var tglAwal = $.trim($("#txtTanggalAwalModerasi").val());
            var tglAkhir = $.trim($("#txtTanggalAkhirModerasi").val());
            
            if ($.trim(tglAkhir) !== "") {
                if (moment(toStdDate('-', tglAwal)).isSameOrBefore(toStdDate('-', tglAkhir), "day")) {
                    checkDatesMod(route, tglAwal, tglAkhir);
                } else {
                   disableInputs();
                    alert("Perhatian: Tanggal awal harus lebih kecil atau sama dengan tanggal akhir!");
                }
            }
        }
    });

    $('.datepicker-akhir').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 1, // Creates a dropdown of 15 years to control year,
        today: 'Hari Ini',
        clear: false,
        close: 'OK Pilih',
        closeOnSelect: false, // Close upon selecting a date,
        format: "dd-mm-yyyy",
        //min: getLimitDateAwal(dateLimit),
        onSet: function() {
            var tglAwal = $.trim($("#txtTanggalAwalModerasi").val());
            var tglAkhir = $.trim($("#txtTanggalAkhirModerasi").val());
            if ($.trim(tglAwal) !== "") {
                if (moment(toStdDate('-', tglAkhir)).isSameOrAfter(toStdDate('-', tglAwal), "day")) {
                    checkDatesMod(route, tglAwal, tglAkhir);
                } else {
                    disableInputs();
                    alert("Perhatian: Tanggal akhir harus lebih besar atau sama dengan tanggal awal!");
                }
            }
        }
    });

    ajaxRequester(route + "/getDaftarPegawaiModerasi", {}, function (result) {
        $.each(result, function(index, val) {
            $('#selDaftarPns').append($('<option>', { 
                value: val.pin_absen,
                text : val.nama_lengkap,
                nip: val.nipbaru
            }));
        });
    
        $('select').material_select();
    }, "get", "json");

    $(document).on("change", "#selKategoriModerasi", function () {
        var kodeKatMod = $(this).val();
        $("#selJenisModerasi").empty();
console.log($("option:selected",this).prop("kategori"));
        if ($("option:selected",this).attr("kategori") === "semua") {
            $("option[kategori='individual']", this).prop("disabled", true);
            $("option[kategori='semua']", this).prop("disabled", false);
        } else {
            $("option[kategori='semua']", this).prop("disabled", true);
            $("option[kategori='individual']", this).prop("disabled", false);
        }

        if (kodeKatMod === null || kodeKatMod.length === 0) {
            $("option[kategori='individual']", this).prop("disabled", false);
            $("option[kategori='semua']", this).prop("disabled", false);
        } else {
           kodeKatMod = kodeKatMod.join('|');
        }

        $('select').material_select();

        ajaxRequester(route + "/getJenisModerasi/"+kodeKatMod, {}, function (result) {
            $.each(result, function (i, item) {
                $('#selJenisModerasi').append($('<option>', { 
                    value: item.kode_presensi,
                    text : item.ket_kode_presensi 
                }));
            });
            $('#selJenisModerasi').material_select();
        }, "get", "json");
    });

    $("#chkPilihSemuaPns").click(function () {
        if ($(this).is(":checked")) {
            $(".info-nama-nip").hide();
            $(".info-list-semua-nama").show();
            $("#divListNama").html('');
            $("#divFotoPemohon").html('');
            $(".info-list-nama").hide();
            $('#selDaftarPns').material_select('destroy');
            $("#hidPin").val($("#hidAllPin").val());
            $(".tglmoderasi").removeClass("disinput");
        } else {
            /*
            $(".info-list-semua-nama").hide();
            $(".info-nama-nip").show();
            $("#selDaftarPns option:selected").prop("selected", false);
            $('#selDaftarPns').material_select();
            $("#spNamaPemohon").text("");
            $("#spNipPemohon").text("");
            $("#hidPin").val("");
            $(".tglmoderasi").removeClass("disinput").addClass("disinput");
            */
            window.location.reload();
        }
    });
        
    $("#selDaftarPns").change(function () {
        if (Array.isArray($(this).val()) && $(this).val().length > 1) {
            var strListPns = "<ol>";
            var strListPin = "";

            $("option:selected", this).each(function (k, v) {
                strListPns += "<li>"+v.text+"</li>";  
                strListPin += v.value + ",";      
            });

            strListPns += "</ol>";

            $("#divListNama").html(strListPns);
            $("#divFotoPemohon").html('');
            $(".info-nama-nip").hide();
            $(".info-list-nama").show();
            $("#hidPin").val(strListPin.slice(0, -1));
            $(".tglmoderasi").removeClass("disinput");
        } else if(Array.isArray($(this).val()) && $(this).val().length === 1) {
            $("#hidPin").val($("option:selected", this)[0].value);
            $("#spNamaPemohon").text($("option:selected", this)[0].text);
            $("#spNipPemohon").text($("option:selected", this)[0].attributes.nip.value);
            $("#divFotoPemohon").html('<img src="http://simpeg.pekalongankota.go.id/upload/foto/'+$("option:selected", this)[0].attributes.nip.value+'-FOTO.jpg" class="responsive-img" alt="foto">');
            $(".tglmoderasi").removeClass("disinput");
            $("#btnProses").removeAttr("disabled");
            $(".info-nama-nip").show();
            $("#divListNama").html('');
            $(".info-list-nama").hide();
        } else {
            /*
            $("#spNamaPemohon").text("");
            $("#spNipPemohon").text("");
            $(".info-nama-nip").show();
            $("#divListNama").html('');
            $("#hidPin").val("");
            $("#divFotoPemohon").html('');
            $(".tglmoderasi").removeClass("disinput").addClass("disinput");
            */
           window.location.reload();
        }
    });
    
    $(".btn-batal").click(function () {
        window.location.reload();
    });

    $("#btnProses").click(function () {
        if ($(this).hasClass("disabled")) {
            return false;
        }
        
        var katMods = $("#selKategoriModerasi").val();
        var pin = $("#hidPin").val();
        var jenis = "";

        if (katMods === null || katMods.length === 0) {
            alert("Kategori dan Jenis Moderasi harus dipilih dulu.");
            return false;
        } else if (katMods.length === 1) {
            jenis = $("#selKategoriModerasi").val()[0] + '|' + $("#selJenisModerasi").val();
        } else {
            jenis = $("#selJenisModerasi").val();
        }

        var tanggal_awal = $("#txtTanggalAwalModerasi").val();
        var tanggal_akhir = $("#txtTanggalAkhirModerasi").val();
        var keterangan = $("#txtKeterangan").val();

        if (pin === undefined || jenis === undefined || tanggal_awal === undefined || tanggal_akhir === undefined || jenis === "" || jenis === null) {
            alert("PERHATIAN:\n\rIsian belum lengkap. Pastikan pemohon, jenis moderasi, dan tanggal awal dan akhir moderasi diisi dengan benar!");
            return;
        }

        var ts1 = moment(tanggal_awal + " 9:00", "D-M-YYYY H:mm").valueOf();
        var ts2 = moment(tanggal_akhir + " 9:00", "D-M-YYYY H:mm").valueOf();
        var m1 = moment(ts1);
        var m2 = moment(ts2);

        if (m1 > m2) {
            alert("PERHATIAN:\n\rTanggal awal moderasi tidak boleh melebihi tanggal akhirnya.");
            return;
        }

        var data = {
            pin_absen: pin,
            katMods: katMods,
            kd_jenis: jenis,
            tanggal_awal: tanggal_awal,
            tanggal_akhir: tanggal_akhir,
            keterangan: keterangan
        };

        if (pin.indexOf(',') !== -1) {
            if (!confirm("Perhatian: Satu atau sebagian PNS yang Anda pilih bisa jadi tidak diproses pengajuan moderasi TF nya dikarenakan sebelumnya sudah terdapat moderasi pada tanggal yang sama. Periksa dengan teliti hasil moderasinya. Lanjutkan?")) {
                return false;
            }
        }

        ajaxRequester(route + "/simpanPemohonModerasi", data, function (result) {
            //console.log(result);
            if (result.status === "success") {
                if (Array.isArray(result.lid) && result.lid.length > 1) {
                    $("#hidLids").val(result.lid.join(','));
                }
                
                fileDokumenPendukung.processQueue();                    
                fileDokumenPendukung.on("successmultiple", function (file, status) {
                    //console.log(status);
                    if (status === "success") {
                        // do nothing: bypass
                    } else {
                        alert("Upload data file dokumen moderasi gagal!");
                        return;
                    }
                 });
                alert("Pemohon sukses dimoderasi. Segera verifikasi daftar PNS yang bersangkutan.");
                window.location.href = route + "/daftarVerMod";
            } else {
                alert(result.message);
                enableInputs();
            }
        }, "post", "json");

    });

    $(document).on("click", ".btn-terima-mod", function () {
        var mid = $(this).attr("mid");
        var catatan = typeof $("#txtCatatan").val() === "undefined" ? "-" : $("#txtCatatan").val();
        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:1, catatan:catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip green white-text">DITERIMA</span>';
                alert("Anda telah melakukan verifikasi dengan status: DITERIMA!");
                $("#tdOpOpd").html(modStyle);
                $("#tdOpOpd-" + mid).html(modStyle);
                $("#tdOpOpdDate").text(result.date["dt_flag_operator_opd"]);
                $("#tdCatatanOpOpd").text(catatan);
            } else {
                if (result.reload === "1") {
                    alert("Sistem mendeteksi perubahan status verifikasi Kepala OPD. Sistem akan memuat ulang halaman...");
                    window.location.reload();
                } else {
                    alert("Sistem gagal merubah status verifikasi!");
                }            
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
                $("#tdOpOpd").html(modStyle);
                $("#tdOpOpd-" + mid).html(modStyle);
                $("#tdOpOpdDate").text(result.date["dt_flag_operator_opd"]);
                $("#tdCatatanOpOpd").text(catatan); 
            } else {
                if (result.reload === "1") {
                    alert("Sistem mendeteksi perubahan status verifikasi Kepala OPD. Sistem akan memuat ulang halaman...");
                    window.location.reload();
                } else {
                    alert("Sistem gagal merubah status verifikasi!");
                }            
            }
        }, "post", "json");
    });

    $(document).on("click", ".btn-del-mod", function () {
        if (!confirm("Yakin Anda ingin menghapus data moderasi dari pegawai yang bersangkutan?")) {
            return;
        }

        var mid = $(this).attr("mid");
        ajaxRequester(route + "/delModerasi", {mid:mid}, function (result) {
            if (result.status === "success") {
                alert("Anda telah menghapus data moderasi pegawai yang bersangkutan!");
                window.location.reload();
            } else {
                alert("Sistem gagal menghapus data moderasi!" + "\n" + result.message);
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
    });

    /*
    $(".btn-mass-verif").click(function (e) {
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

        ajaxRequester(route + "/updateModerasiMassive", {checkedMods:checkedMods, flag:flag}, function (result) {
            if (result.status === "success") {
                alert("Selamat! Anda telah " + status + " beberapa moderasi yang telah dipilih secara massal!");
                window.location.reload();
            } else {
                alert("Sistem gagal melakukan verifikasi moderasi secara massal!");
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

        if (!confirm("Yakinkah Anda ingin " + jenisVerifikasi + " semua moderasi yang telah Anda pilih sebelumnya?")) {
            return false;
        }

        var mids = $("#hidCheckedMods").val();
        var catatan = $("#txtCatatanMassVerif").val();

        ajaxRequester(route + "/updateModerasiMassVerif", {mids:mids, flag:flag, catatan: catatan}, function (result) {
            if (result.status === "success") {
                alert("Selamat! Anda telah " + jenisVerifikasi + " beberapa moderasi yang telah dipilih secara massal!");
                window.location.reload();
            } else {
                alert("Sistem gagal " + jenisVerifikasi + " moderasi secara massal!");
            }
        }, "post", "json");
    });

    setTimeout(function() {
        $("#divNotifMandatoryInput").addClass("animated hinge");
    }, 7000);
    setTimeout(function() {
        $("#divRowNotifMandatoryInput").remove();
        $("#divFotoPemohon").addClass("animated rubberBand");
    }, 10000);
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
    }).always(function() {
        console.log("(C) 2017 - Developed by Husnan W");
    });
}

function getLimitDateAwal(limitDate)
{
    //alert(limitDate);
    var currDate = moment().date();
    var currMonth = moment().month();

    if (currDate <= limitDate) {
        //return moment().startOf("month").format("DD-MM-YYYY");
        var firstDateValidMonth = moment().subtract(1, 'months').format('YYYY-MM') + "-01";
    } else {
        var firstDateValidMonth = moment().format('YYYY-MM') + "-01";
    }

    return moment(firstDateValidMonth).format("DD-MM-YYYY");
}

function toStdDate(separator, str) {
    arrDate = str.split(separator);
    return arrDate[2] + separator + arrDate[1] + separator + arrDate[0];
}

function checkDatesMod(route, dateAwal, dateAkhir) {

    var data = {
        dateAwal: dateAwal, 
        dateAkhir: dateAkhir,
        pinAbsen: $("#hidPin").val() 
    };

    ajaxRequester(route + '/checkDatesMod', data, function(result) {
        if (result.status === "success") {
            enableInputs();
        } else {
            disableInputs();
            alert(result.message);
        }
    }, "post", "json");

return true;
}

function disableInputs(isComplete) {
    $("#btnProses").removeClass("disabled").addClass("disabled").css("cursor", "not-allowed");
    $(".statinput").removeClass("disinput").addClass("disinput");

    if (isComplete === true) {
        $(".tglmoderasi").removeClass("disinput").addClass("disinput");
    }
}

function enableInputs(isComplete) {
    $("#btnProses").removeClass("disabled").css("cursor", "pointer");
    $(".statinput").removeClass("disinput");

    if (isComplete === true) {
        $(".tglmoderasi").removeClass("disinput");
    }
}
