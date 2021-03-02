<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpresensi;
use system;
use comp;

class masterdinas extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemasterpresensi = new servicemasterpresensi();
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
        $data['title'] = 'Master Jenis Dinas';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Jenis Dinas</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelJenisDinas($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            
            // edit
            if(!(empty($input['kd_jenis_dinas']))){
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Jenis Dinas';
            }
            // input
            else{
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Jenis Dinas';
                
                // code : JNSDIN01
                $code = $this->servicemasterpresensi->getDataJenisDinasLast();
                $last_code = $code['kd_jenis_dinas'];
                $data['last_code'] = comp\FUNC::autonumber($last_code, 6, 2);
            }
            
            $data['dataTabel'] = $this->servicemasterpresensi->getDataJenisDinasForm($input['kd_jenis_dinas']);
            $this->subView('form', $data);
        }
    }
    
    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataJenisDinasForm($input['kd_jenis_dinas']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
            $result = $this->servicemasterpresensi->save_update('tb_jenis_dinas', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_jenis_dinas' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_jenis_dinas', $idKey);
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
