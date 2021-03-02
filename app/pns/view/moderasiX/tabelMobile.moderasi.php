<div class="search-page-results">
	<div id="web" class="col s12">
		<div class="card">
			<?php
			if ($dataTabel['count'] > 0) {
				foreach ($dataTabel['value'] as $valMod) {
					extract($valMod);
					$lockStatus = is_null($flag_kepala_opd) ? false : true;
					?>
					<div id="<?= $id ?>" class="card-content btnInfo listModerasiMob hoverable" style="cursor: pointer">
						<div class="search-result">
							<a href="javascript:void(0)" class="search-result-title">
								<?= $kode_presensi . ' - ' . $nama_jenis ?>
								<?= comp\FUNC::modStatus($flag_operator_opd, $lockStatus) ?><?= comp\FUNC::modStatus($flag_operator_opd) ?>
							</a>
							<a href="javascript:void(0)" class="search-result-link">
								<i class="material-icons tiny">date_range</i> <?= comp\FUNC::mergeDate($tanggal_awal, $tanggal_akhir) ?>
							</a>
							<p class="search-result-description">
	                            <i class="tiny material-icons">textsms</i>
	                            <?= empty($keterangan) ? '-' : $keterangan ?>
	                        </p>
						</div>
					</div>
					<div class="divider no-s"></div>
					<?php
				}
			} else {
				?>
				<div class="card-panel orange darken-4 white-text">
	                <div class="valign-wrapper">
	                    <div class="s3"><i class="material-icons medium circle">android</i></div>
	                    <div class="s9">
	                        <span>Tidak ada data yang dapat ditampilkan</span>
	                    </div>
	                </div>
	            </div>
				<?php
			}
			?>
		</div>
	</div>
</div>