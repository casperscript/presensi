<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use app\pns\model\servicemasterpresensi;
use app\pns\model\HusnanWModerasiModel; // added by husnanw
use system;

class main extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->servicemaster = new servicemasterpresensi();
        $this->husnanWModel = new HusnanWModerasiModel();  // added by husnanw
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('pns/login'));
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
        $data['user'] = 'Administrator';
        $data['status'] = 'Web Developer';
        $data['since'] = 'Member since Nov. 2012';
        $data['link_logout'] = $this->link('pns/logout/');
        $this->subView('header', $data);
    }

    protected function menu() {
        $data['title'] = '<!-- Menu -->';
        $data['user'] = 'Administrator';
        $data['subtitle'] = 'MENU UTAMA';
        $data['link_beranda'] = $this->link('pns/');
        $data['link_manketidakhadiran'] = $this->link('pns/modmanketidakhadiran/');

        // -- start husnanw moderasi menu -- //
        $data['link_husnanw_moderasi'] = $this->link('pns/HusnanWModerasi/');
        $data['link_husnanw_daftar_mod_proses'] = $this->link
                ('pns/HusnanWModerasi/daftarMod/');
        $data['link_husnanw_daftar_mod_hasil'] = $this->link
                ('pns/HusnanWModerasi/daftarModHasil/');
        // -- end husnanw moderasi menu -- //
        // MENU PENGATURAN
        $data['link_pengaturan_profil'] = $this->link('pns/pengaturanprofil/');

        // MENU PANDUAN
        $data['link_panduan'] = $this->link('pns/panduan/');

        $data["selfId"] = $this->husnanWModel->getCurrentPns($this->login["nipbaru"]); // added by husnanw
        $data['link_apelpagi'] = $this->link('pns/apelpagi/');
        $data['link_laporan'] = $this->link('pns/laporan/');

        $data['link_datakehadiran'] = $this->link($this->getProject() . 'datakehadiran/');
        $data['link_moderasi'] = $this->link($this->getProject() . 'moderasi/');

        $data['link_logout'] = $this->link('pns/logout/');
        $this->subView('menu', $data);
    }

    protected function modalInput() {
        $data['title'] = '<!-- Modal -->';
        $this->subView('modalInput', $data);
    }

    protected function footer() {
        $data['title'] = '<!-- Footer -->';
        $this->subView('footer', $data);
    }

    protected function spinner() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('spinner', $data);
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
