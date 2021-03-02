<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\servicemasterpresensi;
use system;
use comp;

class mastergruppengguna extends system\Controller {

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
        $data['title'] = 'Master Grup Pengguna';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Grup Pengguna</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelGrupPengguna($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            
            // edit
            if(!(empty($input['kd_grup_pengguna']))){
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Grup Pengguna';
            }
            // input
            else{
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Grup Pengguna';
                
                // code : KDGRUP01
                $code = $this->servicemasterpresensi->getDataGrupPenggunaLast();
                $last_code = $code['kd_grup_pengguna'];
                $data['last_code'] = comp\FUNC::autonumber($last_code, 6, 2);
            }
            
            $data['dataTabel'] = $this->servicemasterpresensi->getDataGrupPenggunaForm($input['kd_grup_pengguna']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataGrupPenggunaForm($input['kd_grup_pengguna']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
            $result = $this->servicemasterpresensi->save_update('tb_grup_pengguna', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_grup_pengguna' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_grup_pengguna', $idKey);
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
