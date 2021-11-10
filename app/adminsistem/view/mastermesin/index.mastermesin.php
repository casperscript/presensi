<body class="search-app quick-results-off loaded">
    <?php $this->getView('adminsistem', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminsistem', 'main', 'header', ''); ?>
        <?php $this->getView('adminsistem', 'main', 'menu', ''); ?>

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
                        <div class="card-action" style="padding-bottom: none;">
                            <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search">
                                <div class="row" style="margin-bottom: 0;">
                                    <div class="input-field col s3 custom-prefix">
                                        <i class="material-icons prefix">search</i>
                                        <input name="cari" id="cari" class="validate" type="text" placeholder="Pencarian Data">
                                    </div>
                                    <div class="input-field col s1">
                                        <?= comp\MATERIALIZE::inputSelect('limit', $listShowData, 10, ' ') ?>
                                        <label>Tampilkan</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <?= comp\MATERIALIZE::inputSelect('kelompok', $nama_kelompok, 0, '') ?>
                                        <label>Kelompok</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <?= comp\MATERIALIZE::inputSelect('status', $listStatus, 'enable', '') ?>
                                        <label>Status</label>
                                    </div>
                                    <div class="input-field col s3">
                                        <button type="submit" class="waves-effect waves-light btn green"><i class="material-icons">search</i></button>
                                        <a id="" class="waves-effect waves-light btn blue btnForm">Tambah</a>
                                    </div>
                                    <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <div id="data-tabel"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div id="frmInputModal" class="modal">
            <form id="frmInput" class="form-horizontal" role="form" onsubmit="return false" autocomplete="off">
                <div class="modal-content">
                    <div id="data-form-input"></div>
                </div>
                <div class="modal-footer right-align">
                    <button id="btnSubmitSimpan" type="submit" class="waves-effect waves-light btn green">Simpan</button>
                    <button type="button" class="modal-close waves-effect waves-light btn red">Batal</button>
                </div>
            </form>
        </div>

        <div id="confirmModal" class="modal">
            <form id="frmHapus" class="form-horizontal" role="form" onsubmit="return false" autocomplete="off">
                <div class="modal-content">
                    <h4 class="modal-title" id="myConfirmModalLabel"></h4>
                    <div id="data-confirm"></div>
                    <input type="hidden" id="id_confirm" value="">
                </div>
                <div class="modal-footer right-align">
                    <button id="btnConfirmHapus" type="submit" class="waves-effect waves-light btn red">Hapus</button>
                    <button type="button" class="modal-close waves-effect waves-light btn green">Batal</button>
                </div>
            </form>
        </div>
        <!-- /.modal -->
        <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            app.loadTabel();

            $(document).on("change", "#limit, #kelompok, #status", function () {
                app.loadTabel();
            }) ;

            $(document).on("submit", "#frmData", function() {
                $("#page").val(1);
                app.loadTabel();
            });

            $(document).on("click", ".btnForm", function() {
                app.showForm(this.id);
            });

            $(document).on("submit", "#frmInput", function() {
                app.simpan(this);
            });

            $(document).on("click", ".btnHapus", function() {
                var title = "Perhatian!!";
                var msg = "Anda akan menghapus " + $(this).attr("nama");
                app.hapus(this.id, title, msg);
            });

            $(document).on("click", ".paging", function() {
                app.tabelPagging($(this).attr("number-page"));
            });

        })(jQuery);
    </script>
</body>