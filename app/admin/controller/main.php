<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\HusnanWModerasiModel; // added by husnanw
use app\admin\model\pegawai_service;
use app\admin\model\servicemasterpresensi;
use system;

class main extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->servicemaster = new servicemasterpresensi();
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
        $data['teks'] = $this->servicemaster->getTeks('BERANDA');
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
        $data['link_logout'] = $this->link('logout/');
        $this->subView('header', $data);
    }
    
    protected function menu() {
        $data['title'] = '<!-- Menu -->';
        $data['user'] = 'Administrator';
        $data['subtitle'] = 'MENU UTAMA';
        $data['link_beranda'] = $this->link();
        $data['link_pegawai'] = $this->link('pegawai/');
        $data['link_tunjangan'] = $this->link('tunjangan/');
        $data['link_pottunjangan'] = $this->link('pottunjangan/');
        
        // MENU MASTER
        $data['link_master_moderasi'] = $this->link('mastermoderasi/');
        $data['link_master_cuti'] = $this->link('mastercuti/');
        $data['link_master_dinas'] = $this->link('masterdinas/');
        $data['link_master_gruppengguna'] = $this->link('mastergruppengguna/');
        $data['link_master_pengguna'] = $this->link('masterpengguna/');
        $data['link_master_jam_kerja'] = $this->link('masterjamkerja/');
        $data['link_master_aturan'] = $this->link('masteraturan/');
        $data['link_master_shift'] = $this->link('mastershift/');
        $data['link_master_libur'] = $this->link('masterlibur/');
        $data['link_master_potongan_pajak'] = $this->link('masterpotonganpajak/');
        $data['link_master_mesin'] = $this->link('mastermesin/');
        $data['link_master_panduan'] = $this->link('masterpanduan/');
        $data['link_master_kode_presensi'] = $this->link('masterkodepresensi/');
        
        // MENU PENGATURAN
        $data['link_pengaturan_profil'] = $this->link('pengaturanprofil/');
        
        $data['link_datamesin'] = $this->link('datamesin/');
		$data['link_usermesin'] = $this->link('usermesin');
        $data['link_updatemesin'] = $this->link('updatemesin/');
        $data['link_jadwal'] = $this->link('jadwal/');
		$data['link_manketidakhadiran'] = $this->link('modmanketidakhadiran/');
		
		// -- start husnanw moderasi menu -- //
        $data['link_husnanw_daftar_ver_mod_proses'] = $this->link('HusnanWModerasi/daftarVerMod');
        $data['link_husnanw_daftar_ver_mod_hasil'] = $this->link('HusnanWModerasi/daftarVerModHasil');
        $data['link_husnanw_moderasi'] = $this->link('HusnanWModerasi/');
        // -- end husnanw moderasi menu -- //
		
        $data["selfId"] = $this->husnanWModel->getCurrentPns($this->login["nipbaru"]); // added by husnanw
        /*--- BEGIN - added by daniek ---*/ 
        $lokasi = $this->pegawai_service->getDataSatker($this->login["kdlokasi"]);
        $data["selfId"]["singkatan_lokasi"] = $lokasi["singkatan_lokasi"];
        $jabatan = $this->login["jabatan_pengguna"] ? $this->login["jabatan_pengguna"] : '';
        $data["selfId"]["jabatan_pengguna"] = $jabatan;
        /*--- END - added by daniek ---*/

        $data['link_jadwal'] = $this->link('jadwal/');
        $data['link_jadwalkerja'] = $this->link('jadwalkerja/');
        $data['link_shift'] = $this->link('shift/');
        $data['link_jamkerja'] = $this->link('jamkerja/');
		
		$data['link_lap_kehadiran'] = $this->link('lapkehadiran/');
        $data['link_lap_disiplin'] = $this->link('lapdisiplin/');
        $data['link_lap_apel'] = $this->link('lapapel/');

        $data['link_laporan'] = $this->link('laporan/');
        $data['link_laporan_terverifikasi'] = $this->link('laporan/verified');
        $data['link_batal_verifikasi'] = $this->link('laporan/batalverifikasi');

        $data['link_jamapelpagi'] = $this->link('jamapelpagi/');
        $data['link_apelpagi'] = $this->link('apelpagi/');
        $data['link_batalapelpagi'] = $this->link('batalapelpagi/');
        $data['link_logout'] = $this->link('logout/');
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
