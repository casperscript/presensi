<?php

namespace app\kepalabkppd\controller;

use app\kepalabkppd\model\servicemain;
use app\kepalabkppd\model\apelpagi_service;
use system;

class batalapelpagi extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->apelpagi_service = new apelpagi_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Pembatalan Apel Pagi';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pembatalan Apel Pagi</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->apelpagi_service->getTabelBatalApel($input);

            if ($dataTabel['jmlData'] > 0) {
                $p = '';
                if (isset($dataTabel['dataTabel'])) {
                    $p = array_map(function ($i) {
                        return $i['pin_absen'];
                    }, $dataTabel['dataTabel']);
                    
                    $p = array_unique($p);
                    $p = implode(',', $p);
                }
                $data['personil'] = $this->apelpagi_service->getdataPersonilBatch($p);
            }
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}