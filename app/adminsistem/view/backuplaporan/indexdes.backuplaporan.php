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
                            <form id="frmData" class="navbar-search expanded" role="search" method="post">
                                <div class="row">
                                    <div style="clear: both"></div>
                                    <div class="input-field col s2">
                                        <select name="bulan" id="pilihbulan">
                                            <?php
                                            $namabulan = [12 => 'Desember'];

                                            foreach ($namabulan as $key => $i) {
                                                echo '<option value="' . ($key) . '">' . $i . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <label>Pilih Bulan</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <select name="tahun" id="pilihtahun">
                                            <?php
                                            for ($i = 2018; $i <= date('Y'); $i++) {
                                                $selected = "";
                                                $tahun = date('Y');
                                                if (date('m') == 1) :
                                                    $tahun--;
                                                endif;

                                                if ($i == $tahun) :
                                                    $selected = "selected";
                                                endif;

                                                echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <label>Pilih Tahun</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <button class="btn-floating btn waves-effect waves-light green btnList" title="Tampilkan" type="button">
                                            <i class="material-icons left">search</i>
                                        </button>
                                    </div>
                                </div>
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
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
    <link href="assets/css/datatables.css" rel="stylesheet">
    <style>
        .sweet-alert {
            width: 50%;
            margin-left: 0;
            left: 25%;
        }
    </style>

    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="assets/js/datatables.js"></script>
    <!-- ./wrapper -->
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>

    <script>
        (function($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $(document).on("click", ".btnList", function() {
                $("#page").val(1);
                app.loadTabelListDes();
            });
        })(jQuery);
    </script>
</body>