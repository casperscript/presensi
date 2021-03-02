<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\pegawai_service;
use app\admin\model\presensi_service;
use system;
use comp;

class shift extends system\Controller {

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
        $data['title'] = 'Shift Kerja';

        $cari = array();
        $data['pil_kel_satker'] = $this->pegawai_service->getPilKelSatker($cari);
        $data['pil_satker'] = array('' => '');
        $this->showView('index', $data);
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $data['dataSatker'] = $this->pegawai_service->getDataSatker($input['kdlokasi']);
            $data['dataShift'] = $this->presensi_service->getTabelShift($input);
//            $data['dataShiftDetail'] = $this->presensi_service->getTabelKrit('tb_shift_detail', array('id_shift' => '2018011423542792'));
            $this->subView('tabel', $data);
        }
    }

    protected function jamkerja() {
        $input = $this->post();
        if ($input) {
            $data['dataShift'] = $this->presensi_service->getDataKrit('tb_shift', array('id_shift' => $input['id_shift']));
            $data['dataShiftDetail'] = $this->presensi_service->getTabelKrit('tb_shift_detail', array('id_shift' => $input['id_shift']), 'startdays');
            $this->subView('jamkerja', $data);
        }
    }

    protected function formInputShift() {
        $input = $this->post(true);
        if ($input) {
            $data['id_satker'] = $input['id_satker'];
            $data['pil_unitShift'] = $this->presensi_service->getPilUnitShift();
            $data['dataTabel'] = $this->presensi_service->getDataShift($input['id_shift']);
            $this->subView('formInputShift', $data);
        }
    }

    protected function formEditShift() {
        $input = $this->post(true);
        if ($input) {
            $data['dataTabel'] = $this->presensi_service->getDataKrit('tb_shift', $input);
//            comp\FUNC::showPre($data);
            $this->subView('formEditShift', $data);
        }
    }

    protected function formInputJamKerja() {
        $input = $this->post(true);
        if ($input) {
            $data['id_shift_detail'] = $input['id_shift_detail'];
            $data['dataTabel'] = $this->presensi_service->getDataKrit('tb_shift_detail', $input);
            $dt['opsi'] = array('key' => 'id_jam_kerja', 'value' => 'nama_jam_kerja');
            $data['pil_jamKerja'] = $this->presensi_service->getPilKrit('tb_jam_kerja', array(), $dt['opsi']);
            $this->subView('formInputJamKerja', $data);
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

    protected function simpan() {
        $input = $this->post(true);
        if ($input['op'] == 'addShift') {
            $data = $this->presensi_service->getDataKrit('tb_shift', array('id_shift' => $input['id_shift']));
            foreach ($data as $key => $value) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }
            $data['id_shift'] = (!empty($data['id_shift'])) ? $data['id_shift'] : date('YmdHis') . rand(99, 10);
            $data['ip'] = comp\FUNC::getUserIp();
            $data['author'] = $this->login['username'];
            $result = $this->presensi_service->save_update('tb_shift', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            if ($result['error']) {
                $AI_Shift = $this->presensi_service->getDataKrit('tb_shift', $data, array('id_shift'));
                $unit = ($data['unit_shift'] == 'mingguan') ? 7 : 1;
                $tsiklus = $unit * $data['siklus_shift'];

                $arrShiftDetail = $this->presensi_service->getTabel('tb_shift_detail');
                $arrShiftDetail['id_shift_detail'] = null;
                $arrShiftDetail['id_shift'] = $AI_Shift['id_shift'];
                $arrShiftDetail['id_jam_kerja'] = 0;
                $arrShiftDetail['author'] = $this->login['username'];
                $arrShiftDetail['ip'] = comp\FUNC::getUserIp();

                for ($a = 0; $a < $tsiklus; $a++) {
                    $arrShiftDetail['startdays'] = $a;
                    $arrShiftDetail['enddays'] = $a;
                    $pesan[] = $this->presensi_service->save('tb_shift_detail', $arrShiftDetail);
                }
                $error_msg = array('status' => 'success', 'message' => 'Data berhasil disimpan', 'data' => $pesan);
            }
            echo json_encode($error_msg);
        } else if ($input['op'] == 'addJamKerja') {
            $dataJamKerja = $this->presensi_service->getDataKrit('tb_jam_kerja', array('id_jam_kerja' => $input['id_jam_kerja']));
            $data = $this->presensi_service->getDataKrit('tb_shift_detail', array('id_shift_detail' => $input['id_shift_detail']));
            $data['id_jam_kerja'] = $dataJamKerja['id_jam_kerja'];
            $data['starttime'] = $dataJamKerja['jam_masuk'];
            $data['endtime'] = $dataJamKerja['jam_pulang'];
            $data['author'] = $this->login['username'];
            $data['ip'] = comp\FUNC::getUserIp();
            $result = $this->presensi_service->update('tb_shift_detail', $data, array('id_shift_detail' => $input['id_shift_detail']));
            $error_msg = array('status' => 'success', 'message' => 'Data berhasil disimpan', 'data' => $result);
            echo json_encode($error_msg);
        } else if ($input['op'] == 'editShift') {
            $data = $this->presensi_service->getDataKrit('tb_shift', array('id_shift' => $input['id_shift']));
            foreach ($data as $key => $value) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }
            $data['id_shift'] = (!empty($data['id_shift'])) ? $data['id_shift'] : date('YmdHis') . rand(99, 10);
            $data['ip'] = comp\FUNC::getUserIp();
            $data['author'] = $this->login['username'];
            $result = $this->presensi_service->save_update('tb_shift', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input['op'] == 'shift') {
            $idKey = array($input['field'] => $input['id']);
            $result = $this->presensi_service->delete('tb_shift', $idKey);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
        }
    }

    protected function modalInput() {
        $data['title'] = '<!-- Modal -->';
        $this->subView('modalInput', $data);
    }

    protected function script() {
        $this->subView('script', array());
    }

}
