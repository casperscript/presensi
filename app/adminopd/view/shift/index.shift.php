<body class="search-app quick-results-off">
    <link href="assets/css/planning.css" rel="stylesheet">
    <?php $this->getView('adminopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>
        <?php $this->getView('adminopd', 'shift', 'modalInput', ''); ?>

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
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Pengaturan</a>
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Shift Kerja</a>
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
                        <div class="card-action" style="padding-bottom: 0px">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search">
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('id_shift', '') ?>
                                <div class="row">
                                    <div class="right">
                                        <div class="input-field col">
                                            <label for="sdate" class="active">Tanggal Awal</label>
                                            <?= comp\MATERIALIZE::inputText('sdate', 'text', '', 'class="datepicker picker_input" size="10"') ?>
                                        </div>
                                        <div class="input-field col">
                                            <label for="edate" class="active">Tanggal Akhir</label>
                                            <?= comp\MATERIALIZE::inputText('edate', 'text', '', 'class="datepicker picker_input" size="10"') ?>
                                        </div>
                                        <div class="input-field col">
                                            <button class="btn waves-effect waves-light green" type="submit">
                                                Cari
                                            </button>
                                            <a id="" class="btn waves-effect waves-light blue btnInputShift" title="Input Data">
                                                Tambah
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-content" style="padding-top: 0px">
                            <!-- Spinner -->
                            <div id="showSpinner" class="row" style="display: none; margin-top: 100px">
                                <?php $this->getView('adminopd', 'main', 'spinner', '') ?>
                            </div>
                            <!-- end Spinner -->

                            <!-- Data Tabel -->
                            <div id="data-tabel" style="display: none"></div>
                            <!-- end Data Tabel -->

                            <!-- Form Input -->
                            <div id="form-tabel" style="display: none"></div>
                            <!-- end Form Input -->
                        </div>

                        <div id="sparkline-bar"></div>
                    </div>
                </div>
            </div>
        </main>
        <?php $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
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
                                $("#showDetail").on("submit", "#frmData", function () {
                                    $("#page").val(1);
                                    app.loadRecord();
                                });

                                $(".navShift").on("click", ".btnToNavSatker", function () {
                                    $(".navShift, #data-tabel").fadeOut(300).promise().then(function () {
                                        $(".navSatker").fadeIn(300);
                                    });
                                });
                                $(document).on("click", ".btnInputShift", function () {
                                    var id_satker = $("#kdlokasi").val();
                                    app.showForm(this.id);
                                });

                                $("#showTabel").on("click", ".paging", function () {
                                    app.tabelPagging($(this).attr("number-page"));
                                });
                                
                                $(document).on("submit", "#frmInputModal #frmInput", function () {
                                    var opForm = $("#op").val();
                                    if (opForm == "addShift") {
                                        ($("#status_shift").is(":checked")) ? $("#status_shift").val("publish") : $("#status_shift").val("draft");
                                        app.simpan(this);
                                    } else if (opForm == "addJamKerja") {
                                        app.simpanJam(this);
                                    } else if (opForm == "editShift") {
                                        app.simpan(this);
                                    }
                                });
                            })(jQuery);

                            $('.datepicker').pickadate({
                                selectMonths: true, // Creates a dropdown to control month
                                selectYears: 15, // Creates a dropdown of 15 years to control year
                                format: 'yyyy-mm-dd',
                                container: 'body'
                            });
    </script>
</body>