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
        <?php $this->getView('adminsistem', 'main', 'modalInput', ''); ?>

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
                                                <a style="font-size: 13px;" class="breadcrumb white-text">User Mesin</a>
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
                    <div class="card stats-card">
                        <!-- Tombol Navigasi Index -->
                        <div id="showIndex">
                            <div class="card-action" style="padding-bottom: 0px">
                                <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
                                    <div class="row">
                                        <div class="input-field col s4">
                                            <?= comp\MATERIALIZE::inputSelect('kd_kelompok_lokasi_kerja', $pil_kel_satker, $kel_satker) ?>
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
                        <div id="showTabel" style="display: none">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false">
                                <?= comp\MATERIALIZE::inputKey('page', 1) ?>
                                <?= comp\MATERIALIZE::inputKey('batas', 10) ?>
                                <?= comp\MATERIALIZE::inputKey('kdlokasi', '') ?>
                                <div class="card-action row" style="padding-bottom: 0px">
                                    <div class="col s6">
                                        <ul class="tabs z-depth-1">
                                            <li id="userdinas" class="tab col s3">
                                                <a href="javascript:void(0)" class="active">User Kedinasan</a>
                                            </li>
                                            <li id="userfinger" class="tab col s3">
                                                <a href="javascript:void(0)" class="">User pada Fingerprint</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col s6 right-align">
                                        <a class="btn-floating waves-effect waves-light red btnBack">
                                            <i class="material-icons left" title="Kembali">reply</i>
                                        </a>
                                        <a class="btn btnX waves-effect waves-light green darken-2 btnBackup">
                                            <i class="material-icons left">library_books</i> Download Data
                                        </a>
                                    </div>
                                </div>
                                <!-- Spinner -->
                                <div id="showSpinner" class="row" style="display: none">
                                    <?php $this->getView('adminsistem', 'usermesin', 'spinner', '') ?>
                                </div>
                                <!-- end Spinner -->
                                <div id="data-userdinas" class="card-content"></div>
                                <div id="data-userfinger" class="card-content"></div>
                            </form> 
                        </div>
                        <!-- end Tombol Navigasi Tabel -->

                        <!--<div id="sparkline-bar"></div>-->
                    </div>
                </div>
            </div>
        </main>
        <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->
    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    
    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="assets/plugins/jquery-download/jquery.fileDownload.js"></script>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
                                (function ($) {
                                    "use strict";
                                    app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
                                    app.showPilSatker("#kd_kelompok_lokasi_kerja");

                                    $("#showIndex").on("submit", "#frmData", function () {
                                        app.showTabel();
                                    });

                                    $(document).on("change", "#kd_kelompok_lokasi_kerja", function () {
                                        app.showPilSatker(this);
                                    });

                                    $("#showTabel").on("click", ".btnBack", function () {
                                        app.showIndex();
                                    });
                                    $("#showTabel").on("click", ".btnBackup", function () {
                                        app.getBackupFinger();
                                    });
                                    $("#showTabel").on("click", "#userdinas", function () {
                                        app.loadUserDinas();
                                    });
                                    $("#showTabel").on("click", "#userfinger", function () {
                                        app.loadUserFinger();
                                    });
                                    $("#showTabel").on("click", ".paging", function () {
                                        app.tabelPagging($(this).attr("number-page"));
                                    });
                                    $(".tabs").tabs();

                                })(jQuery);
    </script>
</body>