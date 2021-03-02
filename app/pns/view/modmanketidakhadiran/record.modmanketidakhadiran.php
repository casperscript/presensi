<?php
//comp\FUNC::showPre($data);
extract($dataTabel);
extract($dataPersonal);

echo $dataPersonal['nipbaru']." | ".$dataPersonal['nama_personil']."<br><br>";
?>

<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no=1;
        //echo $dataPersonal['nipbaru']."<br>"; 
        foreach ($dataTabel as $kol) {
            if($kol['kd_moderasi_presensi']==''){
                $status="Belum Moderasi";
            }else{
                $status="Sudah Moderasi";
            }
            
            ?>
        <tr>
            <td class="center-align"><?= $no++; ?></td>
            <td><?= comp\FUNC::tanggal($kol['tanggal_presensi'], 'long_date'); ?></td>
            <td><?= comp\FUNC::tanggal($kol['tanggal_presensi'], 'day'); ?></td>
            <td><?= $kol['jam_masuk_presensi']; ?> </td>
            <td><?= $kol['jam_pulang_presensi']; ?></td>
            <td><?= $status; ?></td>
        </tr>
            <?php
            $no++;
        }
        ?>
    </tbody>
</table>

