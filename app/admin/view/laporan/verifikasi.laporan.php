<form id="frmData" class="navbar-search expanded" role="search" method="post">
    <div class="row">
        <div class="col s12" style="padding-bottom: 10px">
            <a class="waves-effect waves-light pink btn" href="<?= $this->link('admin/laporan/') ?>"><i class="material-icons  arrow-l left">arrow_back</i>kembali ke daftar OPD</a>
        </div>
        <div class="input-field col s12">
            <?= comp\MATERIALIZE::inputText('satker', 'text', $satker, 'disabled style="color: rgba(0,0,0,.7)"'); ?>
            <label class="active">Satuan Kerja</label>
        </div>
    </div>
    <?= comp\MATERIALIZE::inputKey('kdlokasi', $kdlokasi); ?>
    <?= comp\MATERIALIZE::inputKey('tingkat', '4'); ?>
    <?= comp\MATERIALIZE::inputKey('bulan', $bulan); ?>
    <?= comp\MATERIALIZE::inputKey('tahun', $tahun); ?>
    <?= comp\MATERIALIZE::inputKey('download', '1'); ?>
	<?= comp\MATERIALIZE::inputKey('asli', ''); ?>
</form>
<div id="data-tabel"></div>

<script type="text/javascript">
    $(function() {
        app.loadTabelverifikasi();
    });
</script>