<div class="row">

    <?= comp\MATERIALIZE::inputKey('id_mesin', $id_mesin) ?>
    <div class="input-field col s12">
        <span class="title"><?= $message ?></span>
    </div>
    <div class="file-field input-field col s12">
        <div class="btn teal lighten-1">
            <span>File</span>
            <input id="file_import" type="file" name="file_import" accept="">
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Gambar berita">
        </div>
    </div>

</div>

<script>
    $("#frmInput #modalHeader").html("<?= $form_title ?>");
</script>