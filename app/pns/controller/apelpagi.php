<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use app\pns\model\HusnanWModerasiModel;
use app\pns\model\apelpagi_service;
use system;

class apelpagi extends system\Controller {
    protected $pinAbsen = null;

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->apelpagi_service = new apelpagi_service();
        $this->servicemaster = new HusnanWModerasiModel();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $this->pinAbsen = $this->servicemaster->getPinAbsen($this->login["nipbaru"]);
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('pns/login'));
        }
    }

    protected function index() {
        $data['title'] = 'Apel Pagi';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Apel Pagi</a>';
        $data['personil'] = $this->apelpagi_service->getDataPersonil($this->pinAbsen);
        $this->showView('index', $data, 'theme_admin');
    }

    public function tabel() {
        $input = $this->post(true);
        if ($input) {
            $input['sdate'] = $input['sdate_submit'];
            $input['edate'] = $input['edate_submit'];
            $data['pin_absen'] = $input['pin_absen'];
            $data['dataTabel'] = $this->apelpagi_service->getTabelRecordPersonil($input);
            $data['jadwal'] = $this->apelpagi_service->getArrayJam();

            $data['default'] = '07.15.01 - 08.00';
            $default = $this->apelpagi_service->getData('SELECT * FROM tb_jam_apel WHERE is_default=1', []);
            if ($default['count'] > 0)
                $data['default'] = $default['value'][0]['mulai_apel'] . ' - ' . $default['value'][0]['akhir_apel'];
            $this->subView('tabel', $data);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}
?>