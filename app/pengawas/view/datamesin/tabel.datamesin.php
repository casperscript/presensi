<?php 
//comp\FUNC::showPre($data);  
extract($dataTabel);
?>
<table class="responsive-table bordered striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>Pin Absen</th>
            <th>NIP/NIK</th>
            <th>Nama</th>
            <th class="center-align">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td><?= $kol['pin_absen'] ?></td>
                <td><?= $kol['nipbaru'] ?></td>
                <td><?= $kol['nama_personil'] ?></td>
                <td class="center-align">
                    <a id="<?= $kol['pin_absen'] ?>" class="btn-floating waves-effect waves-light blue-grey btn btnDetail">
                        <i class="material-icons">info_outline</i>
                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<!--pagging($aktif, $batas, $jml_data)-->
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>
