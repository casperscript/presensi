function husnanw_moderasi_main_pns(route, dateLimit) {
    //var dateAwal = moment().subtract(1,'months').endOf('month').subtract(2, "days").format('DD-MM-YYYY')
    $("#txtTglFilterAwal").val(moment().startOf('month').format('DD-MM-YYYY'));
    $("#txtTglFilterAkhir").val(moment().format('DD-MM-YYYY'));
    $('select').material_select();
    Dropzone.autoDiscover = false;

    var fileDokumenPendukung = new Dropzone("#frmDokumenPendukung", {
        paramName: "fileDokumenPendukung",
        maxFiles: 5,
        acceptedFiles: "image/*, .pdf, .PDF, .doc, .DOC, .docx, .DOCX, .odt, .ODT",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 6,
        dictDefaultMessage: "Klik untuk unggah berkas"
    });

    $('.dtpfilterawal').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 1, // Creates a dropdown of 15 years to control year,
        today: 'Hari Ini',
        clear: false,
        close: 'OK Pilih',
        closeOnSelect: false, // Close upon selecting a date,
        format: "dd-mm-yyyy",

        onSet: function() {
            var tglAwal = $.trim($("#txtTglFilterAwal").val());
            var tglAkhir = $.trim($("#txtTglFilterAkhir").val());
            
            if ($.trim(tglAkhir) !== "") {
                if (!moment(toStdDate('-', tglAwal)).isSameOrBefore(toStdDate('-', tglAkhir), "day")) {
                    alert("Perhatian: Tanggal awal harus lebih kecil atau sama dengan tanggal akhir!");
                    $("#txtTglFilterAwal").val(moment().startOf('month').format('DD-MM-YYYY'));
                }
            }
        }
    });

    $('.dtpfilterakhir').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 1, // Creates a dropdown of 15 years to control year,
        today: 'Hari Ini',
        clear: false,
        close: 'OK Pilih',
        closeOnSelect: false, // Close upon selecting a date,
        format: "dd-mm-yyyy",

        onSet: function() {
            var tglAwal = $.trim($("#txtTglFilterAwal").val());
            var tglAkhir = $.trim($("#txtTglFilterAkhir").val());
            if ($.trim(tglAwal) !== "") {
                if (!moment(toStdDate('-', tglAkhir)).isSameOrAfter(toStdDate('-', tglAwal), "day")) {
                    alert("Perhatian: Tanggal akhir harus lebih besar atau sama dengan tanggal awal!");
                    $("#txtTglFilterAwal").val(moment().format('DD-MM-YYYY'));
                }
            }
        }
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

    $(document).on("click", "#btnFilterTgl", function () {
        var data =  {
            dateAwal: $("#txtTglFilterAwal").val(), 
            dateAkhir: $("#txtTglFilterAkhir").val()
        };

        var appendText = "";

        if ($(this).attr("action-value") === "hasil") {
            appendText = "Hasil";
        }

        ajaxRequester(route + "/loadTabelDaftarMod" + appendText, data, function (result) {
            $("#divTabelDaftarMod" + appendText).html(result);
        }, "post", "html");
    });

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

    ajaxRequester(route + "/getCurrentPns", {}, function (result) {
        $("#hidPin").val(result.pin_absen);
        $("#spNamaPemohon").text(result.nama_lengkap);
        $("#spNipPemohon").text(result.nipbaru);
        $("#divFotoPemohon").html('<img src="http://simpeg.pekalongankota.go.id/upload/foto/'+result.nipbaru+'-FOTO.jpg" class="responsive-img" style="min-width: 65px;" alt="foto">');
    }, "get", "json");
    
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

        disableInputs();
        
        ajaxRequester(route + "/simpanPemohonModerasi", data, function (result) {
            if (result.status === "success") {
                //console.log(Array.isArray(result.lid));
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
                alert("Pemohon sukses dimoderasi. Data akan diverifikasi oleh Admin OPD.");
                window.location.href = route + "/daftarMod";
            } else {
                alert(result.message);
                enableInputs();
            }
        }, "post", "json");

    });

    $(document).on("click", ".btn-del-mod", function () {
        if (!confirm("Yakin ingin menghapus data moderasi Anda sendiri?")) {
            return;
        }

        var mid = $(this).attr("mid");
        ajaxRequester(route + "/delModerasi", {mid:mid}, function (result) {
            if (result.status === "success") {
                alert("Anda telah menghapus data moderasi!");
                window.location.reload();
            } else {
                alert("Sistem gagal menghapus data moderasi!" + "\n" + result.message);
            }
        }, "post", "json");
    });

    $(document).on("click", ".btn-info-mod", function () {
        var mid = $(this).attr("mid");
        var action = $(this).attr("isfinal") === "true" ? "infoModerasiHasil" : "infoModerasi";
        
        loadPage(route, action + "/" + mid, {}, "divModalBody", function (data) {
            $('#divModalInfoModerasi').openModal('open');
        });
    });

    $('a.modal-close').click(function (ev) {
        ev.preventDefault();
    });
    
    setTimeout(function() {
        $("#divNotifMandatoryInput").addClass("animated hinge");
    }, 7000);
    setTimeout(function() {
        $("#divRowNotifMandatoryInput").remove();
        $("#divFotoPemohon").addClass("animated rubberBand");
    }, 10000);
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

