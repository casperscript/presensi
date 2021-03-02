<!-- added by husnanw -->
<style>
ul#ulModerasi > li a:hover i {
    color: #f00 !important;
}
</style>
<!-- ### -->
<?php //comp\FUNC::showPre($this) ?>
<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <!-- modified by husnanw -->
                <!--<img src="<?= $this->simpeg_url."/".$selfId["foto"] ?>" class="circle" alt="fotoku">-->
                <!-- ### -->
            </div>
            <div class="sidebar-profile-info">
                <a class="pointer">
                    <p>Admin Sistem</p>
                </a>
            </div>
        </div>
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_beranda;?>">
                    <i class="material-icons">desktop_windows</i>Beranda
                </a>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">apps</i>Master<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>                        
                        <!-- Start Menu Master -->
                        <li><a href="<?= $link_master_kode_presensi; ?>">Kode Presensi</a></li>
                        <li><a href="<?= $link_master_moderasi ?>">Jenis Moderasi</a></li>
                        <li><a href="<?= $link_master_cuti ?>">Jenis Cuti</a></li>
                        <li><a href="<?= $link_master_dinas ?>">Jenis Dinas</a></li>
                        <li><a href="<?= $link_master_gruppengguna ?>">Grup Pengguna</a></li>
                        <li><a href="<?= $link_master_pengguna ?>">Pengguna</a></li>
                        <li><a href="<?= $link_master_jam_kerja; ?>">Jam Kerja</a></li>
                        <li><a href="<?= $link_master_aturan; ?>">Aturan</a></li>
                        <li><a href="<?= $link_master_shift; ?>">Shift</a></li>
                        <li><a href="<?= $link_master_libur; ?>">Libur</a></li>
                        <li><a href="<?= $link_master_potongan_pajak; ?>">Potongan Pajak</a></li>
                        <li><a href="<?= $link_master_mesin; ?>">Mesin Fingerprint</a></li>
                        <li><a href="<?= $link_master_panduan; ?>">Panduan</a></li>
                        
                        <li><a href="<?= $link_jamapelpagi ?>">Jam Apel Pagi</a></li>
                        <li><a href="<?= $link_master_tpp; ?>">TPP</a></li>
                        <li><a href="<?= $link_master_teks; ?>">Teks</a></li>
                        <!-- End Menu Master -->
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">work</i>Waktu Kerja<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_jadwalkerja ?>">Jadwal</a></li>
                        <li><a href="<?= $link_shift ?>">Shift/Kelompok</a></li>
                        <!--<li><a href="<?= $link_jamkerja ?>">Jam Kerja</a></li>-->
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">work</i>Presensi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_datamesin ?>">Data Mesin</a></li>
                        <li><a href="<?= $link_usermesin ?>">User Fingerprint</a></li>
                        <li><a href="<?= $link_updatemesin ?>">Pembaruan Data Mesin</a></li>
                    </ul>
                </div>
            </li>
	    	<li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">spellcheck</i>Apel Pagi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_batalapelpagi ?>">Pembatalan Apel Pagi</a></li>
                    </ul>
                </div>
            </li>
            
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons red-text">star</i>Moderasi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul id="ulModerasi">
                        <!--<li><a href=" $link_husnanw_moderasi ?>"><i class="material-icons">star</i>Pengajuan Moderasi Absensi</a></li>-->
                        <li><a href="<?= $link_husnanw_daftar_ver_mod_proses ?>"><i class="material-icons">star</i>Daftar Proses Moderasi</a></li>
                        <li><a href="<?= $link_husnanw_daftar_ver_mod_hasil ?>"><i class="material-icons">star</i>Status Final Moderasi</a></li>
                    </ul>
                </div>
            </li>

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">library_books</i>Laporan<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <!--li><a href="<?= $link_lap_kehadiran ?>">Laporan Kehadiran</a></li>
                        <li><a href="<?= $link_lap_disiplin ?>">Laporan Disiplin</a></li-->
                        <li><a href="<?= $link_laporan ?>">Cetak Laporan</a></li>
                    </ul>
                </div>
            </li>

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_backuplaporan;?>">
                    <i class="material-icons">query_builder</i>Backup Laporan
                </a>
            </li>

			<li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">assignment_late</i>Tools<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="">Backup Data Semua Finger</a></li>
						<li><a href="">Backup Data Manual</a></li>
                    </ul>
                </div>
            </li>
            
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">settings</i>Pengaturan<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_pengaturan_profil; ?>">Profil</a></li>
                    </ul>
                </div>
            </li>
            
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_logout;?>">
                    <i class="material-icons">exit_to_app</i>Keluar
                </a>
            </li>            
            
        </ul>
        <div class="footer">
            © 2017 <br>Developed by 
            <b><a href="https://kominfo.pekalongankota.go.id" target="_blank">Dinkominfo</a></b>
        </div>
    </div>
</aside>
