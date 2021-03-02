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
                            <form id="frmData" class="navbar-search expanded" role="search" method="post" action="<?= $this->link('adminopd/laporandes18/tabeltpp') ?>">
                                <div class="row">
                                    <div class="input-field col s4">
                                        <?= comp\MATERIALIZE::inputText('satker', 'text', $satker, 'disabled style="color: rgba(0,0,0,.7)"'); ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <select name="bulan" id="pilihbulan">
                                            <option value="12">Desember</option>
                                        </select>
                                        <label>Pilih Bulan</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <select name="tahun" id="pilihtahun">
                                            <option value="2018">2018</option>
                                        </select>
                                        <label>Pilih Tahun</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <div id="kolomTingkat">
                                            <select name="tingkat" id="tingkat">
                                                <option value="1">Tingkat 1</option>
                                                <option value="2">Tingkat 2</option>
                                                <option value="3">Tingkat 3</option>
                                            </select>
                                            <label>Pilih Tingkat Laporan</label>
                                        </div>
                                    </div>
                                    <div class="input-field col s2">
                                        <button class="btn-floating btn waves-effect waves-light green btnTampil" title="Tampilkan" type="button">
                                            <i class="material-icons left">search</i>
                                        </button>
                                        <button class="btn-floating btn waves-effect waves-light indigo" title="Cetak" type="button" id="btnCetak">
                                            <i class="material-icons left">print</i>
                                        </button>
                                        <button class="btn-floating btn waves-effect waves-light red" title="Cetak Asli" type="button" id="btnCetakAsli">
                                            <i class="material-icons left">print</i>
                                        </button>
                                    </div>
                                </div>
                                <select name="format" id="format" class="browser-default hide">
                                    <option value="TPP" selected>Laporan TPP</option>
                                </select>
                                <?= comp\MATERIALIZE::inputKey('download', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('asli', ''); ?>
                                <?= comp\MATERIALIZE::inputKey('bendahara', (is_array($bendahara) ? $bendahara['id_bendahara'] : '')); ?>
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
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $(".btnTampil").on("click", function () {
                $("#page").val(1);
                app.loadTabel();
            }).click();

            $('#btnCetak').on('click', function() {
                $('#asli').val('');
                checkBendahara();
            });

            $('#btnCetakAsli').on('click', function() {
                $('#asli').val(1);
                checkBendahara();
            });

            function checkBendahara() {
                var format = $('#format option:selected').val();
                var bendahara = $('#bendahara').val();
                var verified = $('#verified').val();

                if (format == 'TPP' && !bendahara) {
                    alert('Mohon untuk memilih pegawai bendahara pengeluaran terlebih dahulu.');
                    app.loadTabel();

                //cek verifikasi laporan
                } /*else if (verified == 0) {
                    alert('Penerimaan TPP tidak dapat dicetak sebelum laporan presensi diverifikasi oleh Kepala OPD.');
                }*/ else
                    $('#frmData').submit();
            }

            $("#data-tabel").on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

            $('#pilihbulan, #pilihtahun').on('change', checkbulantahun).change();

            function checkbulantahun() {
                var bulan = $('#pilihbulan option:selected').val();
                var tahun = $('#pilihtahun option:selected').val();

                //uji coba bulan januari&februari 2018
                if ((bulan == 1 && tahun == 2018) || (bulan == 2 && tahun == 2018)) {
                    $('#btnCetakAsli').removeClass('hide');
                    $('#kolomTingkat').addClass('hide');
                } else {
                    $('#btnCetakAsli').addClass('hide');
                    $('#kolomTingkat').removeClass('hide');
                }
            }
        })(jQuery);
    </script>
</body>
