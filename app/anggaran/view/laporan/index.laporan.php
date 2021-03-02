<body class="search-app quick-results-off">
    <?php $this->getView('anggaran', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('anggaran', 'main', 'header', ''); ?>    
        <?php $this->getView('anggaran', 'main', 'menu', ''); ?>

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
                                    <div class="input-field col s6">
                                        <?= comp\MATERIALIZE::inputSelect('kd_kelompok_lokasi_kerja', $pil_kel_satker, '') ?>
                                        <label>Kelompok Lokasi Kerja</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_satker, '') ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <select name="bulan" id="pilihbulan">
                                        <?php
                                            $namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                                            foreach ($namabulan as $key => $i) {
                                                $selected = ''; $bulan = date('m');

                                                if ($key+1 == $bulan)
                                                    $selected = 'selected';
                                                echo '<option value="'.($key+1).'" '.$selected.'>'.$i.'</option>';   
                                            }
                                        ?>
                                        </select>
                                        <label>Pilih Bulan</label>
                                    </div>
                                    <div class="input-field col s1">
                                        <select name="tahun" id="pilihtahun">
                                            <?php
                                            for ($i = 2018; $i < 2021; $i++) {
                                                $selected = ""; $tahun = date('Y');
                                                if ($i == $tahun)
                                                    $selected = "selected";

                                                echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                        <label>Pilih Tahun</label>
                                    </div>
                                    <div class="input-field col s3">
                                        <select name="format" id="format">
                                            <!--<option value="A">Laporan Format A</option>-->
                                            <!--<option value="B">Laporan Format B</option>-->
                                            <!--<option value="C">Laporan Format C</option>-->
                                            <option value="TPP">Laporan TPP</option>
                                            <option value="REKAPTPP">Rekap TPP</option>
                                        </select>
                                        <label>Pilih Format Laporan</label>
                                    </div>
<!--                                    <div class="input-field col s2" id="kolomJenis">
                                        <div>
                                            <select name="jenis" id="jenis">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </select>
                                        </div>
                                    </div>-->
                                    <div class="input-field col s2">
                                        <div id="kolomTingkat">
                                            <select name="tingkat" id="tingkat">
                                                <!--<option value="1">Tingkat 1</option>-->
                                                <!--<option value="2">Tingkat 2</option>-->
                                                <!--<option value="3">Tingkat 3</option>-->
                                                <!--<option value="4">Tingkat 4</option>-->
                                                <!--<option value="5">Tingkat 5</option>-->
                                                <option value="6">Final</option>
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
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('download', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('pin_absen', ''); ?>
                                <?= comp\MATERIALIZE::inputKey('satker', ''); ?>
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
        <?php $this->getView('anggaran', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $(document).on("click", ".btnTampil", function () {
                $("#page").val(1);
                var kdlokasi = $('#kdlokasi option:selected').val();
                if (!kdlokasi)
                    return false;
                app.loadTabel();
            });

            $(document).on("submit", "#frmData", function () {
                var kdlokasi = $('#kdlokasi option:selected').val();
                if (!kdlokasi)
                    return false;
                
                var url = "<?= $this->link($this->getProject() . $this->getController()); ?>";
                var format = $('#format option:selected').val();

                if (format === 'REKAPTPP') {
                    var url = url+'/rekaptpp';
                } else if (format === 'TPP') {
                    var url = url+'/tabeltpp';
                }

                $("#frmData").attr('action', url);
                return true;
            });

            $(document).on("change", "#kd_kelompok_lokasi_kerja", function () {
                var dt = $("#kd_kelompok_lokasi_kerja").val();
                app.showPilSatker(dt);
            });

            $(document).on("click", ".btnDetail", function () {
                app.loadRekap(this.id);
            });

            $('#btnCetak').on('click', function() {
                $('#asli').val('');
                $('#frmData').submit();
            });

            $('#btnCetakAsli').on('click', function() {
                $('#asli').val(1);
                $('#frmData').submit();
            });

            $("#data-tabel").on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

            $('#format, #pilihbulan, #pilihtahun').on('change', checkbulantahun).change();

            function checkbulantahun() {
                var bulan = $('#pilihbulan option:selected').val();
                var tahun = $('#pilihtahun option:selected').val();
                var format = $('#format option:selected').val();

                if ((format == 'TPP' || format == 'REKAPTPP') && ((bulan == 1 && tahun == 2018) || (bulan == 2 && tahun == 2018))) {
                    $('#btnCetakAsli').removeClass('hide');
                } else
                    $('#btnCetakAsli').addClass('hide');
            }

            $('select').on('change', function() {
                $('.check-cetak').prop('checked', false);
                $('.check-cetak').trigger('change');

                var format = $('#format option:selected').val();
                if (format == 'C') {
                    $('#btnCetak').attr('disabled', true);
                    app.loadTabel();
                } else {
                    $('#btnCetak').attr('disabled', false);
                }
            }).change();
        })(jQuery);
    </script>
</body>