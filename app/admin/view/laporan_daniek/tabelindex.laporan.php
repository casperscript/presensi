<h5 class="center-align"><b>LAPORAN PRESENSI BULAN <?= strtoupper($namabulan) ?> <?= $tahun ?></b></h5>
<div class="col m4">
    <h5 class="center-align"><b>Sudah Masuk</b></h5>
    <ol>
    <?php
        $belum = $lokasi; $terverifikasi = '';
        foreach ($laporan as $i) {
            if ($i['sah_kepala_opd'] && !$i['ver_admin_kota']) {
                echo '<li><a href="#" class="btnLap" data-lokasi="'.$i['kdlokasi'].'" data-bulan="'.$bulan.'" data-tahun="'.$tahun.'" title="Verifikasi Laporan">'.$lokasi[$i['kdlokasi']].'</a></li>';
                unset($belum[$i['kdlokasi']]);
            } elseif ($i['ver_admin_kota']) {
                $terverifikasi .= '<li>'.$lokasi[$i['kdlokasi']].'</li>';
                unset($belum[$i['kdlokasi']]);
            }
        }
    ?>
    </ol>
</div>
<div class="col m4">
    <h5 class="center-align"><b>Belum Masuk</b></h5>
    <ol>
    <?php
        foreach ($belum as $i) {
            echo '<li>'.$i.'</li>';
        }
    ?>
    </ol>
</div>
<div class="col m4">
    <h5 class="center-align"><b>Sudah Terverifikasi</b></h5>
    <ol><?= $terverifikasi ?></ol>
</div>