<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpresensi;
use app\adminsistem\model\servicemasterpegawai;
use system;
use comp;

class mastertpp extends system\Controller {
	 public function __construct() {
        parent::__construct();
        $this->servicemasterpresensi = new servicemasterpresensi();
        $this->servicemasterpegawai = new servicemasterpegawai();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('admin/login'));
        }
    }

    protected function index() {
        $data['title'] = 'Master TPP';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'TPP</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelTpp($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    protected function kecuali() {
        $input = $this->post(true);
        if ($input) {
            $data['dataTpp'] = $this->servicemasterpresensi->getTabelTpp($input)['dataTabel'];
            if (count($data['dataTpp']) == 0)
                $this->redirect($this->link('adminsistem/mastertpp'));

            $data['dataKecuali'] = $this->servicemasterpresensi->getDataTppKecuali($input);
            $p = '';
            $data['pegawai'] = '';
            if ($data['dataKecuali']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['dataKecuali']['value']);

                $p = implode(',', $personil);

                $data['pegawai'] = $this->servicemasterpegawai->getDataPegawai($p);
            }

            $this->subView('kecuali', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $data['dataTabel'] = $this->servicemasterpresensi->getDataTppForm($input['kd_tpp']);
            // edit
            if(!(empty($input['kd_tpp']))){
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data TPP';
            }
            // input
            else{
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data TPP';
                $data['dataTabel']['bulan'] = date('m');
                $data['dataTabel']['tahun'] = date('Y');
            }
            
            $data['pil_jenis_tpp'] = [
                '' => '-- PILIH JENIS --', 
                'TPP13' => 'TPP 13',
                'TPP14' => 'TPP 14',
                'TPPDES' => 'TPP Akhir Tahun'
            ];

            $data['pil_bulan'] = [1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'];

            $data['pil_tahun'] = [];
            for ($i = 2018; $i <= 2025; $i++)
                $data['pil_tahun'][$i] = $i;

            $data['pil_periode'] = [
                'full' => 'SATU BULAN',
                'half' => 'TANGGAL 1 S/D 15',
                'custom' => 'CUSTOM'
            ];

            $data['pil_tingkat'] = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 'Final'];

            $this->subView('form', $data);
        }
    }

    public function formKecuali() {
        $input = $this->post(true);
        if ($input) {
            $data = $input;
            $data['pil_lokasi'] = array('' => '-- PILIH OPD --') + $this->servicemasterpegawai->getTabelPilihanLokasiKerja();
            $data['pil_nipbaru'] = array('' => '-- PILIH PEGAWAI --');
            $this->subView('form-kecuali', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataTppForm($input['kd_tpp']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}

            if (empty($data['kd_tpp']))
                $data['created_at'] = date('Y-m-d H:i:s');
            else
                $data['updated_at'] = date('Y-m-d H:i:s');

            $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
            $jml_hari = $data['tgl_akhir'] - $data['tgl_awal'] + 1;

            if ($hitungtgl == $jml_hari)
                $data['periode'] = 'full';

            $result = $this->servicemasterpresensi->save_update('tb_tpp', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_tpp' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_tpp', $idKey);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
        }
    }

    protected function pilpegawai() {
        $input = $this->post(true);
        if ($input) {
            $pil_pegawai = $this->servicemasterpegawai->getPilihanAdmin($input['kdlokasi']);
            echo comp\MATERIALIZE::inputSelect('nipbaru', $pil_pegawai, '', '');            
        }
    }

    public function simpanKecuali() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataTppKecualiForm();
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}

            if (empty($data['kd_tpp']))
                $data['created_at'] = date('Y-m-d H:i:s');
            else
                $data['updated_at'] = date('Y-m-d H:i:s');

            $result = $this->servicemasterpresensi->save_update('tb_tpp_not', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapusKecuali() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_tpp_not' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_tpp_not', $idKey);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}