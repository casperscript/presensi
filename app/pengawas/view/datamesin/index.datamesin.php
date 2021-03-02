<body class="search-app quick-results-off">
    <?php $this->getView('pengawas', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('pengawas', 'main', 'header', ''); ?>    
        <?php $this->getView('pengawas', 'main', 'menu', ''); ?>
        <?php $this->getView('pengawas', 'main', 'modalInput', ''); ?>

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
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Presensi</a>
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Data Kehadiran Personal</a>
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
                        <div class="card-action" style="padding-bottom: 0px">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <div class="row">
                                    <div class="input-field col s4">
                                        <?= comp\MATERIALIZE::inputSelect('kd_kelompok_lokasi_kerja', $pil_kel_satker, '') ?>
                                        <label>Kelompok Lokasi Kerja</label>
                                    </div>
                                    <div class="input-field col s7">
                                        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_satker, '') ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s1">
                                        <button class="btn-floating btn waves-effect waves-light green" title="Kirim" type="submit">
                                            <i class="material-icons left">search</i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
                            <div id="showSpinner" class="row" style="display: none; margin-top: 100px">
                                <?php $this->getView('pengawas', 'main', 'spinner', '') ?>
                            </div>
                            <div id="data-tabel"></div>
                        </div>
                    </div>
                    <!-- End Tabel -->

                    <!-- Show Detail Record Personal -->
                    <div id="showDetail" class="card stats-card" style="display: none">
                        <div class="card-action" style="padding-bottom: 0px">
                            <form id="frmDataDetail" class="navbar-search expanded" onsubmit="return false" role="search" >
                                <?= comp\MATERIALIZE::inputKey('pageDetail', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('pin_absen', ''); ?>
                                <div class="row">
                                    <div class="input-field col s6" style="margin-top: 0px">
                                        <h5 id="nama_personil"></h5>
                                        <h6 id="nip_personil"></h6>
                                    </div>
                                    <div class="input-field col">
                                        <label for="sdate" class="active">Tanggal Awal</label>
                                        <?= comp\MATERIALIZE::inputText('sdate', 'text', '', 'class="datepicker picker_input active"') ?>
                                    </div>
                                    <div class="input-field col">
                                        <label for="edate" class="active">Tanggal Akhir</label>
                                        <?= comp\MATERIALIZE::inputText('edate', 'text', '', 'class="datepicker picker_input"') ?>
                                    </div>
                                    <div class="input-field col">
                                        <button class="btn-floating btn waves-effect waves-light green" type="submit">
                                            <i class="material-icons left">search</i>
                                        </button>
                                        <button class="btn-floating waves-effect waves-light red btnBack" title="Kembali">
                                            <i class="material-icons left">reply</i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
                            <div id="showSpinner" class="row" style="display: block; margin-top: 100px">
                                <?php $this->getView('pengawas', 'main', 'spinner', '') ?>
                            </div> <!-- end Spinner -->
                            <div id="data-detail"></div>
                        </div>
                    </div>
                    <!-- End Detail -->
                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('pengawas', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
                                (function ($) {
                                    "use strict";
                                    app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
//                                    app.loadTabel();
                                    $(document).on("submit", "#frmData", function () {
                                        $("#page").val(1);
                                        app.loadTabel();
                                    });
                                    $(document).on("submit", "#frmDataDetail", function () {
                                        $("#pageDetail").val(1);
                                        app.loadRecord();
                                    });

                                    $(document).on("click", ".btnForm", function () {
                                        app.showForm(this.id);
                                    });
                                    $(document).on("click", ".btnDetail", function () {
                                        app.showDetail(this.id);
                                    });
                                    $(document).on("click", ".btnBack", function () {
                                        app.showTabel(this.id);
                                    });
                                    $(document).on("change", "#kd_kelompok_lokasi_kerja", function () {
                                        var dt = $("#kd_kelompok_lokasi_kerja").val();
                                        app.showPilSatker(dt);
                                    });

                                    $("#showTabel").on("click", ".paging", function () {
                                        app.tabelPagging($(this).attr("number-page"));
                                    });
                                    $("#showDetail").on("click", ".paging", function () {
                                        app.detailPagging($(this).attr("number-page"));
                                    });
                                })(jQuery);

                                $('.datepicker').pickadate({
                                    selectMonths: true, // Creates a dropdown to control month
                                    selectYears: 15, // Creates a dropdown of 15 years to control year
                                    format: 'yyyy-mm-dd'
                                });
    </script>
</body>