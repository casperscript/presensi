<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\compare_service;
use system;
use comp;

class laporanselisih extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain;
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->compare_service = new compare_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }
    
    protected function index() {
        $data['title'] = 'Verifikasi Laporan';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Verifikasi Laporan</a>';
        
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $data['listBulan'] = comp\FUNC::$namabulan1;
        $this->showView('index', $data, 'theme_admin');
    }
    
    protected function tabeltpp() {
        $input = $this->post(true);
        if ($input) {
            comp\FUNC::showPre($input);
        }
    }

    protected function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
