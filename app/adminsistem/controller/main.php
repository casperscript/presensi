<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\HusnanWModerasiModel; // added by husnanw
use app\adminsistem\model\pegawai_service;
use system;

class main extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->husnanWModel = new HusnanWModerasiModel(); // added by husnanw
        $this->pegawai_service = new pegawai_service();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'BERANDA';
        $data['subtitle'] = '';
        $data['breadcrumb'] = '';
        $data['session'] = $this->login;
        $this->showView('index', $data, 'theme_admin');
    }

    protected function loading() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('loading', $data);
    }
    
    protected function header() {
        $data['title'] = '<!-- Header -->';
        $data['user'] = 'Administrator';
        $data['status'] = 'Web Developer';
        $data['since'] = 'Member since Nov. 2012';
        $data['link_logout'] = $this->link('adminsistem/logout/');
        $this->subView('header', $data);
    }
    
    protected function menu() {
        $data['title'] = '<!-- Menu -->';
        $data['user'] = 'Administrator';
        $data['subtitle'] = 'MENU UTAMA';
        $data['link_beranda'] = $this->link('adminsistem/');
        $data['link_pegawai'] = $this->link('adminsistem/pegawai/');
        $data['link_tunjangan'] = $this->link('adminsistem/tunjangan/');
        $data['link_pottunjangan'] = $this->link('adminsistem/pottunjangan/');
        
        // MENU MASTER
        $data['link_master_moderasi'] = $this->link('adminsistem/mastermoderasi/');
        $data['link_master_cuti'] = $this->link('adminsistem/mastercuti/');
        $data['link_master_dinas'] = $this->link('adminsistem/masterdinas/');
        $data['link_master_gruppengguna'] = $this->link('adminsistem/mastergruppengguna/');
        $data['link_master_pengguna'] = $this->link('adminsistem/masterpengguna/');
        $data['link_master_jam_kerja'] = $this->link('adminsistem/masterjamkerja/');
        $data['link_master_aturan'] = $this->link('adminsistem/masteraturan/');
        $data['link_master_shift'] = $this->link('adminsistem/mastershift/');
        $data['link_master_libur'] = $this->link('adminsistem/masterlibur/');
        $data['link_master_potongan_pajak'] = $this->link('adminsistem/masterpotonganpajak/');
        $data['link_master_mesin'] = $this->link('adminsistem/mastermesin/');
        $data['link_master_panduan'] = $this->link('adminsistem/masterpanduan/');
        $data['link_master_kode_presensi'] = $this->link('adminsistem/masterkodepresensi/');
        $data['link_master_tpp'] = $this->link('adminsistem/mastertpp/');
        $data['link_master_teks'] = $this->link('adminsistem/masterteks/');
        
        // MENU PENGATURAN
        $data['link_pengaturan_profil'] = $this->link('adminsistem/pengaturanprofil/');
        
        $data['link_datamesin'] = $this->link('adminsistem/datamesin/');
		$data['link_usermesin'] = $this->link('adminsistem/usermesin');
        $data['link_updatemesin'] = $this->link('adminsistem/updatemesin/');
        $data['link_jadwal'] = $this->link('adminsistem/jadwal/');
		$data['link_manketidakhadiran'] = $this->link('adminsistem/modmanketidakhadiran/');
		
		// -- start husnanw moderasi menu -- //
        $data['link_husnanw_daftar_ver_mod_proses'] = $this->link('adminsistem/HusnanWModerasi/daftarVerMod');
        $data['link_husnanw_daftar_ver_mod_hasil'] = $this->link('adminsistem/HusnanWModerasi/daftarVerModHasil');
        $data['link_husnanw_moderasi'] = $this->link('adminsistem/HusnanWModerasi/');
        // -- end husnanw moderasi menu -- //
		
        //$data["selfId"] = $this->husnanWModel->getCurrentPns($this->login["nipbaru"]); // added by husnanw
        /*--- BEGIN - added by daniek ---*/ 
        $lokasi = $this->pegawai_service->getDataSatker($this->login["kdlokasi"]);
        $data["selfId"]["singkatan_lokasi"] = $lokasi["singkatan_lokasi"];
        $jabatan = $this->login["jabatan_pengguna"] ? $this->login["jabatan_pengguna"] : '';
        $data["selfId"]["jabatan_pengguna"] = $jabatan;
        /*--- END - added by daniek ---*/

        $data['link_jadwal'] = $this->link('adminsistem/jadwal/');
        $data['link_jadwalkerja'] = $this->link('adminsistem/jadwalkerja/');
        $data['link_shift'] = $this->link('adminsistem/shift/');
        $data['link_jamkerja'] = $this->link('adminsistem/jamkerja/');
		
		$data['link_lap_kehadiran'] = $this->link('adminsistem/lapkehadiran/');
        $data['link_lap_disiplin'] = $this->link('adminsistem/lapdisiplin/');
        $data['link_lap_apel'] = $this->link('adminsistem/lapapel/');

        $data['link_laporan'] = $this->link('adminsistem/laporan/');
        $data['link_laporan_terverifikasi'] = $this->link('adminsistem/laporan/verified');

        $data['link_backuplaporan'] = $this->link('adminsistem/backuplaporan');

        $data['link_jamapelpagi'] = $this->link('adminsistem/jamapelpagi/');
        $data['link_apelpagi'] = $this->link('adminsistem/apelpagi/');
        $data['link_batalapelpagi'] = $this->link('adminsistem/batalapelpagi/');
        $data['link_logout'] = $this->link('adminsistem/logout/');
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
