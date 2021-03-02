<?php

namespace app\anggaran\controller;

use app\anggaran\model\servicemain;
use app\anggaran\model\pegawai_service;
use app\anggaran\model\servicemasterpresensi;
use system;
use comp;

class main extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->servicemaster = new servicemasterpresensi();
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
        $data["selfId"]['nama_lengkap'] = '';
        $lokasi = $this->pegawai_service->getDataSatker($this->login["kdlokasi"]);
        $data["selfId"]["singkatan_lokasi"] = $lokasi["singkatan_lokasi"];

        $data['link_beranda'] = $this->link();
        $data['link_pengaturan_profil'] = $this->link('pengaturanprofil/');
        $data['link_laporan'] = $this->link($this->getProject() . 'laporan/');
        $data['link_rekap'] = $this->link('rekap/');

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
