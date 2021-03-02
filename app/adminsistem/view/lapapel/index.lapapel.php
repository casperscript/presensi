<style>
    .btnX {
        padding: 0 1rem !important;
    }
</style>
<body class="search-app quick-results-off">
    <?php $this->getView('adminsistem', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminsistem', 'main', 'header', ''); ?>    
        <?php $this->getView('adminsistem', 'main', 'menu', ''); ?>

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
                                            <span class="secondary-stats"><?= $breadcrumb; ?></span>
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
                            <form id="frmIndex" class="navbar-search expanded" onsubmit="return false" role="search" >
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
                        <div id="preloader" style="display: none;">
                            <div class="col s12 center-align" style="margin: 20px">
                                <div class="preloader-wrapper big active">
                                    <div class="spinner-layer spinner-green-only">
                                        <div class="circle-clipper left">
                                            <div class="circle"></div>
                                        </div><div class="gap-patch">
                                        <div class="circle"></div>
                                        </div><div class="circle-clipper right">
                                        <div class="circle"></div>
                                        </div>
                                    </div>
                                </div><br>
                                <b class="green-text text-darken-2">proses buat laporan</b>
                            </div>
                        </div>
                        <form id="frmData" class="navbar-search expanded fileDownloadForm" method="get">
                            <div class="card-action row" style="padding-bottom: 0px" id="tombol">
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('batas', '10'); ?>
                                <?= comp\MATERIALIZE::inputKey('kdlokasi', ''); ?>

                                <div class="input-field col s12">
                                    <div class="col s12">
                                        <div class="input-field col s1">
                                            <span>Periode</span>
                                        </div>
                                        <div class="input-field col s3">
                                            <i class="fa fa-calendar prefix"></i>
                                            <?= comp\MATERIALIZE::inputText('sdate', 'text', $sdate, 'class="datepicker picker_input active"') ?>
                                            <label for="sdate" class="active">Tanggal Awal</label>
                                        </div>
                                        <div class="input-field col s3">
                                            <i class="fa fa-calendar prefix"></i>
                                            <?= comp\MATERIALIZE::inputText('edate', 'text', $edate, 'class="datepicker picker_input"') ?>
                                            <label for="edate" class="active">Tanggal Akhir</label>
                                        </div>
                                        <div class="input-field col s3">
                                            <button class="btn btnX waves-effect waves-light green darken-2" type="submit">
                                                <i class="material-icons left">library_books</i> Buat Laporan
                                            </button>
                                        </div>
                                        <div class="input-field col s1">
                                            <button class="btn-floating waves-effect waves-light red btnBack">
                                                <i class="material-icons left" title="Kembali">reply</i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content" style="padding-top: 0px">
                                <div class="progress" id="progress" style="display: none">
                                    <div class="indeterminate"></div>
                                </div>
                                <div id="data-tabel"></div>
                            </div>
                        </form>
                    </div>
                    <!-- end Tombol Navigasi Tabel -->

                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
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

            $(document).on("submit", "#frmIndex", function () {
                app.showTabel(this);
            });

            $(document).on("submit", "#frmData", function (e) {
                e.preventDefault();
                app.buatLap();
            });

            $(document).on("click", "#showTabel .btnBack", function () {
                app.showIndex();
            });

            $(document).on("change", "#kd_kelompok_lokasi_kerja", function () {
                var dt = $("#kd_kelompok_lokasi_kerja").val();
                app.showPilSatker(dt);
            });

            $("#showTabel").on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

            $('.datepicker').pickadate({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 15, // Creates a dropdown of 15 years to control year
                formatSubmit: 'yyyy-mm-dd'
            });

        })(jQuery);
    </script>
</body>