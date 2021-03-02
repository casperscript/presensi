<?php
extract($moderasi);
//comp\FUNC::showPre($data);
$namapeg = !empty($pegawai['gelar_depan']) ? $pegawai['gelar_depan'] . ' ' : '';
$namapeg .= $pegawai['namapeg'];
$namapeg .= !empty($pegawai['gelar_blkg']) ? ', ' . $pegawai['gelar_blkg'] : '';

$tglAwal = comp\FUNC::tanggal($tanggal_awal, 'long_date');
$tglAkhir = comp\FUNC::tanggal($tanggal_akhir, 'long_date');
$tanggal = ($tanggal_awal == $tanggal_akhir) ? $tglAwal : $tglAwal . ' - ' . $tglAkhir;
?>
<style>
#modalDetail td, #modalDetail th {
	padding: 5px !important;
}
</style>

<div class="row" style="font-size: 0.8em">
	<div id="detail-info" class="col s12 m12 l6">
		<table class="highlight bordered striped" width="100%">
			<tr>
				<td>NIP</td>
				<td>:</td>
				<td><?= $pegawai['nipbaru'] ?></td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>:</td>
				<td><?= $namapeg ?></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>:</td>
				<td><?= $tanggal ?></td>
			</tr>
			<tr>
				<td>Kategori</td>
				<td>:</td>
				<td><?= $jenisMod['nama_jenis'] ?></td>
			</tr>
			<tr>
				<td>Jenis Moderasi</td>
				<td>:</td>
				<td><?= $kode_presensi ?></td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td>:</td>
				<td><?= $keterangan ?></td>
			</tr>
		</table>
		<br>

		<table class="bordered">
			<thead>
				<tr class="grey darken-3 white-text center-align">
					<th>Adm OPD</th>
					<th>Kep OPD</th>
					<th>Adm Kota</th>
					<th>Kep BKPPD</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= comp\FUNC::modSymbol($flag_operator_opd, $catatan_operator_opd) ?></td>
					<td><?= comp\FUNC::modSymbol($flag_kepala_opd, $catatan_kepala_opd) ?></td>
					<td><?= comp\FUNC::modSymbol($flag_operator_kota, $catatan_operator_kota) ?></td>
					<td><?= comp\FUNC::modSymbol($flag_kepala_kota, $catatan_kepala_kota) ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div id="detail-form" class="col s12" style="display: none">
		<?= comp\MATERIALIZE::inputKey('flag_operator_opd', '') ?>
		<?= comp\MATERIALIZE::inputKey('id', $id) ?>
		<div class="input-field">
			<label for="catatanAdmOPD" class="active">Catatan admin OPD</label>
			<textarea id="catatanAdmOPD" name="catatan_operator_opd" class="materialize-textarea"><?= $catatan_operator_opd ?></textarea>
		</div>
	</div>
</div>

<script>
	<?php
	if (is_null($flag_kepala_opd)) {
		?>
		$("#detail-info-btn, #detail-info").show();
	    <?php
	} else {
		?>
	    $("#detail-lock, #detail-info").show();
		<?php
	}
	?>
</script>