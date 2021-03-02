<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\apelpagi_service;
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
            $this->redirect($this->link('adminopd/login'));
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
        $input['kdlokasi'] = $this->login['kdlokasi'];
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

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $data['form_title'] = (!(empty($input['id_batal_apel']))) ? 'Ubah Data Pembatalan Apel' : 'Tambah Data Pembatalan Apel';
            $satker = $this->apelpagi_service->getPilLokasi();
            $data['satker'] = $satker[$this->login['kdlokasi']];
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['dataTabel'] = $this->apelpagi_service->getDataBatalApelForm($input['id_batal_apel']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = [
                'pin_absen' => $input['pin_absen'],
                'tanggal_apel' => $input['tanggal_apel'],
                'keterangan' => $input['keterangan'],
                'petugas_input' => $this->login['username']
            ];
            $column = ' (pin_absen, tanggal_apel, keterangan, petugas_input)';
            $result = $this->apelpagi_service->save('tb_batal_apel' . $column, $data);

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_batal_apel' => $input['id']);
            $result = $this->apelpagi_service->delete('tb_batal_apel', $idKey);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
        }
    }

    public function getPersonilFromLokasi() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->apelpagi_service->getDataPersonilSatker($input);
            if ($data['count'] > 0) {
                foreach ($data['value'] as $val) {
                    $valData[$val['pin_absen']] = $val['nama_personil'];
                }
            } else {
                $valData[] = '';
            }
            header('Content-Type: application/json');
            echo json_encode($valData);
        }
    }

    public function getDataApel() {
        $input = $this->post(true);
        if ($input) {

            $bln = (int)date('m', strtotime($input['tanggal_apel']));
            $thn = (int)date('Y', strtotime($input['tanggal_apel']));

            //cek status verifikasi laporan presensi
            $laporan = $this->apelpagi_service->getData('SELECT * FROM tb_laporan WHERE bulan = ? AND tahun = ? AND kdlokasi = ?', [$bln, $thn, $input['kdlokasi']]);
            if ($laporan['count'] > 0) {
                echo '3';
                exit;
            }

            /*$dataTabel = $this->apelpagi_service->getData('SELECT * FROM view_apel_all WHERE (pin_absen = ? AND tanggal_log_presensi = ?) ORDER BY jam_log_presensi DESC', array($input['pin_absen'], $input['tanggal_apel']));*/
            $dataTabel = $this->apelpagi_service->getData('SELECT * FROM tb_log_presensi WHERE (status_log_presensi = 2 AND pin_absen = ? AND tanggal_log_presensi = ?) ORDER BY jam_log_presensi DESC', array($input['pin_absen'], $input['tanggal_apel']));
            $data['dataTabel'] = $this->apelpagi_service->checkApel($dataTabel['value']);

            $cek = $this->apelpagi_service->getData('SELECT * FROM tb_batal_apel WHERE (pin_absen = ? AND tanggal_apel = ?)', array($input['pin_absen'], $input['tanggal_apel']));

            if ($cek['count'] > 0) {
                echo '1';
                exit;
            }

            if (count($data['dataTabel']) == 0) {
                echo '2';
                exit;
            }

            //ambil jamapel
            $jam = $this->apelpagi_service->getData('SELECT * FROM `tb_jam_apel` WHERE "'.$input['tanggal_apel'].'" BETWEEN tanggal_mulai AND tanggal_akhir ORDER BY id_jam_apel DESC', []);

            if ($jam['count'] == 0)
                $jam = $this->apelpagi_service->getData('SELECT * FROM `tb_jam_apel` WHERE is_default=1', []);

            $data['jam_apel'] = $jam['value'][0]['mulai_apel'] .' - '.$jam['value'][0]['akhir_apel'];

            $this->subView('tabelapel', $data);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}