<body class="search-app quick-results-off">
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('pns', 'main', 'header', ''); ?>    
        <?php $this->getView('pns', 'main', 'menu', ''); ?>

        <main class="mn-inner">

            <!-- added by Acil -->
            <div class="search-header">
                <div class="card card-transparent no-m">
                    <div class="card-content no-s">
                        <div class="z-dept-1 search-tabs">
                            <div class="search-tabs-container">
                                <div class="col s12 m12 l12">

                                    <!-- Header Text -->
                                    <div class="row search-tabs-row search-tabs-header blue-grey white-text">
                                        <div class="show-on-small hide-on-med-and-up center-align">
                                            <span style="line-height: 48px; text-transform: uppercase">Moderasi Dalam Proses</span>
                                        </div>
                                        <div class="hide-on-small-only">
                                            <span style="line-height: 50px;">Daftar Pengajuan Moderasi</span>
                                        </div>
                                    </div>

                                    <!-- Form Header -->
                                    <form id="frmData" onsubmit="return false" autocomplete="off">
                                        <div class="row search-tabs-row search-tabs-header">
                                            <div class="input-field col l1 m2 s3">
                                                <label for="tahun" class="active">Tahun</label>
                                                <?= comp\MATERIALIZE::inputSelect('tahun', [], date('Y')) ?>
                                            </div>
                                            <div class="input-field col l2 m3 s5">
                                                <label for="bulan" class="active">Bulan</label>
                                                <?= comp\MATERIALIZE::inputSelect('bulan', comp\FUNC::$namabulan1, date('n')) ?>
                                                <?= comp\MATERIALIZE::inputKey('selBulan', date('n')) ?>
                                            </div>
                                            <div class="input-field col l3 m3 s4">
                                                <label for="status" class="active">Status moderasi</label>
                                                <?= comp\MATERIALIZE::inputSelect('status', array('semua' => 'Semua', 'tolak' => 'Ditolak', 'terima' => 'Diterima', 'null' => 'Belum verifikasi'), 'semua') ?>
                                            </div>
                                            <div class="input-field col l6 m4 s12 no-s">
                                                <div class="row no-s">
                                                    <div class="input-field col l11 m10 s9">
                                                        <?= comp\MATERIALIZE::inputText('cari', 'text', '') ?>
                                                        <label for="cari">Cari keterangan</label>
                                                    </div>
                                                    <div class="input-field col l1 m2 s3">
                                                        <button class="btn btn-floating waves-effect green">
                                                            <i class="material-icons">search</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Loader on Submit -->
                                    <div class="row">
                                        <div class="progress no-s">
                                            <div id="progressView" class="determinate"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- added by Acil -->

            <!-- Content -->
            <div class="row">
                <div class="col s12">
                    <div id="data-tabel"></div>
                </div>
            </div>
            <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
                <a id="" href="javascript:void(0)" class="btn-floating btn-large tooltipped primary btnForm" data-position="top" data-tooltip="Tambah Moderasi">
                    <i class="large material-icons">mode_edit</i>
                </a>
            </div>

            <!-- modal./ -->
            <div id="modalInput" class="modal modal-fixed-footer">
                <form id="frmInput" class="col s12" onsubmit="return false" autocomplete="off">
                    <div class="modal-header center-align cyan darken-1 white-text" style="padding: 5px">
                        <span id="modalHeader">Input Moderasi</span>
                        <span class="modal-close right"><i class="material-icons">close</i>
                    </div>
                    <div id="data-form" class="modal-content"></div>
                    <div class="modal-footer">
                        <div class="progress no-s">
                            <div id="simpanProgress" class="determinate"></div>
                        </div>
                        <button type="submit" class="modal-action waves-effect waves-light btn btnFooter" style="margin: 5px;">Simpan</button>
                        <button type="button" class="modal-action modal-close waves-effect waves-grey btn white btnFooter" style="margin: 5px;">Tutup</button>
                    </div>
                </form>
            </div>

            <div id="modalDetail" class="modal modal-fixed-footer">
                <form id="frmInputDetail" class="col s12" onsubmit="return false" autocomplete="off">
                    <div class="modal-header center-align cyan darken-1 white-text" style="padding: 5px">
                        <span id="modalHeader">Detail Moderasi</span>
                        <span class="modal-close right"><i class="material-icons">close</i></span>
                    </div>
                    <div class="modal-content no-s">
                        <div id="data-detail"></div>
                    </div>
                    <div class="modal-footer">
                        <!-- <a class="modal-close btn btnClose">Close</a> -->
                        <a id="" class="btn red waves-effect btnHapus">Hapus</a>
                        <a id="" class="modal-close btn blue waves-effect btnEdit">Edit</a>
                    </div>
                </form>
            </div>

            <div id="modalDetailMobile" class="modal modal-fixed-footer">
            </div>
            <!-- /.modal -->
        </main>


        <?php // $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>
</body>

<link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
<script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
<script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php') ?>"></script>
<script>
                    app.init("<?= $this->link($this->getProject() . $this->getController()) ?>");
                    app.getTahunMod();
                    app.loadTabel();

                    /* Desktop */
                    $(document).on("change", "#bulan, #status", function () {
                        var bulan = $(this).val();
                        $("#selBulan").val(bulan);
                        
                        // Load tabel otomatis ketika ganti bulan
                        app.loadTabel();
                    });
                    $(document).on("change", "#tahun", function () {
                        var tahun = $(this).val();
                        app.getBulanMod(tahun);
                    });

                    $(document).on("change", "#kode_presensi", function () {
                        app.toggleTglAkhir();
                    });

                    $(document).on("click", ".btn-info-mod", function () {
                        $(".tooltipped").trigger("mouseleave");
                        app.infoModerasi(this.id);
                    });

                    $(document).on("click", ".btnHapus", function () {
                        var varTitle = 'PERHATIAN!';
                        var varMessage = 'Anda akan menghapus moderasi';
                        app.showConfirm(this.id, varTitle, varMessage);
                    });

                    $(document).on("click", ".btnEdit", function () {
                        app.showForm(this.id);
                    });

                    $(document).on("click", ".btnForm", function () {
                        app.showForm(this.id);
                    });

                    $(document).on("submit", "#frmData", function () {
                        app.loadTabel();
                    });

                    $(document).on("submit", "#frmInput", function () {
                        app.simpanFile(this);
                    });

                    $(document).on("change", ".selKategoriModerasi", function () {
                        var kodeKatMod = $(this).val();
                        var idMod = $("#frmInput #id").val();

                        if (idMod === null || idMod === "") {
                            console.log($("option:selected", this).prop("kategori"));

                            if ($("option:selected", this).attr("kategori") === "semua") {
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
                        }

                        $("#kode_presensi").empty();
                        app.pilJenisModerasi(kodeKatMod);

                        $("select").material_select();
                    });


                    /* Mobile */
                    $(document).on("click", ".btnInfo", function () {
                        app.infoModerasi(this.id);
                    });
                    $(document).on("click", ".listModerasiMob", function () {
                        app.detail(this.id);
                    });

                    $(document).on("click", ".btnModerasiMob", function () {
                        $("#detail-info-btn, #detail-info").hide();
                        $("#detail-form, #detail-form-btn").show();
                        $("#flag_operator_opd").val(this.id);
                    });

                    $(document).on("submit", "#frmInputDetail", function () {
                        app.simpanVerifMob(this);
                    });
</script>