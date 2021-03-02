<?php
$nDaftarVerMod = count($daftarVerMod);
//comp\FUNC::showPre($data);
?>

<div class="search-page-results">
    <div id="web" class="col s12 m12 l12">
        <?php
        if ($daftarPegawai['count'] > 0) {
            foreach ($daftarPegawai['value'] as $peg) {
                ?>
                <div class="card">
                    <div class="card-content">
                        <span class="card-title"><?= $peg['nama_personil'] ?></span>

                        <?php
                        foreach ($daftarVerMod['moderasi'][$peg['pin_absen']] as $mod) {
                            extract($mod);

                            if ($mod['tanggal_awal'] == $mod['tanggal_akhir']) {
                                $tglMod = comp\FUNC::tanggal($mod['tanggal_awal'], 'long_date');
                            } else {
                                $tglMod = comp\FUNC::tanggal($mod['tanggal_awal'], 'long_date') . ' - ' . comp\FUNC::tanggal($mod['tanggal_akhir'], 'long_date');
                            }
                            $lockStatus = is_null($flag_kepala_opd) ? false : true;
                            ?>

                            <div class="divider"></div>
                            <div id="<?= $mod['id'] ?>" class="search-result listModerasiMob" style="cursor: pointer;">
                                <a href="javascript:void(0)" class="search-result-title">
                                    <?= $mod['kode_presensi'] . ' - ' . $pilJenMod[$mod['kd_jenis']] ?>
                                    <?= comp\FUNC::modStatus($mod['flag_operator_opd'], $lockStatus) ?><?= comp\FUNC::modStatus($mod['flag_operator_opd']) ?>
                                </a>
                                <a href="javascript:void(0)" class="search-result-link">
                                    <i class="material-icons tiny">date_range</i> <?= $tglMod ?>
                                </a>
                                <p class="search-result-description">
                                    <i class="tiny material-icons">textsms</i>
                                    <?= empty($mod['keterangan']) ? '-' : $mod['keterangan'] ?>
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <?php
            }
        } else {
            ?>
            <div class="card-panel orange darken-4 white-text">
                <div class="valign-wrapper">
                    <div class="s3"><i class="material-icons medium circle">android</i></div>
                    <div class="s9">
                        <span>Tidak ada data yang dapat ditampilkan</span>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>