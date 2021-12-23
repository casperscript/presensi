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
                    <div class="card stats-card">
                        <div class="card-action" style="padding-bottom: none;">
                            <form id="frmData" class="navbar-search expanded" role="search" onsubmit="return false" autocomplete="off">
                                <div class="row" style="margin-bottom: 0;">
                                    <!-- <div style="clear: both"></div> -->
                                    <div class="input-field col s3 custom-prefix">
                                        <i class="material-icons prefix">search</i>
                                        <?= comp\MATERIALIZE::inputText('cari', 'text', '', 'placeholder="Nama OPD"') ?>
                                        <label>Pencarian</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <?= comp\MATERIALIZE::inputSelect('status', ['' => ':: Semua ::', 'belum' => 'Belum Backup', 'sudah' => 'Sudah Backup'], 'belum', '') ?>
                                        <label>Status Backup</label>
                                    </div>
                                    <div class="input-field col s3">
                                        <select name="kd_tpp" id="kd_tpp">
                                            <?php foreach ($listTPP as $valTPP) : ?>
                                                <option value="<?= $valTPP['kd_tpp'] ?>" <?= (!empty($valTPP['tampil'])) ? 'selected' : '' ?>><?= $valTPP['label'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label>Kategori Backup</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <button class="btn-floating btn waves-effect waves-light green btnList" title="Tampilkan" type="button">
                                            <i class="material-icons left">search</i>
                                        </button>
                                    </div>
                                </div>
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('bulan', '12'); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <div class="progress" id="progress" style="display: none">
                                <div class="indeterminate"></div>
                            </div>
                            <div id="data-tabel"></div>
                        </div>
                    </div>
                    <!-- end Tombol Navigasi Index -->

                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
    </div>

    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
        .sweet-alert {
            width: 50%;
            margin-left: 0;
            left: 25%;
        }
    </style>

    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <!-- ./wrapper -->
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>

    <script>
        // (function($) {
        //     "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
            app.loadTabelListTPP();

            $(document).on("change", "#frmData", function () {
                app.loadTabelListTPP();
            });
            $(document).on("click", ".btnList", function() {
                $("#page").val(1);
                app.loadTabelListTPP();
            });
            $(document).on("click", ".btnBackup", function() {
                app.saveLogTPP(this.id);
            });
            $(document).on("click", ".btnHapusBackup", function () {
                app.hapusLogTPP(this.id);
            });
        // })(jQuery);
    </script>
</body>