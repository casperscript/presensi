<?php
// comp\FUNC::showPre($data);  
extract($dataPersonil);
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
            <th>NIP/NIK</th>
            <th>Nama</th>
            <td>JADWAL</td>
            <th class="center-align">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($dataTabel as $kol) {
            $nama_jadwal = (isset($dataJadwal[$kol['pin_absen']])) ? $dataJadwal[$kol['pin_absen']]['nama_shift'] : '-';
            ?>
            <tr>
                <td class="center-align"><?= $no++ ?></td>
                <td><?= $kol['nipbaru'] ?></td>
                <td><?= $kol['nama_personil'] ?></td>
                <td><?= $nama_jadwal ?></td>
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
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>

<script>
    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15 // Creates a dropdown of 15 years to control year
    });
    $("#checkAll").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
</script>