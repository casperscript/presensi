<body class="search-app quick-results-off">
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('admin', 'main', 'header', ''); ?>    
        <?php $this->getView('admin', 'main', 'menu', ''); ?>

        <main class="mn-inner">
            <div class="search-header">
                <div class="card card-transparent no-m">
                    <div class="card-content no-s">
                        <div class="z-dept-1 search-tabs">
                            <div class="search-tabs-container">
                                <div class="col s12 m12 l12">

                                    <div class="row search-tabs-row search-tabs-header blue-grey white-text">
                                        <div class="show-on-small hide-on-med-and-up center-align">
                                            <span style="line-height: 48px; text-transform: uppercase">Moderasi Dalam Proses</span>
                                        </div>
                                        <div class="hide-on-small-only center-align">
                                            <span style="line-height: 50px;">Daftar Proses Status Verifikasi PNS yang Mengajukan Moderasi Ketidakhadiran</span>
                                        </div>
                                    </div>

                                    <form id="frmData" onsubmit="return false" autocomplete="off">
                                        <div class="row search-tabs-row search-tabs-header">
                                            <div class="input-field col s12 center-align light-blue accent-1">
                                                <h4 class="black-text" style="font-weight: bold"><?= $satker ?></h4>
                                                <input type="hidden" name="kdlokasi" id="kdlokasi" value="<?= $kodeLokasi ?>">
                                            </div>
                                            <div class="input-field col l2 m2 s5">
                                                <?= comp\BOOTSTRAP::inputSelect('bulan', comp\FUNC::$namabulan1, date('m', strtotime('-1 month')), '') ?>
                                                <label>Pilih Bulan</label>
                                            </div>
                                            <div class="input-field col l1 m2 s3">
                                                <?= comp\BOOTSTRAP::inputSelect('tahun', $listTahun, date('Y', strtotime('-1 month')), '') ?>
                                                <label>Pilih Tahun</label>
                                            </div>
                                            <div class="input-field col l2 m2 s4">
                                                <?= comp\MATERIALIZE::inputSelect('status', array('semua' => 'Semua', 'sah' => 'Disahkan', 'null' => 'Belum Disahkan'), 'semua') ?>
                                            </div>
                                            <div class="input-field col l3 m4 s12 no-s">
                                                <div class="row">
                                                    <div class="input-field col s9 m10">
                                                        <?= comp\MATERIALIZE::inputText('cari', 'text', '') ?>
                                                        <label for="cari">Pencarian nip / nama</label>
                                                    </div>
                                                    <div class="input-field col s3 m2">
                                                        <button class="btn btn-floating waves-effect green">
                                                            <i class="material-icons">search</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col col l4 m4 s12 right-align" style="padding: 10px 0 10px 50px">
                                                <a id="btnMassLegit" class="btn red waves-effect waves-light disabled" title="Aktif jika lebih dari satu moderasi dipilih">PENGESAHAN MASSAL</a>
                                            </div>   
                                        </div>
                                    </form>

                                    <div class="row">
                                        <div class="progress no-s">
                                            <div id="progressView" class="determinate"></div>
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
                    <div id="divTabelVerMod"></div>
                </div>
            </div>
        </main>
    </div>
</body>

<link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet">
<script src="assets/plugins/sweetalert/sweetalert.min.js"></script>
<script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php') ?>"></script>
<script>
    app.init("<?= $this->link($this->getProject() . $this->getController()) ?>");
    app.loadTabel();
    $(document).on("submit", "#frmData", function () {
        app.loadTabel();
    });
</script>