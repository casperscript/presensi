<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\servicemasterpresensi;
use app\admin\model\servicemasterpegawai;
use system;

class mastershift extends system\Controller {

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
        $data['title'] = 'Master Shift';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Shift</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelShift($input);
            $data['nama_lokasi'] = $this->servicemasterpegawai->getTabelPilihanLokasiKerja();
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            
            $data['pil_unit_shift'] = $this->servicemasterpresensi->getPilihanUnitShift();
            $data['pil_lokasi'] = array('' => '-- PILIH LOKASI KERJA --') + $this->servicemasterpegawai->getTabelPilihanLokasiKerja();
            
            // edit
            if(!(empty($input['id_shift']))){
                $data['form_title'] = 'Ubah Data Shift';
                $data['op'] = 'edit';
            }
            // input
            else{
                $data['form_title'] = 'Tambah Data Shift';
                $data['op'] = 'input';
            }
            
            $data['dataTabel'] = $this->servicemasterpresensi->getDataShiftForm($input['id_shift']);
            $this->subView('form', $data);
        }
    }
    
    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataShiftForm($input['id_shift']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
            $result = $this->servicemasterpresensi->save_update('tb_shift', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_shift' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_shift', $idKey);
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

?>
