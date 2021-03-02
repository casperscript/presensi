<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\servicemasterpresensi;
use system;
use comp;

class masteraturan extends system\Controller {

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
        $data['title'] = 'Master Aturan';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Aturan</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelAturan($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            
            // edit
            if(!(empty($input['kd_aturan']))){
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Aturan';
            }
            // input
            else{
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Aturan';
                
                // code : ATR003
                $code = $this->servicemasterpresensi->getDataAturanLast();
                $last_code = $code['kd_aturan'];
                $data['last_code'] = comp\FUNC::autonumber($last_code, 3, 3);
            }
            
            $data['pil_status_aturan'] = array('' => '-- PILIH STATUS --') + $this->servicemasterpresensi->getPilihanStatusAturan();
            $data['dataTabel'] = $this->servicemasterpresensi->getDataAturanForm($input['kd_aturan']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataAturanForm($input['kd_aturan']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
            $result = $this->servicemasterpresensi->save_update('tb_aturan', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_aturan' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_aturan', $idKey);
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
