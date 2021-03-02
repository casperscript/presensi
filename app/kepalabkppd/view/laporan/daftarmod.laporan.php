<?php
use comp\FUNC;
?>
<h5 class="center-align"><b>
    DAFTAR PENGAJUAN MODERASI<br>
</b></h5>
<div class="row">
    <div class="col s12">
        <table class="bordered striped hoverable">
            <thead>
                <tr>
                    <th class="grey darken-3 white-text center-align">NO</th>
                    <th class="grey darken-3 white-text">NAMA</th>
                    <th class="grey darken-3 white-text center-align">OPD</th>
                    <th class="grey darken-3 white-text center-align">TGL PENGAJUAN</th>
                    <th class="grey darken-3 white-text center-align">VER OP.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.OPD</th>
                    <th class="grey darken-3 white-text center-align">VER OP.BKPPD</th>
                    <th class="grey darken-3 white-text center-align">VER KEP.BKPPD</th>

                    <th class="grey darken-3 white-text center-align">AKSI</th>
                    <th class="grey darken-3 white-text center-align"><i class="material-icons">vpn_key</i></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftarVerMod as $index => $val): ?>
                    <?php
                    $keylock = "lock_open";
                    ?>
                    <tr>
                        <td class="center"><?= $index + 1 ?></td>
                        <td><?= $val["nama_lengkap"] ?></td>
                        <td class="center"><?= $val["singkatan_lokasi"] ?></td>
                        <td class="center"><?= FUNC::toHusnanWSniDateTime($val["dt_created"]) ?></td>
                        <td id="tdOpOpd-<?= $val["id"] ?>" class="center"><?= FUNC::husnanWVerModStyle($val["flag_operator_opd"]) ?></td>
                        <td id="tdKepOpd-<?= $val["id"] ?>"  class="center"><?= FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?></td>
                        <td id="tdOpKota-<?= $val["id"] ?>"  class="center"><?= FUNC::husnanWVerModStyle($val["flag_operator_kota"]) ?></td>
                        <td id="tdKepKota-<?= $val["id"] ?>"  class="center"><?= FUNC::husnanWVerModStyle($val["flag_kepala_kota"]) ?></td>
                        <td class="center">
                            <a class="btn-info-mod btn-floating waves-effect waves-light blue" title="info" mid="<?= $val["id"] ?>"><i class="material-icons">info_outline</i></a>
                            <?php if(is_null($val["flag_kepala_opd"]) && $val["usergroup"] === "KDGRUP05"): ?>
                                <a class="btn-tolak-mod btn-floating waves-effect waves-light red" title="saya tolak" mid="<?= $val["id"] ?>"><i class="material-icons">close</i></a>
                                <a class="btn-terima-mod btn-floating waves-effect waves-light green" title="saya terima" mid="<?= $val["id"] ?>"><i class="material-icons">check</i></a>
                            <?php else: ?>
                                <?php $keylock = "lock"; ?>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <i class="material-icons"><?= $keylock ?></i>
                        </td>
                    </tr>        
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="center-align">
    <form id="frmRekap">
        <?= comp\MATERIALIZE::inputKey('bulan', $bulan); ?>
        <?= comp\MATERIALIZE::inputKey('tahun', $tahun); ?>
        <?= comp\MATERIALIZE::inputKey('jns', $jenis); ?>
        <?= comp\MATERIALIZE::inputKey('tk', $tingkat); ?>
        <button class="waves-effect waves-light btn blue <?= (isset($format) ? 'btnTampil' : 'btnDetail') ?>" type="button" id="<?= $pin_absen ?>">
            <i class="material-icons left">loop</i>
            Kembali ke Laporan
        </button>
    </form>
</div>

<form id="frmDokumenPendukung" class="dropzone" method="post" action="<?= $this->link($this->getProject(). 'upload/mod') ?>" style="display:none;"></form> 
<script src="<?= $this->link('js/dropzone.js'); ?>"></script>
<script src="<?= $this->link('js/husnanw_moderasi_admin_opd.js'); ?>"></script>
<script>
    (function ($) {
    husnanw_moderasi_main_admin_opd("<?= $this->link("adminopd/HusnanWModerasi"); ?>", "<?= $data['dateLimit'] ?>");                
    })(jQuery);
</script>
<?php //echo MATERIALIZE::Pagging($page, $batas, $jmlData) ?>