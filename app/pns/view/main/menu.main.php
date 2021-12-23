<!-- added by husnanw -->
<style>
    ul#ulModerasi>li a:hover i {
        color: #f00 !important;
    }
</style>
<!-- ### -->

<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <img src="<?= (!empty($selfId['foto'])) ? $this->link() . $selfId["foto"] : $this->link() . 'template/theme_admin/assets/images/profile-image.png' ?>" class="circle" alt="Foto Pegawai">
            </div>
            <div class="sidebar-profile-info">
                <a class="pointer">
                    <!-- modified by husnanw -->
                    <p><?= (isset($selfId["nama_lengkap"])) ? $selfId['nama_lengkap'] : 'Nama Tidak Terdaftar' ?></p>
                    <span style="text-transform: lowercase">PNS@<?= (isset($selfId["singkatan_lokasi"])) ? $selfId["singkatan_lokasi"] : 'Undefined'; ?></span>
                    <!-- ### -->
                </a>
            </div>
        </div>
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_beranda; ?>">
                    <i class="material-icons">desktop_windows</i>Beranda
                </a>
            </li>

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">work</i>Presensi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <!--li><a href="<?= $link_apelpagi; ?>">Apel Pagi</a></li-->
                        <li><a href="<?= $link_datakehadiran ?>">Data Kehadiran</a></li>
                    </ul>
                </div>
            </li>

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_moderasi ?>">
                    <i class="material-icons">email</i> Moderasi
                </a>
            </li>

            <!-- added by husnanw --
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                <i class="material-icons red-text">star</i>Moderasi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>                
                <div class="collapsible-body">
                    <ul id="ulModerasi">
                        <li><a href="<?= $link_husnanw_moderasi ?>"><i class="material-icons">star</i>Pengajuan Moderasi</a></li>
                        <li><a href="<?= $link_husnanw_daftar_mod_proses ?>"><i class="material-icons">star</i>Daftar Proses Moderasi</a></li>
                        <li><a href="<?= $link_husnanw_daftar_mod_hasil ?>"><i class="material-icons">star</i>Status Final Moderasi</a></li>
                    </ul>
                </div>
            </li>
            <!-- ### -->

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_laporan; ?>">
                    <i class="material-icons">library_books</i>Laporan
                </a>
            </li>

            <!--li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">library_books</i>Laporan<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="">Detail Kehadiran</a></li>
                        <li><a href="">Kedisplinan</a></li>
                       
                    </ul>
                </div>
            </li-->

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_panduan; ?>">
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
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_logout; ?>">
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