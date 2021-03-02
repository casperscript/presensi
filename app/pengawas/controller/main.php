<?php

namespace app\pengawas\controller;

use app\pengawas\model\servicemain;
use app\pengawas\model\HusnanWModerasiModel; // added by husnanw
use app\pengawas\model\pegawai_service;
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
        $data['link_logout'] = $this->link('pengawas/logout/');
        $this->subView('header', $data);
    }
    
    protected function menu() {
        $data['title'] = '<!-- Menu -->';
        $data['user'] = 'Administrator';
        $data['subtitle'] = 'MENU UTAMA';
        $data['link_beranda'] = $this->link('pengawas/');
        $data['link_pegawai'] = $this->link('pengawas/pegawai/');
        
        // MENU PENGATURAN
        $data['link_pengaturan_profil'] = $this->link('pengawas/pengaturanprofil/');
        
        $data['link_updatemesin'] = $this->link('pengawas/updatemesin/');
        $data['link_datamesin'] = $this->link('pengawas/datamesin/');
		
        $data["selfId"] = $this->husnanWModel->getCurrentPns($this->login["nipbaru"]); // added by husnanw
        /*--- BEGIN - added by daniek ---*/ 
        $lokasi = $this->pegawai_service->getDataSatker($this->login["kdlokasi"]);
        $data["selfId"]["singkatan_lokasi"] = $lokasi["singkatan_lokasi"];
        $jabatan = $this->login["jabatan_pengguna"] ? $this->login["jabatan_pengguna"] : '';
        $data["selfId"]["jabatan_pengguna"] = $jabatan;
        /*--- END - added by daniek ---*/

        $data['link_laporan'] = $this->link('pengawas/laporan/');
        $data['link_laporan_terverifikasi'] = $this->link('pengawas/laporan/verified');

        $data['link_backuplaporan'] = $this->link('pengawas/backuplaporan');

        $data['link_logout'] = $this->link('pengawas/logout/');
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
