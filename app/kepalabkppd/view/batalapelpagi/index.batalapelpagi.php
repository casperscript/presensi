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
                                    <div class="input-field col s7 custom-prefix">
                                        <i class="material-icons prefix">search</i>
                                        <input name="cari" id="cari" class="validate" type="text" placeholder="Pencarian Data berdasarkan nama atau pin absen">
                                    </div>
                                    <div class="input-field col s2">
                                        <button type="submit" class="waves-effect waves-light btn green">Cari</button>
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
                </div>
            </div>
        </main>

        <div id="frmInputModal" class="form-apelpagi modal">
            <form id="frmInput" class="form-horizontal" role="form" onsubmit="return false" autocomplete="off">
                <div class="modal-content">
                    <div id="data-form-input"></div>
                </div>
                <div class="modal-footer right-align">
                    <button id="btnSubmitSimpan" type="submit" class="waves-effect waves-light btn green" style="display: none;">Simpan</button>
                    <button type="button" class="modal-close waves-effect waves-light btn red" id="btnBatal">Batal</button>
                </div>
            </form>
        </div>

        <div id="confirmModal" class="modal">
            <form id="frmConfirm" class="form-horizontal" role="form" onsubmit="return false" autocomplete="off">
                <div class="modal-content">
                    <h4 class="modal-title" id="myConfirmModalLabel"></h4>
                    <div id="data-confirm"></div>
                    <input type="hidden" id="id_confirm" data-pin="" data-tanggal="">
                    <br />
                    <div id="resultConfirm"></div>
                </div>
                <div class="modal-footer right-align">
                    <button id="btnConfirm" type="submit" class="" data-confirm=""></button>
                    <button id="btnBatalConfirm" type="button" class="modal-close waves-effect waves-light btn grey">Batal</button>
                </div>
            </form>
        </div>

        <!-- /.modal -->
        <?php $this->getView('kepalabkppd', 'main', 'footer', ''); ?>
    </div>


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