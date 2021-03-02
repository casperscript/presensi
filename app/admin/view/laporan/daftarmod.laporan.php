<?php
use comp\FUNC;
$nDaftarVerMod = count($daftarVerMod);
?>
<style>
    .content-mod {
        font-size: 10px;
    }
    td, th {
        padding: 5px;
    }
    .massive {
        margin-bottom: 5px;
    }
</style>
<h5 class="center-align"><b>
    DAFTAR PENGAJUAN MODERASI<br>
</b></h5>
<div class="row content-mod">
    <div class="col s12 right-align massive">
        <?php if ($nDaftarVerMod > 1): ?>
            <a id="btnTerimaSemua" class="btn btn-mass-verif green waves-effect waves-light disabled" flag="1">Terima Semua</a>
        <?php endif; ?>
        <?php if ($nDaftarVerMod > 1): ?>
            <a id="btnTolakSemua" class="btn btn-mass-verif red waves-effect waves-light disabled" flag="0">Tolak Semua</a>
        <?php endif; ?>
    </div>
    <div class="col s12">
        <div id="divTabelVerMod">
            <table class="bordered striped hoverable">
                <thead>
                    <tr>
                        <th class="grey darken-3 white-text center-align">NO</th>
                        <th class="grey darken-3 white-text">NAMA</th>
                        <th class="grey darken-3 white-text center-align">MODERASI</th>
                        <th class="grey darken-3 white-text center-align">KODE</th>
                        <th class="grey darken-3 white-text center-align">TGL MODERASI</th>
                        <th class="grey darken-3 white-text center-align">VER OP.OPD</th>
                        <th class="grey darken-3 white-text center-align">VER KEP.OPD</th>
                        <th class="grey darken-3 white-text center-align">VER OP.BKPPD</th>
                        <th class="grey darken-3 white-text center-align">VER KEP.BKPPD</th>

                        <th class="grey darken-3 white-text center-align">AKSI</th>
                        <th class="grey darken-3 white-text center-align">check all<br><input type="checkbox" class="filled-in" id="chkCheckAllMod" <?= $nDaftarVerMod < 1 ? 'disabled="disabled"' : "" ?> />
                            <label for="chkCheckAllMod" style="padding:10px"></label></th>
                        <th class="grey darken-3 white-text center-align"><i class="material-icons">vpn_key</i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $lastPin = '';
                        $no = 1;
                    ?>
                    
                    <?php foreach ($daftarVerMod as $index => $val): ?>
                        <?php
                        $keylock = "lock_open";
                        $chkDisabled = '';
                        $nRowSpan = 0;

                        foreach ($daftarVerMod as $v2) {
                            $nRowSpan += count(array_keys($v2, $val["pin_absen"]));
                        }
                        
                        if (!is_null($val["flag_kepala_opd"]) || $val["usergroup"] !== "KDGRUP05") {
                            $keylock = "lock";
                            $chkDisabled = 'disabled="disabled"';
                        }
                        
                        ?>
                        <tr>
                            <?php if ($lastPin !== $val["pin_absen"]): ?>                    
                                <td class="center" rowspan="<?= $nRowSpan ?>"><?= $no++ ?></td>
                                <td rowspan="<?= $nRowSpan ?>"><?= $val["nama_lengkap"] ?></td>
                                <?php $lastPin = $val["pin_absen"]; ?>
                            <?php endif; ?>
                            <td class="center"><?= $val["nama_jenis"] ?></td>
                            <td class="center"><span class="chip brown white-text" title="<?= $val["ket_kode_presensi"] ?>" style="cursor: help;"><?= $val["kode_presensi"] ?></span></td>
                            <td class="center">&nbsp; &nbsp;<?= FUNC::toHusnanWSniDate($val["tanggal_awal"]) ?> -<br><?= FUNC::toHusnanWSniDate($val["tanggal_akhir"]) ?></td>
                            <td id="tdOpOpd-<?= $val["id"] ?>" class="center" title="CATATAN: <?= $val["catatan_operator_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_opd"]) ?></td>
                            <td id="tdKepOpd-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_opd"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_opd"]) ?></td>
                            <td id="tdOpKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_operator_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_operator_kota"]) ?></td>
                            <td id="tdKepKota-<?= $val["id"] ?>"  class="center" title="CATATAN: <?= $val["catatan_kepala_kota"] ?>" style="cursor: help;"><?= FUNC::husnanWVerModStyle($val["flag_kepala_kota"]) ?></td>
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
                                <input type="checkbox" class="filled-in check-all-mod" id="chkMod<?= $val["id"] ?>" value="<?= $val["id"] ?>" <?= $chkDisabled ?>  />
                                <label for="chkMod<?= $val["id"] ?>" style="margin:10px 0 0 0; padding: 10px;"></label>
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