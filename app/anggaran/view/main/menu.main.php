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
                <!-- modified by husnanw -->
                <img src="<?= (isset($selfId['foto'])) ? $this->simpeg_url."/".$selfId["foto"] : $this->link('template/theme_admin/assets/images/profile-image.png') ?>" class="circle" alt="fotoku">
                <!-- ### -->
            </div>
            <div class="sidebar-profile-info">
                <a class="pointer">
                    <!-- modified by husnanw -->
                    <p><?= (isset($selfId["nama_lengkap"])) ? $selfId['nama_lengkap'] : 'Administrator' ?></p>
                    <span style="text-transform: lowercase">Badan Keuangan Daerah</span>
                    <!-- ### -->
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
                    <i class="material-icons">library_books</i>Laporan<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_laporan ?>">Laporan OPD</a></li>
                        <li><a href="<?= $link_rekap ?>">Rekap Laporan</a></li>
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
