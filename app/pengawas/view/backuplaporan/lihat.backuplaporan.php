<body class="search-app quick-results-off">
    <?php $this->getView('pengawas', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('pengawas', 'main', 'header', ''); ?>    
        <?php $this->getView('pengawas', 'main', 'menu', ''); ?>

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
                            <a class="waves-effect waves-light pink btn" href="<?= $this->link('pengawas/backuplaporan/') ?>"><i class="material-icons  arrow-l left">arrow_back</i>kembali</a>
                            <form id="frmData" class="navbar-search expanded" role="search" method="post" style="margin-top: 10px">
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?= comp\MATERIALIZE::inputText('satker', 'text', $satker, 'disabled style="color: rgba(0,0,0,.7)"'); ?>
                                        <label>Satuan Kerja</label>
                                    </div>
                                    <div style="clear: both"></div>
                                    <div class="input-field col s2">
                                        <select name="bulan" id="pilihbulan">
                                        <?php
                                        $namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                                        foreach ($namabulan as $key => $i) {
                                            $selected = '';
                                            if ($key+1 == $bulan) {
                                                $selected = 'selected';
                                            }
                                            echo '<option value="'.($key+1).'" '.$selected.'>'.$i.'</option>';   
                                        }
                                        ?>
                                        </select>
                                        <label>Pilih Bulan</label>
                                    </div>
                                    <div class="input-field col s2">
                                        <select name="tahun" id="pilihtahun">
                                            <?php
                                            for ($j = 2018; $j < 2021; $j++) { 
                                                $selected = '';
                                                if ($j == $tahun) {
                                                    $selected = 'selected';
                                                }
                                                echo '<option value="'.$j.'" '.$selected.'>'.$j.'</option>';
                                            }
                                            ?>                                           
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
                                    </div>
                                </div>
                                <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('kdlokasi', $kdlokasi); ?>
                                <?= comp\MATERIALIZE::inputKey('download', '1'); ?>
                                <?= comp\MATERIALIZE::inputKey('pin_absen', ''); ?>
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
        <?php $this->getView('pengawas', 'main', 'footer', ''); ?>
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
            app.loadTabel();

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
                } else {
                    /*if (jenis == 1)
                        var url = url+'/tabelmasuk';
                    else if (jenis == 2)
                        var url = url+'/tabelapel';
                    else
                        var url = url+'/tabelpulang';
                    */
                    var url = url+'/tabelpresensi';
                }

                $("#frmData").attr('action', url);
                return true;
            });

            $(document).on("click", ".btnDetail", function () {
                app.loadRekap(this.id);
            });

            $("#data-tabel").on("click", ".paging", function () {
                app.tabelPagging($(this).attr("number-page"));
            });

            $('#btnCetak').on('click', function() {
                $('#frmData').submit();
            });

            $('#format').on('change', function() {
                var format = $(this, 'option:selected').val();

                $('#kolomJenis').removeClass('hide');
                $('#kolomTingkat').removeClass('hide');
                $('#kolomStatus').removeClass('hide');
                $("#jenis").attr('disabled', false);
                $("#jenis").find('option[value="3"]').attr('disabled', false);
                $("#jenis").find('option[value="1"]').html('1 - MK');
                $("#jenis").find('option[value="2"]').html('2 - APEL');
                $("#jenis").find('option[value="3"]').html('3 - PK');
                $("#btnCetak").attr('disabled', false);

                if (format == 'C') {
                    $('#jenis').val(1);
                    $("#jenis").find('option[value="3"]').attr('disabled', true);
                    $("#jenis").find('option[value="1"]').html(1);
                    $("#jenis").find('option[value="2"]').html(2);
                    $("#jenis").find('option[value="3"]').html('-');
                    $("#btnCetak").attr('disabled', true);
                } else if (format == 'TPP') {
                    $('#kolomJenis').addClass('hide');
                    $('#kolomStatus').addClass('hide');
                    $("#jenis").attr('disabled', true);
                }
                $('select').material_select();
                $('#status').trigger('change');
            }).change();

            //tambah filter status
            $('#status').on('change', function() {
                var status = $('#status option:selected').val();
                var format = $('#format option:selected').val();

                $("#jenis").find('option[value="1"]').removeAttr('disabled');
                $('#kolomTingkat').removeClass('hide');

                if (status == 'NONPNS') {
                    $('#tingkat').find('option[value="1"]').prop('selected', true);
                    $('#kolomTingkat').addClass('hide');
                    if (format == 'C') {
                        $('#jenis').find('option[value="2"]').prop('selected', true);
                        $("#jenis").find('option[value="1"]').attr('disabled', true);
                    }
                }
                $('select').material_select();
            }).change();
        })(jQuery);
    </script>
</body>