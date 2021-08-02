<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\HusnanWModerasiModel; // added by husnanw
use app\adminopd\model\pegawai_service;
use app\adminopd\model\laporan_service;
use app\adminopd\model\servicemasterpresensi;
use system;
use comp;

class main extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->servicemaster = new servicemasterpresensi();
        $this->husnanWModel = new HusnanWModerasiModel();  // added by husnanw
        $this->pegawai_service = new pegawai_service();
        $this->laporan_service = new laporan_service();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('adminopd/login'));
        }
    }

    protected function index() {
        $data['title'] = 'BERANDA';
        $data['subtitle'] = '';
        $data['breadcrumb'] = '';
        $data['teks'] = $this->servicemaster->getTeksBeranda();
        $this->showView('index', $data, 'theme_admin');
    }

    protected function loading() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('loading', $data);
    }

    protected function header() {
        $data['title'] = '<!-- Header -->';
        $data['user'] = 'Admin OPD';
        $data['status'] = 'Web Developer';
        $data['since'] = 'Member since Nov. 2012';
        $data['link_logout'] = $this->link('adminopd/logout/');
        $this->subView('header', $data);
    }

    protected function menu() {
        $data['title'] = '<!-- Menu -->';
        $data['user'] = 'Admin OPD';
        $data['subtitle'] = 'MENU UTAMA';
        $data['kdlokasi'] = $this->getSession('SESSION_LOGIN')['kdlokasi'];
        
        $active = $this->getController();
        $data['menu'] = $this->servicemain->getMenu($active);
//        $data['controller'] = $controller;
//        comp\FUNC::showPre($data['menu']);exit;
//        comp\FUNC::showPre($active);exit;

        $data['link_beranda'] = $this->link('adminopd/');
        $data['link_manketidakhadiran'] = $this->link('adminopd/modmanketidakhadiran/');
        $data['link_datakehadiran'] = $this->link('adminopd/datakehadiran/');
        $data['link_moderasi'] = $this->link('adminopd/moderasi/');
        $data['link_daftar_mod_proses'] = $this->link('adminopd/moderasi/daftarVerMod/');
        $data['link_daftar_mod_hasil'] = $this->link('adminopd/moderasi/daftarVerModHasil/');
        $data['link_pengaturan_profil'] = $this->link('adminopd/pengaturanprofil/');
        $data['link_pengaturan_pengguna'] = $this->link('adminopd/pengaturanpengguna/');
        $data['link_panduan'] = $this->link('adminopd/panduan/');
        $data['link_apelpagi'] = $this->link('adminopd/apelpagi/');
        $data['link_batalapelpagi'] = $this->link('adminopd/batalapelpagi/');
        $data['link_laporan'] = $this->link('adminopd/laporan/');
        $data['link_laporan_tpp'] = $this->link('adminopd/laporan/tpp');
        $data['link_laporan_individu'] = $this->link('adminopd/laporan/individu');
        $data['link_laporan_final'] = $this->link('adminopd/laporan/laporanfinal');
        $data['link_laporan_cetak'] = $this->link('adminopd/laporan/cetak');
        $data['link_laporan_tpp_13'] = $this->link('adminopd/laporan/tpp13');
        $data['link_laporan_tpp_14'] = $this->link('adminopd/laporan/tpp14');
        $data['link_laporan_cetak_des19'] = $this->link('adminopd/laporandes19/cetak');
        $data['link_laporan_tpp_des19'] = $this->link('adminopd/laporandes19/tpp');
        $data['link_backuplaporan'] = $this->link('adminopd/backuplaporan');
        $data['link_jadwalkerja'] = $this->link('adminopd/jadwalkerja');
        $data['link_shift'] = $this->link('adminopd/shift');
        $data['link_logout'] = $this->link('adminopd/logout/');
        $data['link_tpp_cetak'] = $this->link('adminopd/tpp/cetak');
        $data['link_tpp_presensi_cetak'] = $this->link('adminopd/tpp/presensi');

        $data["selfId"] = $this->husnanWModel->getCurrentPns($this->login["nipbaru"]); // added by husnanw
        /* --- BEGIN - added by daniek --- */
        $lokasi = $this->pegawai_service->getDataSatker($this->login["kdlokasi"]);
        $data["selfId"]["singkatan_lokasi"] = $lokasi["singkatan_lokasi"];
        $jabatan = $this->login["jabatan_pengguna"] ? $this->login["jabatan_pengguna"] : '';
        $data["selfId"]["jabatan_pengguna"] = $jabatan;
        /* --- END - added by daniek --- */



        $data['menu_tpp'] = $this->laporan_service->getTppMenu();
        $this->subView('menu', $data);
    }

    protected function modalInput() {
        $data['title'] = '<!-- Modal -->';
        $this->subView('modalInput', $data);
    }

    protected function spinner() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('spinner', $data);
    }

    protected function footer() {
        $data['title'] = '<!-- Footer -->';
        $this->subView('footer', $data);
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
