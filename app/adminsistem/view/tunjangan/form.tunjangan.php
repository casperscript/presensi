<h4><?= $form_title ?></h4>
<div class="row">
	<div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('jenis_tunjangan', 'text', '', ''); ?>
        <label for="last_name" class="">Jenis Tunjangan</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('jumlah', 'text', '', ''); ?>
        <label for="last_name" class="">Jumlah</label>
    </div>
</div>