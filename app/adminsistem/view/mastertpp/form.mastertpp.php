<?php extract($dataTabel);?>
<h4><?= $form_title ?></h4>
<div class="row">
    <?= comp\MATERIALIZE::inputKey('kd_tpp', $kd_tpp); ?>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputSelect('jenis_tpp', $pil_jenis_tpp, $jenis_tpp, ' required'); ?>
        <label for="" class="">Jenis TPP</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputText('label', 'text', $label, ' required'); ?>
        <label for="" class="">Label Menu</label>
    </div>
    <div class="input-field col s4">
        <?= comp\MATERIALIZE::inputSelect('tahun', $pil_tahun, $tahun, ' required'); ?>
        <label for="" class="">Tahun</label>
    </div>
    <div class="input-field col s4">
        <?= comp\MATERIALIZE::inputSelect('bulan', $pil_bulan, $bulan, ' required'); ?>
        <label for="" class="">Bulan</label>
    </div>
    <div class="input-field col s4">
        <?= comp\MATERIALIZE::inputSelect('tingkat', $pil_tingkat, $tingkat, ' required'); ?>
        <label for="" class="">Tingkat Laporan Presensi</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputSelect('periode', $pil_periode, $periode, ' required'); ?>
        <label for="" class="">Periode</label>
    </div>
    <div class="input-field col s3">
        <?= comp\MATERIALIZE::inputText('tgl_awal', 'text', $tgl_awal, ' required readonly'); ?>
        <label for="" class="">Tanggal Awal</label>
    </div>
    <div class="input-field col s3">
        <?= comp\MATERIALIZE::inputText('tgl_akhir', 'text', $tgl_akhir, ' required readonly'); ?>
        <label for="" class="">Tanggal AKhir</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputSelect('potongan', [0 => 'TIDAK', 1 => 'YA'], $potongan, ' required'); ?>
        <label for="" class="">Potongan</label>
    </div>
    <div class="input-field col s6">
        <?= comp\MATERIALIZE::inputSelect('tampil', [0 => 'TIDAK', 1 => 'YA'], $tampil, ' required'); ?>
        <label for="" class="">Tampilkan Menu</label>
    </div>
    <div class="input-field col s12">
        <div id="resultForm"></div>
    </div>
</div>

<script>
    $(function() {
        $('#periode').on('change', function() {
            $('#tgl_awal').attr('readonly', true);
            $('#tgl_awal').attr('style', 'color :#0095a8;');
            $('#tgl_akhir').attr('readonly', true);
            $('#tgl_akhir').attr('style', 'color :#0095a8;');

            var tgl_awal = 1;
            var tgl_akhir = $('#tgl_akhir').val();
            var periode = $(this).val();
            if (periode == 'full') {
                var bulan = parseInt($('#bulan').val(), 10);
                var tahun = parseInt($('#tahun').val(), 10);
                var last = new Date(tahun, bulan, 0);
                var tgl_akhir = last.getDate();
            } else if (periode == 'half') 
                var tgl_akhir = 15;
            else {
                var tgl_awal = $('#tgl_awal').val();
                $('#tgl_awal').removeAttr('readonly');
                $('#tgl_awal').removeAttr('style');
                $('#tgl_akhir').removeAttr('readonly');
                $('#tgl_akhir').removeAttr('style');
            }

            $('#tgl_awal').val(tgl_awal);
            $('#tgl_akhir').val(tgl_akhir);

            Materialize.updateTextFields();
        }).change();

        $('#bulan').on('change', function() {
            $('#periode').trigger('change');
        });

        $('#jenis_tpp').on('change', function() {
            if ($('#kd_tpp').val() != '')
                return true;

            var label = '';
            var jenis = $('#jenis_tpp').val();
            if (jenis == 'TPP13')
                label = 'Penerimaan TPP Ke-13';
            else if (jenis == 'TPP14')
                label = 'Penerimaan TPP Ke-14';
            else if (jenis == 'TPPDES')
                label = 'Penerimaan TPP Desember ' + $('#tahun').val();

            $('#label').val(label);
            Materialize.updateTextFields();
        }).change();
    });
</script>