<body class="search-app quick-results-off">
    <?php $this->getView('pns', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('pns', 'main', 'header', ''); ?>    
        <?php $this->getView('pns', 'main', 'menu', ''); ?>

        <main class="mn-inner">
            
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
                    <!-- Show Detail Record Personal -->
                    <div class="card stats-card">
                        <div class="card-action" style="padding-bottom: 0px">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('pin_absen', $personil['pin_absen']); ?>
                                <div class="row">
                                    <div class="input-field col s6" style="margin-top: 0px">
                                        <h5 id="nama_personil"><?= $personil['nama_personil'] ?></h5>
                                        <h6 id="nip_personil"><?= $personil['nipbaru'] ?></h6>
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
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
                            <div id="data-detail"></div>
                        </div>
                    </div>
                    <!-- End Detail -->
                    <div id="sparkline-bar"></div>
                </div>
            </div>
        </main>
        <?php $this->getView('pns', 'main', 'footer', ''); ?>
    </div>

   <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $(document).on("submit", "#frmData", function () {
                $("#page").val(1);
                app.loadTabel();
            });

            $(document).on("click", ".paging", function () {
                console.log($(this).attr("number-page"));
                app.tabelPagging($(this).attr("number-page"));
            });

            $('.datepicker').pickadate({
                selectMonths: true, 
                selectYears: true,
                formatSubmit: 'yyyy-mm-dd'
            });
        })(jQuery);
    </script>
</body>