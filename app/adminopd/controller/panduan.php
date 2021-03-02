<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\servicemasterpresensi;
use system;

class panduan extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemasterpresensi = new servicemasterpresensi();
        $this->servicemain = new servicemain();
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
        $data['title'] = 'Panduan';
        $data['breadcrumb'] = '<a href="'.$this->link().'pns" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Panduan</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelPanduan($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data['link_file'] = $this->link('upload/panduan/');
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
