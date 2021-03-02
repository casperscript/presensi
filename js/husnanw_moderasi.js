function husnanw_moderasi_main(route, dateLimit, verModKodeLokasi) {
    var dateAwal = getLimitDateAwal(dateLimit);
    $('select').material_select();
    Dropzone.autoDiscover = false;

    var fileDokumenPendukung = new Dropzone("#frmDokumenPendukung", {
        paramName: "fileDokumenPendukung",
        maxFiles: 5,
        acceptedFiles: "image/*, .pdf, .PDF, .doc, .DOC, .docx, .DOCX, .odt, .ODT",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 6
    });

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 1, // Creates a dropdown of 15 years to control year,
        today: 'Hari Ini',
        clear: false,
        close: 'OK Pilih',
        closeOnSelect: false, // Close upon selecting a date,
        format: "dd-mm-yyyy",
        min: dateAwal,
        onSet: function() {

        }
    });

    ajaxRequester(route + "/getDaftarOpd", {}, function (result) {
        $.each(result, function(index, val) {
            $('#selDaftarOpd, #selDaftarOpdVerMod').append($('<option>', { 
                value: val.kdlokasi,
                text : val.singkatan_lokasi
            }));
        });
    
        $('select').material_select();
    }, "get", "json");

    $("#selDaftarOpd").change(function () {
        ajaxRequester(route + "/getDaftarPegawaiModerasi/" + $(this).val(), {}, function (result) {
            $('#selDaftarPns').empty();
            $.each(result, function(index, val) {
                $('#selDaftarPns').append($('<option>', { 
                    value: val.pin_absen,
                    text : val.nama_lengkap,
                    nip: val.nipbaru
                }));
            });
        
            $('select').material_select();
        }, "get", "json");
    });

    $("#selDaftarOpdVerMod").change(function () {
        var kdlokasi = $(this).val();
        var status = $(this).attr("isfinal") === "1" ? "final" : "";
        loadPage(route, "loadTabelVerMod/" + kdlokasi + "X1X"+status, {}, "divTabelVerMod", function (data) {
            
        });
    });
        
    $("#selDaftarPns").change(function () {
        $("#spNamaPemohon").text($("option:selected", this).text());
        $("#spNipPemohon").text($("option:selected", this).attr("nip"));
        $("#divFotoPemohon").html('<img src="http://simpeg.pekalongankota.go.id/upload/foto/'+$("option:selected", this).attr("nip")+'-FOTO.jpg" class="responsive-img" alt="foto">');

        $("#btnProses").removeAttr("disabled");
    });

    $(".btn-batal").click(function () {
        window.location.reload();
    });

    $("#btnProses").click(function () {
        var kodeLokasi = $("#selDaftarOpd").val();
        var pin = $("#selDaftarPns").val();
        var jenis = $("input[name='rbtJenisModerasi']:checked").val();
        var tanggal_awal = $("#txtTanggalAwalModerasi").val();
        var tanggal_akhir = $("#txtTanggalAkhirModerasi").val();
        var keterangan = $("#txtKeterangan").val();

        if (kodeLokasi === undefined || pin === undefined || jenis === undefined || tanggal_awal === undefined || tanggal_akhir === undefined) {
            alert("PERHATIAN:\n\rIsian belum lengkap. Pastikan lokasi OPD, pemohon, jenis moderasi, dan tanggal awal dan akhir moderasi diisi dengan benar!");
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
            kdlokasi: kodeLokasi,
            pin_absen: pin,
            kd_jenis: jenis,
            tanggal_awal: tanggal_awal,
            tanggal_akhir: tanggal_akhir,
            keterangan: keterangan
        };

        ajaxRequester(route + "/simpanPemohonModerasi", data, function (result) {
            //console.log(result);
            if (result.status === "success") {
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
                alert("Pemohon sukses dimoderasi. Data akan diverifikasi oleh Kepala OPD.");
                window.location.href = route + "/daftarVerMod";
            } else {
                alert(result.message);
            }
        }, "post", "json");

    });

    $(document).on("click", ".btn-sahkan-mod", function () {
        var mid = $(this).attr("mid");
        var catatan = $("#txtCatatan").val();
        ajaxRequester(route + "/updateModerasi", {mid:mid, flag:2, catatan: catatan}, function (result) {
            if (result.status === "success") {
                var modStyle = '<span class="chip orange white-text">DISAHKAN</span>';
                alert("Anda telah melakukan verifikasi dengan status: DISAHKAN!");
                $("#tdOpKota").html(modStyle);
                $("#tdOpKota-" + mid).html(modStyle);
                $("#tdOpKotaDate").text(result.date["dt_flag_operator_kota"]);
                $("#tdPerubahanTerakhir").text(result.date["dt_flag_operator_kota"]);
                $("#tdCatatanOpKota").text(catatan);

                if ($.trim(catatan) === "") {
                    window.location.reload();
                }

                ajaxRequester(route + "/getTotalBelumVerifikasi/" + verModKodeLokasi, {}, function (result) {
                    $("#bTotal").text(result.total);
                }, "get", "json");
            } else {
                if (result.reload === "1") {
                    alert("Sistem mendeteksi perubahan status pengesahan Kepala BKPPD. Sistem akan memuat ulang halaman...");
                    window.location.reload();
                } else {
                    alert("Sistem gagal melakukan pengesahan moderasi!");
                }
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

    $('a.modal-close').click(function (ev) {
        ev.preventDefault();
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
                alert("Sistem gagal melakukan pengesahan moderasi secara massal!");
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

function getLimitDateAwal(limitDate)
{
    var currDate = moment().date();
    var currMonth = moment().month();

    if (currDate >= 1 && currDate <= limitDate && currMonth !== 0) {
        return moment().subtract(1, 'months').startOf("month").format("DD-MM-YYYY");
    } else {
        return moment().startOf("month").format("DD-MM-YYYY");
    }
}