<body class="search-app quick-results-off">
    <?php $this->getView('adminopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>

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
                            <form id="frmData" class="navbar-search expanded" role="search" method="post" action="<?= $this->link('adminopd/laporan/verifikasi') ?>">
                                <div class="row">
                                    <div class="input-field col s6">
                                        <?= comp\MATERIALIZE::inputText('satker', 'text', $satker, 'disabled style="color: rgba(0,0,0,.7)"'); ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s3">
                                        <select name="bulan" id="pilihbulan">
                                            <?php
                                            foreach (comp\FUNC::$namabulan as $key => $i) {
                                                $selected = '';
                                                $bulan = date('m');
                                                if ($bulan == 1)
                                                    $bulan = 13;

                                                if ($key + 2 == $bulan)
                                                    $selected = 'selected';
                                                echo '<option value="' . ($key + 1) . '" ' . $selected . '>' . $i . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <label>Pilih Bulan</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <?php
                                        $selected = (date('m') == 1) ? date('Y') - 1 : date('Y');
                                        echo comp\BOOTSTRAP::inputSelect('tahun', $listTahun, $selected, 'class="pilihtahun"');
                                        ?>
                                        <label>Pilih Tahun</label>
                                    </div>
                                    <div class="input-field col s1">
                                        <button class="btn-floating btn waves-effect waves-light green btnTampil" title="Tampilkan" type="button">
                                            <i class="material-icons left">search</i>
                                        </button>
                                        <!--button class="btn-floating btn waves-effect waves-light indigo" title="Cetak" type="button" id="btnCetak">
                                            <i class="material-icons left">print</i>
                                        </button>
                                        <button class="btn-floating btn waves-effect waves-light red" title="Cetak Asli" type="button" id="btnCetakAsli">
                                            <i class="material-icons left">print</i>
                                        </button-->
                                    </div>
                                </div>
                                <?= comp\MATERIALIZE::inputKey('tingkat', '2'); ?>
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('download', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('asli', ''); ?>
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
        <?php $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>

    <!-- ./wrapper -->
    <link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <style>.sweet-alert{width: 50%; margin-left: 0; left: 25%;}</style>
    <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $(".btnTampil").on("click", function () {
                $("#page").val(1);
                app.loadVerifikasi();
            }).click();
        })(jQuery);
    </script>
</body>
