<div class="row">
	<div class="col m6">
		<h5 class="center-align"><b>Sudah Backup</b></h5>
		<table class="responsive-table bordered striped hoverable" id="listTable">
		    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
		        <tr>
		            <th class="center-align">No</th>
		            <th class="center-align">OPD</th>
		            <th class="center-align" width="150">Aksi</th>
		        </tr>
		    </thead>
		    <tbody>
	        	<?php
		        $no = 1;
		        foreach ($induk['value'] as $i) {
		        	echo '<tr>
		        		<td class="center-align">'.$no.'</td>';
		        	echo '<td>'.$i['singkatan_lokasi'].'</td>';
		        	
		        	echo '<td class="center-align"><a href="'.$this->link('adminsistem/backuplaporan/lihat/').$i['kdlokasi'].'/'.$data['bulan'].$data['tahun'].'" class="btn-floating btn waves-effect waves-light light-blue accent-4" title="Tampilkan" type="button">
                        <i class="material-icons left">info</i>
                    </a>';

              echo '<button class="btn-floating btn waves-effect waves-light red accent-4 btnHapus" title="Hapus" type="button" data-kdlokasi="'.$i['kdlokasi'].'" 
                    	data-lokasi="'.$i['singkatan_lokasi'].'"
                    >
                        <i class="material-icons left">delete</i>
                    </button>';

                if (!in_array($i['kdlokasi'], $sudah))   
	              echo '<button class="btn-floating btn waves-effect waves-light green accent-4 btnPresensi" title="Backup Presensi" type="button" data-kdlokasi="'.$i['kdlokasi'].'" 
	              	data-lokasi="'.$i['singkatan_lokasi'].'"
	              >
	                  <i class="material-icons left">system_update_alt</i>
	              </button>';
	              
		        	echo '</td></tr>';
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
		<table class="responsive-table bordered striped hoverable" id="listTablenot">
		    <thead class="grey darken-3 white-text" style="color: rgba(255, 255, 255, 0.901961);">
		        <tr>
		            <th class="center-align">No</th>
		            <th class="center-align">OPD</th>
		            <th class="center-align">Aksi</th>
		        </tr>
		    </thead>
		    <tbody>
	        	<?php
		        $no = 1;
		        foreach ($belum as $j) {
		        	echo '<tr>
		        		<td class="center-align">'.$no.'</td>';
		        	echo '<td>'.$lokasi[$j].'</td>';
		        	echo '<td class="center-align"><button class="btn-floating btn waves-effect waves-light amber darken-4 btnBackup" title="Backup" type="button" data-kdlokasi="'.$j.'">
                        <i class="material-icons left">system_update_alt</i>
                    </button></td>';
		        	echo '</tr>';
		        	$no++;
		        }
		        ?>
		    </tbody>
		</table>
	</div>
</div>