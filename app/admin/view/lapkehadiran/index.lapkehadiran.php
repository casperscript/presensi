<style>
    .btnX {
        padding: 0 1rem !important;
    }
</style>
<body class="search-app quick-results-off">
    <?php $this->getView('admin', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('admin', 'main', 'header', ''); ?>    
        <?php $this->getView('admin', 'main', 'menu', ''); ?>
        <?php $this->getView('admin', 'main', 'modalInput', ''); ?>

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
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Laporan</a>
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Presensi</a>
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
                    <!-- Tombol Navigasi Index -->
                    <div id="showIndex" class="card stats-card">
                        <div class="card-action" style="padding-bottom: 0px">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
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
                    </div>
                    <!-- end Tombol Navigasi Index -->

                    <!-- Tombol Navigasi Tabel -->
                    <div id="showTabel" class="card stats-card" style="display: none">
                        <form id="frmData" class="navbar-search expanded fileDownloadForm" method="get" action="<?= $this->link($this->getController() . '/pdfKehadiran') ?>">
                            <div class="card-action row" style="padding-bottom: 0px">
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('batas', '10'); ?>
                                <?= comp\MATERIALIZE::inputKey('kdlokasi', ''); ?>

                                <div class="input-field col s12">
                                    <div class="col s12">
                                        <div class="input-field col s1">
                                            <span>Periode</span>
                                        </div>
                                        <div class="input-field col">
                                            <i class="fa fa-calendar prefix"></i>
                                            <?= comp\MATERIALIZE::inputText('sdate', 'text', $sdate, 'class="datepicker picker_input active"') ?>
                                            <label for="sdate" class="active">Tanggal Awal</label>
                                        </div>
                                        <div class="input-field col">
                                            <i class="fa fa-calendar prefix"></i>
                                            <?= comp\MATERIALIZE::inputText('edate', 'text', $edate, 'class="datepicker picker_input"') ?>
                                            <label for="edate" class="active">Tanggal Akhir</label>
                                        </div>
                                        <div class="input-field col">
                                            <label for="sdate" class="active">Status</label>
                                            <?= comp\MATERIALIZE::inputSelect('stat', $pil_status, '', 'class="datepicker picker_input active"') ?>
                                        </div>
                                    </div>
                                    <div class="col s12">
                                        <div class="input-field col s1">
                                            <a class="btn-floating waves-effect waves-light red btnBack">
                                                <i class="material-icons left" title="Kembali">reply</i>
                                            </a>
                                        </div>
                                        <div class="input-field col s11">
                                            <a class="btn btnX waves-effect waves-light green darken-2 btnKehadiran">
                                                <i class="material-icons left">library_books</i> Kehadiran
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Spinner -->
                            <div id="showSpinner" class="row" style="display: none; margin-top: 100px">
                                <?php $this->getView('admin', 'main', 'spinner', '') ?>
                            </div>
                            <!-- end Spinner -->
                            <div id="data-tabel" class="card-content dataTables_wrapper"></div>
                        </form>
                    </div>
                    <!-- end Tombol Navigasi Tabel -->

                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('admin', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="assets/plugins/jquery-download/jquery.fileDownload.js"></script>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
                                (function ($) {
                                    "use strict";
                                    app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

                                    $(document).on("click", "#showTabel .btnBack", function () {
                                        app.showIndex();
                                    });
                                    $(document).on("click", "#showTabel .btnKehadiran", function () {
                                        var frm = $("#showTabel #frmData");
                                        app.printKehadiran(frm);
                                    });

                                    $(document).on("submit", "#showIndex #frmData", function () {
                                        app.showTabel(this);
                                    });
                                    $(document).on("submit", "#showTabel #frmData", function () {
                                        $("#page").val(1);
                                        app.loadRecord(this);
                                    });


                                    $(document).on("change", "#kd_kelompok_lokasi_kerja", function () {
                                        var dt = $("#kd_kelompok_lokasi_kerja").val();
                                        app.showPilSatker(dt);
                                    });

                                    $("#showTabel").on("click", ".paging", function () {
                                        app.tabelPagging($(this).attr("number-page"));
                                    });

                                })(jQuery);


    </script>
</body>