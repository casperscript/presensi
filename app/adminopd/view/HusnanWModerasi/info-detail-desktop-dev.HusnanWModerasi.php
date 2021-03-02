<?php
extract($moderasi);

function tanggal($date = '') {
	$sdate = '2010-01-01';
	$viewDate = !empty($date && $date > $sdate) ? comp\FUNC::tanggal($date, 'long_date') : '-';
	return $viewDate;
}

$namapeg = !empty($pegawai['gelar_depan']) ? $pegawai['gelar_depan'] . ' ' : '';
$namapeg .= $pegawai['namapeg'];
$namapeg .= !empty($pegawai['gelar_blkg']) ? ', ' . $pegawai['gelar_blkg'] : '';

$tglAwal = comp\FUNC::tanggal($tanggal_awal, 'long_date');
$tglAkhir = comp\FUNC::tanggal($tanggal_akhir, 'long_date');
$tanggal = ($tanggal_awal == $tanggal_akhir) ? $tglAwal : $tglAwal . ' - ' . $tglAkhir;

//comp\FUNC::showPre($data);
?>
<style>
#modalDetail td, #modalDetail th {
	padding: 5px !important;
}
</style>

<div class="row">
	<div id="detail-info" class="col s12">
		<table class="highlight bordered stripped" width="100%">
			<tr>
				<td width="130px">NIP</td>
				<td width="5px">:</td>
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
				<td><?= $kode_presensi . ' (' . $kodeMod['ket_kode_presensi'] . ')'?></td>
			</tr>
			<tr>
				<td>Potongan</td>
				<td>:</td>
				<td><?= $kodeMod['pot_kode_presensi'] ?> %</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td>:</td>
				<td><?= $keterangan ?></td>
			</tr>
		</table>
		<br>
		<table class="bordered center-align stripped">
			<thead>
				<tr class="grey darken-3 white-text">
					<th></th>
					<th class="center-align" width="20%">Adm OPD</th>
					<th class="center-align" width="20%">Kep OPD</th>
					<th class="center-align" width="20%">Adm Kota</th>
					<th class="center-align" width="20%">Kep BKPPD</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="grey darken-1 white-text">Status</td>
					<td class="center-align"><?= comp\FUNC::modSymbol($flag_operator_opd) ?></td>
					<td class="center-align"><?= comp\FUNC::modSymbol($flag_kepala_opd) ?></td>
					<td class="center-align"><?= comp\FUNC::modSymbol($flag_operator_kota) ?></td>
					<td class="center-align"><?= comp\FUNC::modSymbol($flag_kepala_kota) ?></td>
				</tr>
				<tr>
					<td class="grey darken-1 white-text">Tgl Verif.</td>
					<td class="center-align"><?= tanggal($dt_flag_operator_opd) ?></td>
					<td class="center-align"><?= tanggal($dt_flag_kepala_opd) ?></td>
					<td class="center-align"><?= tanggal($dt_flag_operator_kota) ?></td>
					<td class="center-align"><?= tanggal($dt_flag_kepala_kota) ?></td>
				</tr>
				<tr>
					<td class="grey darken-1 white-text">Catatan</td>
					<td class="hoverable"><?= $catatan_operator_opd ?></td>
					<td class="hoverable"><?= $catatan_kepala_opd ?></td>
					<td class="hoverable"><?= $catatan_operator_kota ?></td>
					<td class="hoverable"><?= $catatan_kepala_kota ?></td>
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
	//$(".detailAction").hide();
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