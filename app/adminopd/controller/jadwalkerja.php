<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\pegawai_service;
use app\adminopd\model\presensi_service;
use system;
use comp;

class jadwalkerja extends system\Controller {

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
        $data['title'] = 'Jadwal Kerja';
        $this->showView('index', $data);
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $data['dataPersonil'] = $this->pegawai_service->getTabelPersonil($input);
            $data['dataSatker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            $data['dataJadwal'] = $this->presensi_service->getArrPersonilJadwal($data['dataPersonil']['dataTabel']);
            $this->subView('tabel', $data);
        }
    }

    protected function detail() {
        $input = $this->post(true);
        if ($input) {
            $input['batas'] = '1000';
            $data['dataPersonil'] = $this->pegawai_service->getDataKrit('view_presensi_personal', $input);
            $data['tabelJadwal'] = $this->presensi_service->getTabelKrit('tb_jadwal', $input, 'sdate');
            $data['dataInfoSatker'] = $this->pegawai_service->getPilKrit('tref_lokasi_kerja', $data['dataPersonil'], array('key' => 'kdlokasi', 'value' => 'nmlokasi'));
            $data['dataShift'] = $this->presensi_service->getPilKrit('tb_shift', ['pin_absen' => $data['dataPersonil']['pin_absen']], array('key' => 'id_shift', 'value' => 'nama_shift'));
            $data['author'] = $this->presensi_service->getListAuthor($data['tabelJadwal']['dataTabel']);
            $this->subView('detail', $data);
        }
    }
    
    protected function tabelJadwal() {
        $input = $this->post(true);
        if ($input) {
            comp\FUNC::showPre($input);
        }
    }

    protected function form() {
        $input = $this->post(true);
        if ($input) {
            $data['dataPersonil'] = $this->pegawai_service->getDataPersonil($input['pin_absen']);
            $data['dataJadwal'] = $this->presensi_service->getDataKrit('tb_jadwal', $input);
            $data['pilShift'] = $this->presensi_service->getPilShift($input);
//            comp\FUNC::showPre($data);
            $this->subView('form', $data);
        }
    }

    protected function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->presensi_service->getDataKrit('tb_jadwal', $input);
            $dataShift = $this->presensi_service->getDataKrit('tb_shift', ['id_shift' => $input['id_shift']]);
            $tabelShiftDetail = $this->presensi_service->getTabelShiftDetail($input['id_shift']);
            foreach ($data as $key => $value) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }
            $data['id_jadwal'] = (empty($input['id_jadwal'])) ? date('YmdHis') . rand(11, 99) : $data['id_jadwal'];
            $data['author'] = $this->login['username'];
            $data['dateAdd'] = date('Y-m-d H:i:s');

            // Check start and end date
            $checkCrashJadwal = $this->presensi_service->checkCrashJadwal($input);
//                comp\FUNC::showPre($checkCrashJadwal);exit;

            if (strtotime($input['sdate']) > strtotime($input['edate'])) {
                $error_msg = array('title' => 'Gagal', 'message' => 'Tanggal akhir harus lebih besar dari tanggal awal', 'status' => 'error');
            } else if ($checkCrashJadwal['jumlah'] > 0) {
                $error_msg = array('title' => 'Gagal', 'message' => 'Tanggal awal atau akhir sudah dipakai. Cek rentang wktu pada jadwal yang sudah dibuat', 'status' => 'error');
            } else {
                $result = $this->presensi_service->save_update('tb_jadwal', $data);
                $error_msg = ($result['error']) ?
                        array('title' => 'Berhasil', 'status' => 'success', 'message' => 'Data berhasil disimpan') : array('title' => 'Gagal', 'status' => 'error', 'message' => 'Data gagal disimpan');

                // Get Id Jadwal
                $getNewJadwal = $this->presensi_service->getDataKrit('tb_jadwal', $data);

                /*                 * *************** */
                $cyle = ($dataShift['unit_shift'] == 'mingguan') ? 7 * $dataShift['siklus_shift'] : $dataShift['siklus_shift'];
                $siklus = ((strtotime($data['sdate']) - strtotime($dataShift['tanggal_mulai_shift'])) / 86400) % $cyle;
                for ($a = strtotime($data['sdate']); $a <= strtotime($data['edate']); $a = $a + 86400) {
                    $tanggal = date('Y-m-d', $a);
                    $vid_days = $siklus % $cyle;

                    $data2[$tanggal]['id_jadwal'] = $getNewJadwal['id_jadwal'];
                    $data2[$tanggal]['tanggal'] = $tanggal;
                    $data2[$tanggal]['id_jam_kerja'] = $tabelShiftDetail[$vid_days]['id_jam_kerja'];
                    $data2[$tanggal]['masuk'] = $tabelShiftDetail[$vid_days]['stime'];
                    $data2[$tanggal]['pulang'] = $tabelShiftDetail[$vid_days]['etime'];
                    $data2[$tanggal]['author'] = $getNewJadwal['author'];
                    $data2[$tanggal]['dateAdd'] = date('Y-m-d H:i:s');
                    $siklus = $siklus + 1;

                    // Insert database
                    $this->presensi_service->save_update('tb_jadwal_detail', $data2[$tanggal]);
                }
                /*                 * *************** */
            }
//            comp\FUNC::showPre($checkCrashJadwal);
            echo json_encode($error_msg);
        }
    }

    public function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array($input['field'] => $input['id']);
            $result = $this->presensi_service->delete('tb_jadwal', $idKey);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
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

    protected function modalInput() {
        $data['title'] = '<!-- Modal -->';
        $this->subView('modalInput', $data);
    }

    protected function script() {
        $this->subView('script', array());
    }

}
