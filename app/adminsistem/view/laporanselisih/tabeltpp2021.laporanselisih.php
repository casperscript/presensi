<?php

//comp\FUNC::showPre($data);exit;
function compareClass($val1, $val2) { //1 yang ditampilkan, 2 pembanding
    if ($val1 - $val2 != 0) :
        return 'red lighten-4';
    else:
        return 'teal lighten-4';
    endif;
}
?>
<h5 class="center-align">
    <b>
        <?= $satker['nmlokasi'] ?><br>
        Bulan <?= comp\FUNC::$namabulan1[$bulan] ?> Tahun <?= $tahun ?>        
    </b>
</h5>
<table class="bordered hoverable custom-border">
    <thead>
        <tr>
            <th class="grey lighten-2 center-align" rowspan="2">No</th>
            <th class="grey lighten-2 center-align" rowspan="2">Nama / NIP / NPWP / Jabatan</th>
            <th class="grey lighten-2 center-align" rowspan="2">Kelas Jab.</th>
            <th class="grey lighten-2 center-align" colspan="2">Tambahan Penghasilan</th>
            <th class="grey lighten-2 center-align" colspan="2">Persentase Potongan</th>
            <th class="grey lighten-2 center-align" rowspan="2">Potongan</th>
            <th class="grey lighten-2 center-align" rowspan="2">TPP Kotor</th>
            <th class="grey lighten-2 center-align" rowspan="2">Pajak</th>
            <th class="grey lighten-2 center-align" colspan="2">TPP Bersih</th>
            <th class="grey lighten-2 center-align" rowspan="2">BPJS</th>
            <th class="grey lighten-2 center-align" colspan="2">Diterimakan</th>
        </tr>
        <tr>	
            <th class="grey lighten-2 center-align">Proses</th>
            <th class="grey lighten-2 center-align">Backup</th>
            <th class="grey lighten-2 center-align">Proses</th>
            <th class="grey lighten-2 center-align">Backup</th>
            <th class="grey lighten-2 center-align">Proses</th>
            <th class="grey lighten-2 center-align">Backup</th>
            <th class="grey lighten-2 center-align">Proses</th>
            <th class="grey lighten-2 center-align">Backup</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $pin_absen = '';
        $tot_tpp = 0;
        $tot_tpp40 = 0;
        $tot_tpp60 = 0;
        $tot_pot = 0;
        $tot_tppkotor = 0;
        $tot_pajak = 0;
        $tot_terima = 0;
        $tot_potbpjs = 0;
        $tot_terimapotbpjs = 0;
        $tot_bc_tppterima = 0;
        $tot_bc_nominaltp = 0;
        $tot_bc_tppbersih = 0;
        foreach ($pegawai['value'] as $peg) {
            if (isset($tppbackup[$peg['nipbaru']])) {
                $bc = $tppbackup[$peg['nipbaru']];
            } else {
                $bc['nominal_tp'] = 0;
                $bc['pot_final'] = 0;
                $bc['tpp_kotor'] = 0;
                $bc['tpp_bersih'] = 0;
                $bc['pot_bpjskes'] = 0;
                $bc['tpp_terima'] = 0;
            }
            $pin = $peg['pin_absen'];
            $sum = $rekap[$pin]['sum_pot'];
            $sum['all'] = ($sum['all'] > 100 ? 100 : $sum['all']);

            //mengenolkan tunj
            $sum['all'] = (!is_numeric($sum['all']) ? 100 : $sum['all']);

            $nominal_tp40 = $peg['nominal_tp'] * 40 / 100;
            $nominal_tp60 = $peg['nominal_tp'] * 60 / 100;

            $pot = round($sum['all'] / 100 * $nominal_tp60, -1);
            $tpp_kotor = $peg['nominal_tp'] - $pot;
            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $pot_pajak = 0;
            if (isset($pajak[$gol])) {
                $pot_pajak = round($pajak[$gol] * $tpp_kotor);
            }

            $terima = round($tpp_kotor - $pot_pajak);

            $checkBpjsGaji = round((($peg['nominal_tp'] + $peg['totgaji']) > $kenabpjs['value']) ?
                    ($kenabpjs['value'] - $peg['totgaji']) * 0.01 :
                    $peg['nominal_tp'] * 0.01);
            $pot_bpjs = ($terima > $checkBpjsGaji) ? $checkBpjsGaji : $terima;
            $terima_potbpjs = round($terima - $pot_bpjs);
            ?>
            <tr>
                <td class="center-align"><?= $no ?></td>
                <td>
                    <?= $peg['nama_personil'] ?>
                    <br><?= $peg['nipbaru'] ?>
                    <br><?= ($peg['npwp'] ? $peg['npwp'] : '-') ?>
                    <br>Golongan <?= $peg['golruang'] ?>
                </td>
                <td class="center-align"><?= $peg['kelas'] ?></td>
                <td class="right-align"><?= number_format($nominal_tp40 + $nominal_tp60, 0, ",", ".") ?></td>
                <td class="right-align <?= compareClass($nominal_tp40 + $nominal_tp60, $bc['nominal_tp']) ?>"><?= number_format($bc['nominal_tp'], 0, ",", ".") ?></td>
                <?php
                if ($rekap[$pin]['pot_penuh']) :
                    $sum['all'] = 100;
                    ?>
                    <td class="center-align"><?= $rekap[$pin]['sum_pot']['all'] ?></td>
                <?php else: ?>
                    <td class='center-align'><?= $sum['mk'] + $sum['ap'] + $sum['pk'] ?></td>
                <?php endif; ?>
                <td class="center-align green lighten-4"><?= $bc['pot_final'] ?></td>

                <td class="right-align"><?= ($pot > 0 ? number_format($pot, 0, ",", ".") : '-') ?></td> <!-- total potongan kehadiran -->
                <td class="right-align"><?= ($tpp_kotor ? number_format($tpp_kotor, 0, ",", ".") : '-') ?></td> <!-- tpp kotor -->
                <td class="right-align"><?= ($pot_pajak ? number_format($pot_pajak, 0, ",", ".") : '-') ?></td> <!-- pajak -->
                <td class="right-align"><?= ($terima ? number_format($terima, 0, ",", ".") : '-') ?></td> <!-- tpp bersih -->
                <td class="right-align <?= compareClass($terima, $bc['tpp_bersih']) ?>"><?= number_format($bc['tpp_bersih'], 0, ",", ".") ?></td> <!-- total potongan kehadiran -->
                <td class="right-align"><?= ($pot_bpjs ? number_format($pot_bpjs, 0, ",", ".") : '-') ?></td> <!-- potongan bpjs -->
                <td class="right-align"><?= ($terima_potbpjs ? number_format($terima_potbpjs, 0, ",", ".") : '-') ?></td> <!-- diterimakan -->
                <td class="right-align <?= compareClass($terima_potbpjs, $bc['tpp_terima']) ?>"><?= number_format($bc['tpp_terima'], 0, ",", ".") ?></td> <!-- diterimakan [backup] -->
            </tr>
            <?php
            $pin_absen .= $pin . (count($pegawai['value']) != $no ? ',' : '');
            $tot_tpp += $peg['nominal_tp'];
            $tot_tpp40 += $nominal_tp40;
            $tot_tpp60 += $nominal_tp60;
            $tot_pot += $pot;
            $tot_tppkotor += $tpp_kotor;
            $tot_pajak += $pot_pajak;
            $tot_terima += $terima;
            $tot_potbpjs += $pot_bpjs; //acil
            $tot_terimapotbpjs += $terima_potbpjs; //acil
            $tot_bc_tppterima += $bc['tpp_terima'];
            $tot_bc_nominaltp += $bc['nominal_tp'];
            $tot_bc_tppbersih += $bc['tpp_bersih'];
            $no++;
        }
        ?>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th class="right-align"><?= number_format($tot_tpp40 + $tot_tpp60, 0, ",", ".") ?></th>
            <th class="right-align <?= compareClass($tot_tpp40 + $tot_tpp60, $tot_bc_nominaltp) ?>"><?= number_format($tot_bc_nominaltp, 0, ",", ".") ?></th>
            <th></th>
            <th></th>
            <th class="right-align"><?= ($tot_pot > 0 ? number_format($tot_pot, 0, ",", ".") : '-') ?></th>
            <th class="right-align"><?= number_format($tot_tppkotor, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_pajak, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_terima, 0, ",", ".") ?></th>
            <th class="right-align <?= compareClass($tot_terima, $tot_bc_tppbersih) ?>"><?= number_format($tot_bc_tppbersih, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_potbpjs, 0, ",", ".") ?></th>
            <th class="right-align"><?= number_format($tot_terimapotbpjs, 0, ",", ".") ?></th>
            <th class="right-align <?= compareClass($tot_terimapotbpjs, $tot_bc_tppterima) ?>"><?= number_format($tot_bc_tppterima, 0, ",", ".") ?></th> <!-- backup -->
        </tr>
    </tbody>
</table>

<script>
    $(".infoSatker").html("<?= $satker['singkatan_lokasi'] ?>");
<?php if ($tot_terimapotbpjs - $tot_bc_tppterima == 0) { ?>
        $(".infoChip").html("OK");
<?php } else { ?>
        $(".infoChip").html("GAGAL!!");
<?php } ?>
</script>
