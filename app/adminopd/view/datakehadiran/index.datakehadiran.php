<style>
    .loading {
        overflow: hidden;
    }

    /* Anytime the body has the loading class, our
       modal element will be visible */
    .loading .modalloading {
        display: block;
    }

</style>

<body class="search-app quick-results-off loading">
    <?php $this->getView('adminopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>
        <?php $this->getView('adminopd', 'main', 'modalInput', ''); ?>

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
                                                <a style="font-size: 13px;" class="breadcrumb white-text">Data Kehadiran Personal</a>
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
                    <!-- Show Tabel -->
                    <div id="showTabel" class="card stats-card">
                        <div class="card-action" style="padding-bottom: 0px">
                            <?php // comp\FUNC::showPre($data) ?>
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" autocomplete="off">
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <div class="row no-s">
                                    <div class="input-field col s12 m4 l5">
                                        <?php
                                        foreach ($listMesin as $key => $val) {
                                            ?>
                                            <button id="<?= $key ?>" type="button" title="<?= $val ?>" class="btn orange waves-effect waves-effect m-b-xs btnUpdate" onclick="app.updateMesin(this.id); this.disabled = true;">
                                                <?= /* $val */'Ambil Data Finger' ?>
                                            </button>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="col s18 m6 l5 offset-m4 right">
                                        <div class="input-field col s9">
                                            <i class="material-icons prefix">search</i>
                                            <?= comp\MATERIALIZE::inputText('cari', 'text', '', 'onkeyup="app.loadTabel()"') ?>
                                            <label for="cari">Cari nama personil</label>
                                        </div>
                                        <div class="input-field col s3">
                                            <label for="batas" class="active">Tampilkan</label>
                                            <?= comp\MATERIALIZE::inputSelect('batas', array(5 => 5, 10 => 10, 25 => 25, 50 => 50, 1000 => 'Semua'), '10') ?>
                                        </div>
                                    </div>
                                    <div class="progress no-s">
                                        <div class="indeterminate" style="display: none"></div>
                                        <div class="determinate" style="width: 100%; display: none"></div>
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

                            <!-- Show Tabel Personal -->
                            <div id="data-tabel"></div>
                            <!-- End Tabel -->
                        </div>
                    </div>
                    <!-- End Tabel -->

                    <!-- Show Detail Record Personal -->
                    <div id="showDetail" class="card stats-card" style="display: none">
                        <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
                            <div class="card-action" style="padding-bottom: 0px">
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <div class="row">
                                    <div class="input-field col s12 m4 l6" style="margin-top: 0px">
                                        <div class="input-field col s12">
                                            <label for="pin_absen" class="active">Pilih Nama Pegawai</label>
                                            <?= comp\MATERIALIZE::inputSelect('pin_absen', $listPersonil, '', 'class="select2" style="width: 100%"') ?>
                                        </div>
                                    </div>
                                    <div class="col s12 m8 l6">
                                        <div class="input-field col s3">
                                            <label for="sdate" class="active">Tanggal Awal</label>
                                            <?= comp\MATERIALIZE::inputText('sdate', 'text', '', 'class="datepicker picker_input active"') ?>
                                        </div>
                                        <div class="input-field col s3">
                                            <label for="edate" class="active">Tanggal Akhir</label>
                                            <?= comp\MATERIALIZE::inputText('edate', 'text', '', 'class="datepicker picker_input"') ?>
                                        </div>
                                        <div class="input-field col s3">
                                            <label for="batas" class="active">Tampilkan</label>
                                            <?= comp\MATERIALIZE::inputSelect('batas', array(5 => 5, 10 => 10, 25 => 25, 50 => 50, 1000 => 'Semua'), '10') ?>
                                        </div>
                                        <div class="input-field col s3">
                                            <button class = "btn-floating btn waves-effect waves-light green" type = "submit">
                                                <i class = "material-icons left">search</i>
                                            </button>
                                            <button class = "btn-floating waves-effect waves-light red btnBack" title = "Kembali">
                                                <i class = "material-icons left">reply</i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content" style="padding-top: 0px">
                                <div id="showSpinner" class="row" style="display: block; margin-top: 100px">
                                    <?php $this->getView('adminopd', 'main', 'spinner', '') ?>
                                </div> <!-- end Spinner -->

                                <div id="data-detail"></div>  <!-- detail record -->

                            </div>
                        </form>
                    </div>
                    <!-- End Detail -->
                    <div id="sparkline-bar"></div>
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
                                $(".select2").select2({
                                    width: "resolve"
                                });

                                app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
                                app.loadTabel();

                                $(document).on("click", ".btnForm", function () {
                                    app.showForm(this.id);
                                });
                                $(document).on("click", ".btnDetail", function () {
                                    app.showDetail(this.id);
                                });
                                $(document).on("click", ".btnBack", function () {
                                    app.showTabel(this.id);
                                });

                                // Tabel Karyawan
                                $("#showTabel").on("change", "#frmData", function () {
                                    $("#showTabel #page").val(1);
                                    app.loadTabel();
                                });
                                $("#showTabel").on("click", ".paging", function () {
                                    app.tabelPagging($(this).attr("number-page"));
                                });

                                // Detail Record
                                $("#showDetail").on("submit", "#frmData", function () {
                                    $("#showDetail #page").val(1);
                                    app.loadRecord();
                                });
                                $("#showDetail").on("click", ".paging", function () {
                                    app.detailPagging($(this).attr("number-page"));
                                });
                                $("#showDetail").on("change", "#pin_absen", function () {
                                    app.loadRecord();
                                });

                                // Action Form
                                $(document).on("submit", "#frmInput", function () {
                                    app.simpan(this);
                                });
                            })(jQuery);

                            $(".datepicker").pickadate({
                                selectMonths: true, // Creates a dropdown to control month
                                selectYears: 15, // Creates a dropdown of 15 years to control year
                                format: 'yyyy-mm-dd',
                                closeOnSelect: true
                            });
                            $(".batas").material_select();
    </script>
</body>