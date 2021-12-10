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
                <img src="<?= (isset($selfId['foto'])) ? $this->simpeg_url . "/" . $selfId["foto"] : 'assets/images/profile-image.png' ?>" class="circle" alt="fotoku" style="width:100px; height:100px">
            </div>
            <div class="sidebar-profile-info">
                <a class="pointer">
                    <p><?= (isset($selfId["nama_lengkap"])) ? $selfId['nama_lengkap'] : 'Nama Tidak Terdaftar' ?></p>
                    <span style="text-transform: lowercase"><?= $selfId['jabatan_pengguna'] ?> ADMINOPD@<?= $selfId["singkatan_lokasi"] ?></span>
                </a>
            </div>
        </div>

        <!-- Begin::Menu dinamis ->
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <?php foreach ($menu as $header) : ?>
                <?php if ($header['tipe'] == 'link') : ?>
                    <li class="no-padding active <?php // ($header['active'] == 1) ? 'active' : ''  
                                                    ?>">
                        <a class="collapsible-header waves-effect waves-grey" href="<?= $this->link($this->getProject() . $header['path']); ?>">
                            <i class="material-icons"><?= $header['icon'] ?></i><?= $header['nama'] ?>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="no-padding <?= ($header['active'] == true) ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey">
                            <i class="material-icons"><?= $header['icon'] ?></i><?= $header['nama'] . $header['active'] ?><i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                        </a>
                        <?php if (count($header['sub']) > 0) : ?>
                            <div class="collapsible-body">
                                <?php foreach ($header['sub'] as $sub) : ?>
                                    <ul>
                                        <li><a <?= isset($sub['active']) ? 'class="active-page"' : '' ?> href="<?= $this->link($this->getProject() . $sub['path']) ?>"><?= $sub['nama'] ?></a></li>
                                    </ul>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <!-- End::Menu dinamis -->

        <!-- Begin::Menu statis -->
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
                        <li><a href="<?= $link_datakehadiran ?>">Data Kehadiran</a></li>
                    </ul>
                </div>
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
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">star</i>Moderasi<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul id="ulModerasi">
                        <li><a href="<?= $link_moderasi ?>"><i class="material-icons">star</i>Pengajuan Moderasi</a></li>
                        <li><a href="<?= $link_daftar_mod_proses ?>"><i class="material-icons">star</i>Daftar Moderasi</a></li>
                    </ul>
                </div>
            </li>

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">library_books</i>Laporan<i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?= $link_laporan ?>">Verifikasi</a></li>
                        <li><a href="<?= $link_laporan_cetak ?>">Cetak Laporan</a></li>
                        <li><a href="<?= $link_laporan_tpp ?>">Penerimaan TPP</a></li>
                    </ul>
                </div>
            </li>

            <?php foreach ($menu_tpp as $kd_tpp => $tpp) { ?>
                <li class="no-padding">
                    <a class="collapsible-header waves-effect waves-grey pink-text">
                        <i class="material-icons pink-text">feedback</i><b><?= $tpp['label'] ?></b><i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                    </a>
                    <div class="collapsible-body">
                        <ul>
                            <?php if ($tpp['periode'] != 'full') { ?>
                                <li><a href="<?= $link_tpp_presensi_cetak ?>/<?= $kd_tpp ?>">Cetak Laporan</a></li>
                            <?php } ?>
                            <li><a href="<?= $link_tpp_cetak ?>/<?= $kd_tpp ?>">Cetak TPP</a></li>
                        </ul>
                    </div>
                </li>
            <?php } ?>

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
                        <li><a href="<?= $link_pengaturan_pengguna; ?>">Pengguna</a></li>
                        <li><a href="<?= $link_jadwalkerja ?>">Jadwal Karyawan</a></li>
                        <li><a href="<?= $link_shift ?>">Shift Kerja</a></li>
                    </ul>
                </div>
            </li>

            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey" href="<?= $link_logout; ?>">
                    <i class="material-icons">exit_to_app</i>Keluar
                </a>
            </li>

        </ul>
        <!-- End::Menu statis -->

        <div class="footer">
            <div class="row no-s">
                <div class="col s4">
                    <img src="assets/images/rsz_kota_pekalongan.png" width="100%" height="100%">
                </div>
                <div class="col s8">
                    <a>Pemerintah<br>Kota Pekalongan</a>
                </div>
            </div>
        </div>
    </div>
</aside>
<script>
    //    $(document).ready(function () {
    //        $('.sidenav').sidenav();
    //    });
</script>