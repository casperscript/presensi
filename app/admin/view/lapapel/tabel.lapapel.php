<?php
//comp\FUNC::showPre($data);  
extract($dataTabel);
?>

<style>
    .black-cell {
        background: rgba(0, 0, 0, 0) -moz-linear-gradient(center top , #4c4c4c, #242424) repeat scroll 0 0;
        border-left: medium none;
        border-right-color: #191919;
        border-top-color: #7f7f7f;
        /*min-width: 1.333em;*/
        /*padding: 0.5em 0.583em;*/
    }
</style>
<table class="responsive-table bordered highlight striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align">No</th>
            <th>
                <span class="p-v-xs">
                    <input type="checkbox" id="checkAll" class="filled-in">
                    <label for="checkAll"></label>
                </span>
            </th>
            <th>NIP/NIK</th>
            <th>Nama</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td>
                    <?= comp\MATERIALIZE::inputCheckbox('pin_absen[' . $kol['pin_absen'] . ']', array($kol['pin_absen']), '', '') ?>
                </td>
                <td><?= $kol['nipbaru'] ?></td>
                <td><?= $kol['nama_personil'] ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>

<script>
    $("#checkAll").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
</script>