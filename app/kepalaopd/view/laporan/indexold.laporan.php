<body class="search-app quick-results-off">
    <?php $this->getView('kepalaopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('kepalaopd', 'main', 'header', ''); ?>    
        <?php $this->getView('kepalaopd', 'main', 'menu', ''); ?>

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
                                    <div class="input-field col s12">
                                        <?= comp\MATERIALIZE::inputText('satker', 'text', $satker, 'disabled style="color: rgba(0,0,0,.7)"'); ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <select name="bulan" id="pilihbulan">
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
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
                                    <div class="input-field col s3">
                                        <select name="format" id="format">
                                            <option value="A">Laporan Format A</option>
                                            <option value="B">Laporan Format B</option>
                                            <option value="C">Laporan Format C</option>
                                            <option value="TPP">Laporan TPP</option>
                                        </select>
                                        <label>Pilih Format Laporan</label>
                                    </div>
                                    <div class="input-field col s1"">
                                        <div id="kolomJenis">
                                            <select name="jenis" id="jenis">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="input-field col s2">
                                        <div id="kolomTingkat">
                                            <select name="tingkat" id="tingkat">
                                                <option value="1">Tingkat 1</option>
                                                <option value="2">Tingkat 2</option>
                                                <option value="3">Tingkat 3</option>
                                                <option value="4">Tingkat 4</option>
                                                <option value="5">Tingkat 5</option>
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
        <?php $this->getView('kepalaopd', 'main', 'footer', ''); ?>
    </div>

    <div id="divModalInfoModerasi" class="modal">
        <div class="modal-content">
            <h4>Informasi Detil Pengajuan Moderasi</h4>
            <div id="divModalBody"></div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn"><span style="position: relative; top: -2px;">Terima Kasih</span> <i class="material-icons">thumb_up</i></a>
        </div>
    </div>

    <div id="divModalMassVerif" class="modal">
        <div class="modal-content">
            <div id="divModalBodyMassVerif"></div>
        </div>
        <div class="modal-footer">
            <a href="#!" id="btnTerapkanMassVerif" class="modal-action waves-effect waves-light btn red" style="margin: 0 5px;"><i class="material-icons">check</i><span style="position: relative; top: -2px;"> Terapkan!</span></a>
            <a href="#!" class="modal-action modal-close waves-effect waves-light btn grey" style="margin: 0 5px;"><i class="material-icons">clear</i><span style="position: relative; top: -2px;"> Batal</span></a>
        </div>
    </div>
    <!-- ./wrapper -->
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");

            $(document).on("click", ".btnTampil", function () {
                $("#page").val(1);
                app.loadTabel();
            });

            $(document).on("submit", "#frmData", function () {
                var url = "<?= $this->link($this->getProject() . $this->getController()); ?>";
                var format = $('#format option:selected').val();
                var jenis = $('#jenis option:selected').val();

                if (format == 'C') {
                    var url = url+'/tabelrekapc'+jenis;
                    var pin_absen = [];
                    $('.check-cetak').each(function() {
                        if ($(this).prop('checked')) {
                            pin_absen.push($(this).val());
                        }
                    });
                    $('#pin_absen').val(pin_absen);
                } else if (format == 'TPP')
                    var url = url+'/tabeltpp';
                else {
                    if (jenis == 1)
                        var url = url+'/tabelmasuk';
                    else if (jenis == 2)
                        var url = url+'/tabelapel';
                    else
                        var url = url+'/tabelpulang';
                }

                $("#frmData").attr('action', url);
                return true;
            });

            $(document).on("click", ".btnDetail", function () {
                app.loadRekap(this.id);
            });

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

                if (format == 'TPP' && !bendahara) {
                    alert('Mohon untuk memilih pegawai bendahara pengeluaran terlebih dahulu.');
                    app.loadTabel();
                } else
                    $('#frmData').submit();
            }

            $("#data-tabel").on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

            $('#format').on('change', function() {
                var format = $(this, 'option:selected').val();

                $('#kolomJenis').removeClass('hide');
                $('#kolomTingkat').removeClass('hide');
                $('#kolomStatus').removeClass('hide');
                $("#jenis").attr('disabled', false);
                $("#jenis").find('option[value="3"]').attr('disabled', false);
                $("#btnCetak").attr('disabled', false);

                if (format == 'C') {
                    $('#jenis').val(1);
                    $("#jenis").find('option[value="3"]').attr('disabled', true);
                    $("#btnCetak").attr('disabled', true);
                } else if (format == 'TPP') {
                    $('#kolomJenis').addClass('hide');
                    $('#kolomTingkat').addClass('hide');
                    $('#kolomStatus').addClass('hide');
                    $("#jenis").attr('disabled', true);
                }
                $('select').material_select();

            }).change();

            $('#format, #pilihbulan, #pilihtahun').on('change', checkbulantahun).change();

            function checkbulantahun() {
                var bulan = $('#pilihbulan option:selected').val();
                var tahun = $('#pilihtahun option:selected').val();
                var format = $('#format option:selected').val();

                if (format == 'TPP' && bulan == 1 && tahun == 2018) {
                    $('#btnCetakAsli').removeClass('hide');
                } else
                    $('#btnCetakAsli').addClass('hide');
            }
        })(jQuery);
    </script>
</body>