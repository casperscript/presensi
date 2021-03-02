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
                    <div id="showTabel" class="card stats-card">
                        <div class="card-action" style="padding-bottom: 0px">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
                                <div class="row">
                                    <div class="input-field col s5">
                                        <?= comp\MATERIALIZE::inputSelect('kelompok', $pil_kel_satker, '') ?>
                                        <label>Kelompok Lokasi Kerja</label>
                                    </div>
                                    <div class="input-field col s5">
                                        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_satker, '') ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <button type="submit" class="waves-effect waves-light btn green">AMBIL DATA</button>
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
                            <div id="data-detail"></div>
                        </div>
                    </div>
                    <!-- End Detail -->
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

            $(document).on("submit", "#frmData", function () {
                $("#page").val(1);
                app.loadTabel();
            });

            $(document).on("submit", "#frmDataDetail", function () {
                $("#pageDetail").val(1);
                app.loadRecord();
            });
            
            $(document).on("click", ".btnDetail", function () {
                app.showDetail(this.id);
            });
            $(document).on("click", ".btnBack", function () {
                app.showTabel(this.id);
            });
            
            $("#showTabel").on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

            $("#showDetail").on("click", ".paging", function () {
                app.detailPagging($(this).attr("number-page"));
            });

            $(document).on("change", "#kelompok", function () {
                var dt = $('#kelompok :selected').val();
                app.showPilSatker(dt, '#kdlokasi');
            });

            $('.datepicker').pickadate({
                selectMonths: true, 
                selectYears: true,
                formatSubmit: 'yyyy-mm-dd'
            });
        })(jQuery);
    </script>
</body>