<?php
//comp\FUNC::showPre($data);
?>
<div class="card">
    <div class="card-content no-s">
        <table class="bordered highlight">
            <thead class="hide-on-small-only">
                <tr class="grey darken-3 white-text">
                    <th rowspan="2" class="center-align" width="90px">Moderasi</th>
                    <th rowspan="2" class="center-align" width="80px">Kode</th>
                    <th rowspan="2" class="center-align" width="150px">Tanggal</th>
                    <th rowspan="2" class="center-align">Keterangan</th>
                    <th colspan="4" class="center-align" width="34%">Verifikasi</th>
                    <th rowspan="2" class="center-align">
                        <i class="material-icons">vpn_key</i>
                    </th>
                </tr>

                <tr class="grey darken-3 white-text">
                    <th class="center-align" width="8%">Admin OPD</th>
                    <th class="center-align" width="8%">Kepala OPD</th>
                    <th class="center-align" width="8%">Admin Kota</th>
                    <th class="center-align" width="10%">Kepala BKPPD</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $numbBg = 0;

                if ($dataTabel['count'] > 0) {
                    /* Data lebih dari 1 */
                    foreach ($dataTabel['value'] as $valMod) {
                        extract($valMod);

                        if (!empty($flag_kepala_opd)) {
                            $keylock = "lock";
                            $chkDisabled = 'disabled="disabled"';
                        } else {
                            $chkDisabled = '';
                            $keylock = "lock_open";
                        }

                        ?>
                        <tr id="<?= $id ?>" class="btn-info-mod" style="cursor: pointer;">
                            <td class="center"><?= $nama_jenis ?></td>
                            
                            <td class="center">
                                <span class="chip brown white-text" title="<?= $ket_kode_presensi ?>">
                                    <?= $kode_presensi ?>
                                </span>
                            </td>

                            <td class="center">
                                <?= comp\FUNC::mergeDate($tanggal_awal, $tanggal_akhir); ?>
                            </td>

                            <td><?= $keterangan ?></td>

                            <td class="center">
                                <?= comp\FUNC::modSymbol($flag_operator_opd, $catatan_operator_opd) ?>
                            </td>

                            <td class="center">
                                <?= comp\FUNC::modSymbol($flag_kepala_opd, $catatan_kepala_opd) ?>
                            </td>

                            <td class="center">
                                <?= comp\FUNC::modSymbol($flag_operator_kota, $catatan_operator_kota) ?>
                            </td>

                            <td class="center">
                                <?= comp\FUNC::modSymbol($flag_kepala_kota, $catatan_kepala_kota) ?>
                            </td>

                            <td class="center">
                                <i class="material-icons"><?= $keylock ?></i>
                            </td>
                        </tr>
                        <?php
                    }

                } else {
                    ?>
                    <!-- Tidak ada data yang ditampilkan -->
                    <tr>
                        <td colspan="10" class="center-align">Tidak ada data yang dapat ditampilkan</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>