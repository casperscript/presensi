<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\pegawai_service_moderasi;
use app\admin\model\presensi_service_moderasi;
use system;
use comp;

class modmanketidakhadiran extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->pegawai_service = new pegawai_service_moderasi();
            $this->presensi_service = new presensi_service_moderasi();
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
            
        }
    }

    protected function index() {
        $data['title'] = 'Manajemen Ketidakhadiran';
        $cari = array();
        $data['pil_kel_satker'] = $this->pegawai_service->getPilKelSatker($cari);
        $data['pil_satker'] = array('' => '');
        $this->showView('index', $data, 'theme_admin');
    }
    
    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = '';
            $data['dataTabel'] = $this->pegawai_service->getTabelPersonil($input);
            $this->subView('tabel', $data);
        }
    }

    protected function detail() {
        $input = $this->post(true);
        if ($input) {
            $input['sdate'] = date('Y-11-1');
            $input['edate'] = date('Y-m-t');
            $data['title'] = '';
            $data['tanggal'] = array('sdate' => $input['sdate'], 'edate' => $input['edate']);
            $data['dataPersonal'] = $this->pegawai_service->getDataPersonil($input['pin_absen']);

            $this->subView('detail', $data);
        }
    }

    protected function record() {
        $input = $this->post(true);
        if ($input) {
            $data['tanggal'] = array('sdate' => $input['sdate'], 'edate' => $input['edate']);
            $data['dataPersonal'] = $this->pegawai_service->getDataPersonil($input['pin_absen']);

            $this->subView('detail', $data);
        }
    }

    public function getPilLokasiFromKelLokasi() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->pegawai_service->getData('SELECT kdlokasi, singkatan_lokasi FROM tref_lokasi_kerja WHERE (kd_kelompok_lokasi_kerja = ?)', array($input['id_kelompok']));
            if ($data['count'] > 0) {
                foreach ($data['value'] as $val) {
                    $valData[$val['kdlokasi']] = $val['singkatan_lokasi'];
                }
            } else {
                $valData[] = '';
            }
            header('Content-Type: application/json');
            echo json_encode($valData);
        }
    }
    
    public function getDetailPersonil() {
        $input = $this->post(true);
        if ($input) {
            $data['dataTabel'] = $this->presensi_service->getDataKetidakhadiran($input['pin_absen']);
            $data['dataPersonal'] = $this->pegawai_service->getDataPersonil($input['pin_absen']);
            $this->subView('record', $data);
            
        }
    }

    public function getDetailRecord() {
        $input = $this->post(true);
        if ($input) {
            $input['sdate'] = date('Y-m-d', strtotime($input['sdate']));
            $input['edate'] = date('Y-m-d', strtotime($input['edate']));
            $data['pin_absen'] = $input['pin_absen'];
            $data['dataTabel'] = $this->presensi_service->getTabelRecordPersonil($input);
            $this->subView('record', $data);
        }
    }

    protected function script() {
        $this->subView('script', array());
    }

}
