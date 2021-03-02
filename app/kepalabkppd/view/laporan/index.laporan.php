<body class="search-app quick-results-off">
    <?php $this->getView('kepalabkppd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('kepalabkppd', 'main', 'header', ''); ?>    
        <?php $this->getView('kepalabkppd', 'main', 'menu', ''); ?>

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
                    <div class="card stats-card">
                        <div class="card-action">
                            <div class="row">
                                <div class="input-field col s3">
                                    <?= comp\BOOTSTRAP::inputSelect('bulan', comp\FUNC::$namabulan1, date('m', strtotime('-1 month')), '') ?>
                                    <label>Pilih Bulan</label>
                                </div>
                                <div class="input-field col s3">
                                    <?= comp\BOOTSTRAP::inputSelect('tahun', $listTahun, date('Y', strtotime('-1 month')), '') ?>
                                    <label>Pilih Tahun</label>
                                </div>
                                <div class="input-field col s2">
                                    <button class="btn-floating btn waves-effect waves-light green btnTampil" title="Tampilkan" type="button">
                                        <i class="material-icons left">search</i>
                                    </button>
                                </div>
                            </div>
                            <div class="progress" id="progress" style="display: none">
                                <div class="indeterminate"></div>
                            </div>
                            <div class="row kolom-laporan" id="showIndex" style="padding-top: 0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php $this->getView('kepalabkppd', 'main', 'footer', ''); ?>
    </div>

    <!-- ./wrapper -->
    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <style>.sweet-alert{width: 50%; margin-left: 0; left: 25%;}</style>
    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        $(function() {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $('.btnTampil').on('click', function(e) {
                app.loadIndex();
            }).click();
        });
    </script>
</body>