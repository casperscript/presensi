<h4><?= $form_title ?></h4>
<div class="row">
	<div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('jenis_pottunjangan', 'text', '', ''); ?>
        <label for="last_name" class="">Jenis Potongan Tunjangan</label>
    </div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('jumlah', 'text', '', ''); ?>
        <label for="last_name" class="">Jumlah</label>
    </div>
</div>