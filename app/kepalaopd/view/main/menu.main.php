<!-- added by husnanw -->
<style>
ul#ulModerasi > li a:hover i {
    color: #f00 !important;
}
</style>
<!-- ### -->
<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <img src="<?= (isset($selfId['foto'])) ? $this->simpeg_url."/".$selfId["foto"] : 'assets/images/profile-image.png' ?>" class="circle" alt="fotoku">
            </div>
            <div class="sidebar-profile-info">
                <a class="pointer">
                    <p><?= (isset($selfId["nama_lengkap"])) ? $selfId["nama_lengkap"] : 'Nama Tidak Terdaftar' ?></p>
                    <span style="text-transform: lowercase"><?=$selfId['jabatan_pengguna']?> KEPALAOPD@<?= $selfId["singkatan_lokasi"] ?></span>
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
                    <i class="material-icons">work</i>Apel Pagi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <!--li><a href="<?= $link_apelpagi ?>">Apel Pagi</a></li-->
                        <li><a href="<?= $link_batalapelpagi ?>">Pembatalan Apel Pagi</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_daftar_ver_mod ?>">
                <i class="material-icons red-text">star</i>Moderasi<!--i class="nav-drop-icon material-icons">keyboard_arrow_right</i-->
                </a>
            </li>
            
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">library_books</i>Laporan<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_laporan ?>">Verifikasi - Sahkan Laporan</a></li>
                        <li><a href="<?= $link_laporan_final ?>">Laporan Final</a></li>
                    </ul>
                </div>
            </li>
            
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_panduan;?>">
                    <i class="material-icons">description</i>Panduan
                </a>
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
