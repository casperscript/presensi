<?php
extract($dataShift);
//comp\FUNC::showPre($data);
$firstShift = ($jmlData > 0) ? $dataTabel[0]['id_shift'] : 0;
echo comp\MATERIALIZE::inputKey('idShift', '');
?>
<div id="tabelShift" class="col s4">
    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title">Daftar Shift</span>
            <ul class="collection">
                <?php
                foreach ($dataTabel as $kol) {
                    ?>
                    <li id="list<?= $kol['id_shift'] ?>" class="collection-item dismissable">
                        <?= $kol['nama_shift'] ?>
                        <a id="<?= $kol['id_shift'] ?>" class="secondary-content btnShift" href="javascript:void(0)"><i class="material-icons">send</i></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<div class="col s8">
    <div id="showJamKerja" class="card hoverable"></div>
</div>
<script>
    $(".navShift #kdlokasi").val("<?= $dataSatker['kdlokasi'] ?>");
    $(".navShift #nama_satker").html("<?= $dataSatker['singkatan_lokasi'] ?>");
    $(".navShift #alamat_satker").html("<?= $dataSatker['almt_kantor'] ?>");
    app.loadJamKerja("<?= $firstShift ?>");
    $("#tabelShift").on("click", ".btnShift", function () {
        app.loadJamKerja(this.id);
    });
    $("#showJamKerja").on("click", ".addJam", function () {
        app.showFormJam(this.id);
    });
    $("#showJamKerja").on("click", ".btnEditShift", function () {
        app.showFormEditShift(this.id);
    });
    $("#showJamKerja").on("click", ".btnHapusShift", function () {
        var title = "Apakah anda yakin?";
        var msg = "Data yang dihapus tidak dapat dikembalikan!";
        var op = "shift";
        var field = "id_shift";
        app.showConfirm(this.id, op, field, title, msg);
    });
</script>