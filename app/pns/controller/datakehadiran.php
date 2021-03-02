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
        $data['dataPersonal'] = $this->pegawai_service->getDataPersonil(['nipbaru' => $this->login['nipbaru']]);
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

    protected function form() {
        $input = $this->post(true);
        if ($input) {
            $data['dataForm'] = $this->presensi_service->getDataKrit('tb_log_presensi', $input);
            $this->subView('form', $data);
        }
    }
    
    protected function simpan () {
        $input = $this->post(true);
        if ($input) {
            $data = $this->presensi_service->getDataKrit('tb_log_presensi', $input);
            if (!empty($data['pin_absen'])) {
                $data['status_log_presensi'] = $input['status_presensi'];
                $result = $this->presensi_service->save_update('tb_log_presensi', $data);
                return $result;
            } else {
                return 'data gagal';
            }
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
