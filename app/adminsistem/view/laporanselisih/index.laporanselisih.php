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
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false">
                                <div class="row">
                                    <div class="input-field col s3">
                                        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $listSatker, '', 'class="pilihsatker"') ?>
                                        <label class="active">Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <?php
                                        $selected_bulan = date('m') - 1;
                                        echo comp\BOOTSTRAP::inputSelect('bulan', $listBulan, $selected_bulan, 'class="pilihbulan"');
                                        ?>
                                        <label class="active">Bulan</label>
                                    </div>
                                    <div class="input-field col s1">
                                        <?php
                                        $selected_tahun = (date('m') == 1) ? date('Y') - 1 : date('Y');
                                        echo comp\BOOTSTRAP::inputSelect('tahun', $listTahun, $selected_tahun, 'class="pilihtahun"');
                                        ?>
                                        <label class="active">Tahun</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <?= comp\BOOTSTRAP::inputSelect('format', $listFormat, '', 'class="pilihformat"'); ?>
                                        <label class="active">Format</label>
                                    </div>
                                    <div class="input-field col s1">
                                        <button class="btn-floating btn waves-effect waves-light green btnTampil" title="Tampilkan" type="submit">
                                            <i class="material-icons left">search</i>
                                        </button>
                                    </div>
                                    <div class="input-field col s3">
                                        <div class="chip infoSatker">Empty!!!</div>
                                        <div class="chip infoChip">Empty!!!</div>
                                    </div>
                                </div>
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
                            <!-- Spinner -->
                            <div id="showSpinner" class="row" style="display: none; margin-top: 100px">
                                <?php $this->getView('adminopd', 'main', 'spinner', '') ?>
                            </div>
                            <!-- end Spinner -->

                            <!-- Show tabel -->
                            <div id="showTabel">
                                <div id="data-tabel"></div>
                            </div>
                            <!-- end tabel -->
                        </div>
                    </div>
                    <!-- end Tombol Navigasi Index -->

                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
                                (function ($) {
                                    "use strict";
                                    app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

                                    app.loadTabel();

                                    $("select").select2();

                                    $(document).on("change", "#kdlokasi", function () {
                                        app.loadTabel();
                                    });
                                    $(document).on("submit", "#frmData", function () {
                                        $("#page").val(1);
                                        app.loadTabel();
                                    });
                                })(jQuery);
    </script>
</body>