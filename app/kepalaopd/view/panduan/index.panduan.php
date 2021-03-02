<body class="search-app quick-results-off loaded">
    <?php $this->getView('kepalaopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('kepalaopd', 'main', 'header', ''); ?>    
        <?php $this->getView('kepalaopd', 'main', 'menu', ''); ?>

        <main class="mn-inner" style="background-color:#cfd8dc;">
            <div class="search-header">
                <div class="card card-transparent no-m">
                    <div class="card-content no-s">
                        <div class="z-depth-1 search-tabs">
                            <div class="search-tabs-container">
                                <div class="col s12 m12 l12">
                                    <div class="row search-tabs-row search-tabs-container blue-grey white-text">
                                        <div class="col s12 m6 l6">
                                            <span style="line-height: 48px;text-transform: uppercase;"><?= $title; ?></span>
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
                    <div class="card stats-card">
                        <div class="card-content">
                            <div class="col s12">
                                <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" enctype="multipart/form-data">
                                    <div class="input-field col s7 custom-prefix">
                                        <i class="material-icons prefix">search</i>
                                        <input name="cari" id="cari" class="validate" type="text" placeholder="Pencarian Data">
                                    </div>
                                    <div class="input-field col s2">
                                        <button type="submit" class="waves-effect waves-light btn green">Cari</button>
                                    </div>
                                    <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                </form>
                            </div>
                            <div class="col s12">
                                <div id="data-tabel"></div>
                            </div>
                        </div>
                    </div>
                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>

        <?php $this->getView('kepalaopd', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            app.loadTabel();

            $(document).on("submit", "#frmData", function () {
                $("#page").val(1);
                app.loadTabel();
            });

            $(document).on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

        })(jQuery);
    </script>
</body>
