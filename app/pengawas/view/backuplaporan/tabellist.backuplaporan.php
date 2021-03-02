<div class="row">
	<div class="col m6">
		<h5 class="center-align"><b>Sudah Backup</b></h5>
		<table class="responsive-table bordered striped hoverable">
		    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
		        <tr>
		            <th class="center-align" rowspan="2">No</th>
		            <th class="center-align" rowspan="2">OPD</th>
		            <th class="center-align" rowspan="2">Aksi</th>
		        </tr>
		    </thead>
		    <tbody>
	        	<?php
		        $no = 1;
		        foreach ($induk['value'] as $i) {
		        	echo '<tr>
		        		<td class="center-align">'.$no.'</td>';
		        	echo '<td>'.$i['singkatan_lokasi'].'</td>';
		        	echo '<td class="center-align"><a href="'.$this->link('pengawas/backuplaporan/lihat/').$i['kdlokasi'].'/'.$data['bulan'].$data['tahun'].'" class="btn-floating btn waves-effect waves-light light-blue accent-4" title="Tampilkan" type="button">
                        <i class="material-icons left">info</i>
                    </a></td>';
		        	echo '</tr>';
		        	$no++;
		        }
		        ?>
		    </tbody>
		</table>
	</div>
	<?= comp\MATERIALIZE::inputKey('bln', $bulan); ?>
	<?= comp\MATERIALIZE::inputKey('thn', $tahun); ?>
	<div class="col m6">
		<h5 class="center-align"><b>Belum Backup</b></h5>
		<table class="responsive-table bordered striped hoverable">
		    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
		        <tr>
		            <th class="center-align" rowspan="2">No</th>
		            <th class="center-align" rowspan="2">OPD</th>
		            <!--th class="center-align" rowspan="2">Aksi</th-->
		        </tr>
		    </thead>
		    <tbody>
	        	<?php
		        $no = 1;
		        foreach ($belum as $j) {
		        	echo '<tr>
		        		<td class="center-align">'.$no.'</td>';
		        	echo '<td>'.$lokasi[$j].'</td>';
		        	/*echo '<td class="center-align"><button class="btn-floating btn waves-effect waves-light amber darken-4 btnBackup" title="Backup" type="button" data-kdlokasi="'.$j.'">
                        <i class="material-icons left">system_update_alt</i>
                    </button></td>';*/
		        	echo '</tr>';
		        	$no++;
		        }
		        ?>
		    </tbody>
		</table>
	</div>
</div>