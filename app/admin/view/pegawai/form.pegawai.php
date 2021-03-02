<h4><?= $form_title ?></h4>
<div class="row">
	<div class="input-field col s12">
		<?= comp\MATERIALIZE::inputText('nip', 'text', '', ''); ?>
		<label for="last_name" class="">NIP</label>
	</div>
    <div class="input-field col s12">
        <?= comp\MATERIALIZE::inputText('nama_lengkap', 'text', '', ''); ?>
        <label for="last_name" class="">Nama Lengkap</label>
    </div>
	<div class="input-field col s12">
        <select>
            <option value="" disabled selected>-- Pilih OPD --</option>
            <option value="1">Option 1</option>
            <option value="2">Option 2</option>
            <option value="3">Option 3</option>
        </select>
        <label>OPD</label>
    </div>
</div>