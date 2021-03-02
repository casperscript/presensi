<body class="search-app quick-results-off">
    <link href="assets/css/planning.css" rel="stylesheet">
    <?php $this->getView('adminopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>
        <?php $this->getView('adminopd', 'shift', 'modalInput', ''); ?>

        <main class="mn-inner">
            <div class="search-header">
                <div class="card card-transparent no-m">
                    <div class="card-content no-s">
                        <div class="z-depth-1 search-tabs">
                            <div class="search-tabs-container">
                                <div class="col s12 m12 l12">
                                    <div class="row search-tabs-row search-tabs-container blue-grey white-text">
                                        <div class="col s12 m6 l6">
                                            <span style="line-height: 48px;text-transform: uppercase;"><?= $title ?></span>
                                        </div>
                                        <div class="col s12 m6 l6 right-align search-stats">
                                            <span class="secondary-stats">
                                                <a style="font-size: 13px;" class="breadcrumb white-text" href="<?= $this->link('') ?>">Index</a>
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Pengaturan</a>
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Jadwal Kerja</a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <!-- Show Tabel Personal -->
                    <div id="showTabel" class="card stats-card">
                        <div class="card-action navSatker" style="padding-bottom: 0px">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" autocomplete="off">
                                <div class="row no-m-b">
                                    <?= comp\MATERIALIZE::inputKey('page', '1') ?>
                                    <div class="input-field col s12 m6">
                                        <h5>Daftar Pegawai</h5>
                                    </div>
                                    <div class="input-field col s6 m4 custom-prefix">
                                        <i class="material-icons prefix">search</i>
                                        <?= comp\MATERIALIZE::inputText('nama', 'text', '', 'class="validate"') ?>
                                        <label for="nama">Cari nama pegawai atau personil</label>
                                    </div>
                                    <div class="input-field col s4 m2">
                                        <button class="waves-effect waves-light btn green" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
                            <?php // comp\FUNC::showPre($data); ?>
                            <!-- Spinner -->
                            <div id="showSpinner" class="row" style="display: none; margin-top: 100px">
                                <?php $this->getView('adminopd', 'main', 'spinner', '') ?>
                            </div>
                            <!-- end Spinner -->

                            <!-- Data Tabel -->
                            <div id="data-tabel" style="display: none"></div>
                            <!-- end Data Tabel -->

                            <!-- Detail Jadwal -->
                            <div id="detail-tabel" style="display: none"></div>
                            <!-- end Detail Jadwal -->
                        </div>
                    </div>
                    <!-- End Tabel -->

                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
                                (function ($) {
                                    "use strict";
                                    app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
                                    app.loadTabel();

                                    $(document).on("click", ".btnDetail", function () {
                                        app.loadDetail(this.id);
                                    });

                                    $(document).on("click", ".btnToNavSatker", function () {
                                        $("#detail-tabel").fadeOut(300).promise().then(function () {
                                            $(".navSatker").fadeIn(300);
                                            app.loadTabel();
                                        });
                                    });
                                    $(document).on("submit", "#frmData", function () {
                                        app.loadTabel();
                                    });
                                    $(document).on("submit", "#frmInput", function () {
                                        app.simpan(this);
                                    });
                                    // $(document).on("click", ".btnDelete", function () {
//                                        alert(this.id);
                                        // app.hapus(this.id);
                                    // });
                                    $("#detail-tabel").on("click", ".btnDelete", function () {
                                        var pin_absen = $("#detail-tabel #pin_absen").val();
                                        app.hapus(this.id, pin_absen);
                                    });
                                    /* ************** */

                                    $(".navShift").on("click", ".btnInputShift", function () {
                                        var id_satker = $(".navShift #kdlokasi").val();
                                        app.showForm(this.id, id_satker);
                                    });
                                    $(document).on("change", "#kd_kelompok_lokasi_kerja", function () {
                                        var dt = $("#kd_kelompok_lokasi_kerja").val();
                                        app.showPilSatker(dt);
                                    });

                                    $("#showTabel").on("click", ".paging", function () {
                                        var page = $(this).attr("number-page");
//                                        alert(page);
                                        app.tabelPagging(page);
                                    });
                                })(jQuery);
    </script>
</body>