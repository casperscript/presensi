<?php 
$last = '';
var_dump($terakhir);die;
if ($terakhir && $terakhir['last_update'])
    $last = '<span class="green-text text-darken-2">Data Apel Terakhir Tanggal ' . comp\FUNC::tanggal($terakhir['last_update'], 'long_date') . '</span>';
else
    $last = '<span class="red-text text-darken-2">Data Apel tidak ditemukan</span>';
?>
<div class="right-align"><b><?= $last ?></b></div>
<table class="bordered striped hoverable custom-border">
    <thead>
        <tr>
            <th class="grey darken-3 white-text center-align">No</th>
            <th class="grey darken-3 white-text center-align">Nama</th>
            <?php
                for ($i=1; $i <= 31; $i++)
                    echo "<td class='grey darken-3 white-text center-align' width='25'>$i</td>";
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach ($pegawai['value'] as $peg) { ?>
            <tr>
                <td><?= $no ?></td>
                <td class="center-align"><?= $peg['nama_personil'] ?></td>
                <?php
                    $pin = $peg['pin_absen'];
                    $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $bln, $tahun);
                    for ($i = 1; $i <= $hitungtgl; $i++) {
                        $tgl = $tahun . '-'. $bln . '-' . $i;
                        $hari = date("l", strtotime($tgl));
                        if (isset($rekap[$pin][$i]) && $rekap[$pin][$i] != 'A2')
                            echo '<td class="center-align green accent-3"><b>'.$rekap[$pin][$i].'</b></td>';
                        elseif ($hari != 'Saturday' && $hari != 'Sunday' && strtotime($tgl) <= strtotime(date('Y-m-d')))
                            echo '<td class="center-align red accent-3"><b>A2</b></td>';
                        else
                            echo '<td></td>';
                    }
                ?>
            </tr>
        <?php 
            $no++;
        } 
        ?>
    </tbody>
</table>