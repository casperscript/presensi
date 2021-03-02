<!-- Show Detail Record Personal -->
<form id="frmDataTabel" class="navbar-search expanded fileDownloadForm" method="get" action="<?= $this->link($this->getController() . '/pdfKehadiran') ?>">
    <div class="card-action row" style="padding-bottom: 0px">
        <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
        <?= comp\MATERIALIZE::inputKey('batas', '10'); ?>
        <?= comp\MATERIALIZE::inputKey('kdlokasi', $kdlokasi); ?>

        <div class="input-field col s12">
            <div class="col s12">
                <div class="input-field col s1">
                    <span>Periode</span>
                </div>
                <div class="input-field col">
                    <i class="fa fa-calendar prefix"></i>
                    <?= comp\MATERIALIZE::inputText('sdate', 'text', $sdate, 'class="datepicker picker_input active"') ?>
                    <label for="sdate" class="active">Tanggal Awal</label>
                </div>
                <div class="input-field col">
                    <i class="fa fa-calendar prefix"></i>
                    <?= comp\MATERIALIZE::inputText('edate', 'text', $edate, 'class="datepicker picker_input"') ?>
                    <label for="edate" class="active">Tanggal Akhir</label>
                </div>
                <div class="input-field col">
                    <label for="sdate" class="active">Status</label>
                    <?= comp\MATERIALIZE::inputSelect('stat', $pil_status, '', 'class="datepicker picker_input active"') ?>
                </div>
            </div>
            <div class="col s12">
                <div class="input-field col s1">
                    <a class="btn-floating waves-effect waves-light red btnBack">
                        <i class="material-icons left" title="Kembali">reply</i>
                    </a>
                </div>
                <div class="input-field col s11">
                    <a class="btn btnX waves-effect waves-light green darken-2 btnKehadiran">
                        <i class="material-icons left">library_books</i> Kehadiran
                    </a>
                    <a class="btn btnX waves-effect waves-light teal darken-2 btnDisiplin">
                        <i class="material-icons left">library_books</i> Disiplin
                    </a>
                    <a class="btn btnX waves-effect waves-light lime darken-2 btnRekap">
                        <i class="material-icons left">library_books</i> Rekap
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div id="data-tabel" class="card-content dataTables_wrapper"></div>
</form>
<!-- End Detail -->

<script>
    app.loadTabel();
    $("#frmDataTabel").on("click", ".btnBack", function () {
        app.showIndex();
    });
    $("#frmDataTabel").on("click", ".btnKehadiran", function () {
        var frm = $("#frmData");
        app.printKehadiran(frm);
    });
    $("#stat").material_select("update");
    $("#frmData").on("click", ".paging", function () {
        app.tabelPagging($(this).attr("number-page"));
    });
    $(document).on("submit", ".fileDownloadForm", function (e) {
        $.fileDownload($(this).prop('action'), {
            preparingMessageHtml: "We are preparing your report, please wait...",
            failMessageHtml: "There was a problem generating your report, please try again.",
            httpMethod: "POST",
            data: $(this).serialize()
        });
        e.preventDefault(); //otherwise a normal form submit would occur
    });
</script>