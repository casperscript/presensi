<?php
//comp\FUNC::showPre($_SESSION);
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
            <th>Nama</th>
            <th class="center-align">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($dataTabel as $key => $val) {
            if ($key > 10000000) {
                ?>
                <tr>
                    <td class="center-align"><?= $no++ ?></td>
                    <td>
                        <?= comp\MATERIALIZE::inputCheckbox('id[' . $key . ']', array($key), '', '') ?>
                    </td>
                    <td><?= $val ?></td>
                    <td class="center-align">
                        <a id="<?= $key ?>" class="btn-floating waves-effect waves-light blue-grey btn btnDetail">
                            <i class="material-icons">info_outline</i>
                        </a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
