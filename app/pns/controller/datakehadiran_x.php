<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use app\pns\model\pegawai_service;
use app\pns\model\presensi_service;
use system;
use comp;

class datakehadiran extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->pegawai_service = new pegawai_service();
            $this->presensi_service = new presensi_service();
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Data Kehadiran Pegawai';
        $data['tanggal'] = array('sdate' => date('Y-m-01'), 'edate' => date('Y-m-t'));
        $data['dataPersonal'] = $this->pegawai_service->getDataPersonil($this->login);
        $this->showView('index', $data, 'theme_admin');
    }
    
    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $arrRecordLap = array('sdate' => $input['sdate'], 'edate' => $input['edate'], 'pin_absen' => array($input['pin_absen'] => $input['pin_absen']));
            $data['dataTabel'] = $this->presensi_service->getTabelRecordPersonil($input);
            $data['dataLaporan'] = $this->presensi_service->getArrayRecord($arrRecordLap);
            $this->subView('tabel', $data);
        }
    }

    protected function spinner() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('spinner', $data);
    }

    protected function script() {
        $this->subView('script', array());
    }

}
