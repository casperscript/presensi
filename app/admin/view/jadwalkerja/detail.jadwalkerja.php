<?php
extract($tabelJadwal);
//comp\FUNC::showPre($data);
echo comp\MATERIALIZE::inputKey('pin_absen', $dataPersonil['pin_absen']);
?>
<div class="card horizontal">
    <div class="card-image">
        <img src="<?= $this->simpeg_url . '/' . $dataPersonil['foto_pegawai'] ?>" height="150px">
    </div>
    <div class="card-stacked">
        <div class="card-content" style="padding: 5px 10px">
            <h5><?= $dataPersonil['nama_personil'] ?></h5>
            <address>
                NIP. <?= $dataPersonil['nipbaru'] ?><br />
                <?= $dataInfoSatker[$dataPersonil['kdlokasi']] ?> 
            </address>
        </div>
        <div class="card-action cardDetail" style="padding: 5px 10px">
            <a class="btn btn-floating waves-effect red btnToNavSatker" title="Kembali"><i class="material-icons">reply</i></a>
            <a id="" class="btn btn-floating waves-effect green btnAddJadwal" title="Tambah Data"><i class="material-icons">input</i></a>
        </div>
    </div>
</div>

<table class="responsive-table bordered highlight striped hoverable">
    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
        <tr>
            <th class="center-align" rowspan="2">No</th>
            <th rowspan="2">Nama Jadwal</th>
            <th class="center-align" colspan="2">Berlaku</th>
            <th class="center-align" rowspan="2">Status</th>
            <th class="center-align" class="center-align" rowspan="2">Aksi</th>
        </tr>
        <tr>
            <th class="center-align">Mulai</th>
            <th class="center-align">Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($jmlData > 0) {
            foreach ($dataTabel as $kol) {
                ?>
                <tr>
                    <td class="center-align"><?= $no++ ?></td>
                    <td><?= $dataShift[$kol['id_shift']] ?></td>
                    <td class="center-align"><?= comp\FUNC::tanggal($kol['sdate'], 'long_date') ?></td>
                    <td class="center-align"><?= comp\FUNC::tanggal($kol['edate'], 'long_date') ?></td>
                    <td></td>
                    <td class="center-align">
                        <a id="<?= $kol['id_jadwal'] ?>" class="btn-floating waves-effect red waves-light btn btnDelete">
                            <i class="material-icons">delete</i>
                        </a>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td class="center-align" colspan="6">
                    <strong>Tidak ada jadwal</strong>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?= comp\MATERIALIZE::pagging($page, $batas, $jmlData); ?>

<script>
    $(".cardDetail").on("click", ".btnAddJadwal", function () {
        app.showForm(this.id, "<?= $dataPersonil['pin_absen'] ?>", "<?= $dataPersonil['kdlokasi'] ?>");
    });
</script>