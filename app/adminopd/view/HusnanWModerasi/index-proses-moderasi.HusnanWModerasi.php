<body class="search-app quick-results-off">
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>

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
                                        <div class="hide-on-small-only center-align">
                                            <span style="line-height: 50px;">Daftar Proses Status Verifikasi PNS yang Mengajukan Moderasi Ketidakhadiran</span>
                                        </div>
                                    </div>

                                    <!-- Form Header -->
                                    <form id="frmData" onsubmit="return false" autocomplete="off">
                                        <div class="row search-tabs-row search-tabs-header">
                                            <div class="input-field col l2 m2 s5">
                                                <!--<i class="material-icons prefix hide-on-small-only">date_range</i>-->
                                                <?= comp\MATERIALIZE::inputSelect('bulan', comp\FUNC::$namabulan1, date('m', strtotime('-9 day'))) ?>
                                            </div>
                                            <div class="input-field col l1 m2 s3">
                                                <?= comp\BOOTSTRAP::inputSelect('tahun', $listTahun, date('Y', strtotime('-9 day')), '') ?>
                                            </div>
                                            <div class="input-field col l2 m2 s4">
                                                <?= comp\MATERIALIZE::inputSelect('status', array('semua' => 'Semua', 'tolak' => 'Ditolak', 'terima' => 'Diterima', 'null' => 'Belum verifikasi'), 'semua') ?>
                                            </div>
                                            <div class="input-field col l3 m4 s12 no-s">
                                                <div class="row">
                                                    <div class="input-field col s9 m10">
                                                        <?= comp\MATERIALIZE::inputText('cari', 'text', '') ?>
                                                        <label for="cari">Pencarian</label>
                                                    </div>
                                                    <div class="input-field col s3 m2">
                                                        <button class="btn btn-floating waves-effect green">
                                                            <i class="material-icons">search</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="input-field col l4 m4 s12 hide-on-med-and-down right-align">
												<a id="btnTerimaSemua" class="btn btn-mass-verif green waves-effect waves-light disabled" flag="1" title="Diaktifkan saat lebih dari satu moderasi dipilih">Terima</a>
												<a id="btnTolakSemua" class="btn btn-mass-verif red waves-effect waves-light disabled" flag="0" title="Diaktifkan saat lebih dari satu moderasi dipilih">Tolak</a>
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
                    <div id="divTabelVerMod"></div>
                </div>
            </div>
            <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
                <a class="btn-floating btn-large primary">
                    <i class="large material-icons">mode_edit</i>
                </a>
            </div>

            <!-- modal./ -->
            <div id="divModalInfoModerasi" class="modal">
                <div class="modal-content">
                    <h4>Informasi Detil Pengajuan Moderasi</h4>
                    <div id="divModalBody"></div>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-action modal-close waves-effect waves-green btn"><span style="position: relative; top: -2px;">Terima Kasih</span> <i class="material-icons">thumb_up</i></a>
                </div>
            </div>

            <div id="modalInput" class="modal modal-fixed-footer">
                <form id="frmInput" class="col s12" onsubmit="return false" autocomplete="off">
                    <div class="modal-content">
                        <div id="data-form"></div>
                    </div>
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
                    <div class="modal-content no-s">
                        <h4 class="center-align blue-grey white-text isinya">Detail Moderasi</h4>
                        <duv id="data-detail"></duv>
                    </div>
                    <div class="modal-footer center-align">
                        <div id="detail-info-btn" class="detailAction">
                            <a id="1" href="javascript:void(0)" class="waves-effect green btn btnModerasiMob">Terima</a>
                            <a id="0" href="javascript:void(0)" class="waves-effect red btn btnModerasiMob">Tolak</a>
                        </div>
                        <div id="detail-lock" class="center-align detailAction">
                            <a class="modal-close btn waves-effect">Close</a>
                        </div>
                        <div id="detail-form-btn" class="detailAction">
                            <button class="modal-close btn waves-effect">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.modal -->
        </main>


        <?php // $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>
</body>

<script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php') ?>"></script>
<script>
                    app.init("<?= $this->link($this->getProject() . $this->getController()) ?>");
                    app.loadTabel();

                    /* Desktop */
                    $(document).on("click", ".btn-mass-verif", function () {
                        var flag = $(this).attr("flag");
                        app.verifMassal(flag);
                    });
                    $(document).on("click", ".btn-tolak-mod", function () {
                        app.changeStatMod(this.id, 0);
                    });
                    $(document).on("click", ".btn-terima-mod", function () {
                        app.changeStatMod(this.id, 1);
                    });
                    $(document).on("click", ".btn-info-mod", function () {
                        app.infoModerasi(this.id);
                        //alert(this.id);
                    });

                    $(document).on("submit", "#frmData", function () {
                        app.loadTabel();
                    });
                    $(document).on("submit", "#frmInput", function () {
                        app.simpanVerif();
                    });

                    //Check List
                    $(document).on("click", "#chkCheckAllMod", function () {
                        if ($(this).is(":checked")) {
                            $(".check-all-mod").each(function () {
                                if (!$(this).prop("disabled")) {
                                    $(this).prop("checked", true);
                                }
                            });
                        } else {
                            $(".check-all-mod").prop("checked", false);
                        }
                        app.chkModHandler(".check-all-mod", ".btn-mass-verif");
                    });
                    $(document).on("click", ".check-all-mod", function () {
                        app.chkModHandler(".check-all-mod", ".btn-mass-verif");
                    });

                    /* Mobile */
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
