<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\pegawai_service;
use app\adminsistem\model\presensi_service;
use system;
use comp;

class lapkehadiran extends system\Controller {

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
        $data['title'] = 'Laporan Presensi';
        
        // nav index
        $data['pil_kel_satker'] = $this->pegawai_service->getPilKelSatker();
        $data['pil_satker'] = array('' => '');
        
        // nav tabel
        $data['sdate'] = date('1 F, Y');
        $data['edate'] = date('t F, Y');
        $data['pil_status'] = array('all' => 'Semua', 'pns' => 'PNS', 'non' => 'Non PNS');
        
        $this->showView('index', $data, 'theme_admin');
    }

    protected function navIndex() {
        $data['title'] = '<!-- Navigasi Utama / Index -->';
        $data['pil_kel_satker'] = $this->pegawai_service->getPilKelSatker();
        $data['pil_satker'] = array('' => '');
        $this->subView('navindex', $data);
    }

    protected function navTabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = '<!-- Navigasi Tabel -->';
            $data['sdate'] = date('1 F, Y');
            $data['edate'] = date('t F, Y');
            $data['pil_status'] = array('all' => 'Semua', 'pns' => 'PNS', 'non' => 'Non PNS');
            $data['kdlokasi'] = $input['kdlokasi'];
            $this->subView('navtabel', $data);
        }
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = '';
            $data['kdlokasi'] = $input['kdlokasi'];
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
            $result = $this->pegawai_service->getPilLokasi($input);
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public function getDetailPersonil() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->pegawai_service->getDataPersonil($input['pin_absen']);
            header('Content-Type: application/json');
            echo json_encode($data);
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

    public function pdfRegister() {
        $input = $this->post(true);
        if ($input) {
            $data['sdate'] = date('Y-m-d', strtotime($input['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($input['edate']));
            $data['kdlokasi'] = $input['kdlokasi'];
            $data['pin_absen'] = $input['id'];
            $this->setSession('TEMP_PDF', $data);
        }
    }

    public function pdfKehadiran() {
        $input = $this->getSession('TEMP_PDF');
        if ($input) {
            $data['sdate'] = $input['sdate'];
            $data['edate'] = $input['edate'];
            $data['dataUser'] = $this->pegawai_service->getArrayPersonal($input['pin_absen']);
            $data['dataSatker'] = $this->pegawai_service->getDataSatker($input['kdlokasi']);
            $data['dataHadir'] = $this->presensi_service->getArrayRecord($input);

            $filename = $input['kdlokasi'] . date('YmdHis');
            $data['filename_f'] = $this->dir_arsip . $filename . '.pdf';
            $data['filename_f_id'] = $filename;
            $data['filename_d'] = 'Lap_Kehadiran (' . $input['sdate'] . ' s.d ' . $input['edate'] . ') ' . date('His') . '.pdf';

            $data_simpan = $this->presensi_service->getTabel('tb_arsip');
            $data_simpan['id'] = $filename;
            $data_simpan['jenis'] = 'kehadiran';
            $data_simpan['kdlokasi'] = $input['kdlokasi'];
            $data_simpan['filename'] = $filename . '.pdf';
            $data_simpan['dateAdd'] = date('Y-m-d H:i:s');
            $data_simpan['author'] = $this->login['username'];
            $data_simpan['ip'] = comp\FUNC::getUserIp();

            $this->presensi_service->save('tb_arsip', $data_simpan);
            $this->subView('pdfKehadiran', $data);
        }
    }

    public function pdfDisiplin() {     // belum dibuat
        $input = $this->getSession('TEMP_PDF');
        if ($input) {
            $data['sdate'] = $input['sdate'];
            $data['edate'] = $input['edate'];
            $data['dataUser'] = $this->pegawai_service->getArrayPersonal($input['pin_absen']);
            $data['dataSatker'] = $this->pegawai_service->getDataSatker($input['kdlokasi']);
            $data['dataHadir'] = $this->presensi_service->getArrayRecord($input);

            $filename = $input['kdlokasi'] . date('YmdHis');
            $data['filename_f'] = $this->dir_arsip . $filename . '.pdf';
            $data['filename_f_id'] = $filename;
            $data['filename_d'] = 'Lap_Kehadiran (' . $input['sdate'] . ' s.d ' . $input['edate'] . ') ' . date('His') . '.pdf';

            $data_simpan = $this->presensi_service->getTabel('tb_arsip');
            $data_simpan['id'] = $filename;
            $data_simpan['jenis'] = 'kehadiran';
            $data_simpan['kdlokasi'] = $input['kdlokasi'];
            $data_simpan['filename'] = $filename . '.pdf';
            $data_simpan['dateAdd'] = date('Y-m-d H:i:s');
            $data_simpan['author'] = $this->login['username'];
            $data_simpan['ip'] = comp\FUNC::getUserIp();

            $this->presensi_service->save('tb_arsip', $data_simpan);
            $this->subView('pdfKehadiran', $data);
        }
    }

    protected function script() {
        $this->subView('script', array());
    }

}
