<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\servicemasterpresensi;
use system;

class masterpotonganpajak extends system\Controller {

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
        $data['title'] = 'Master Potongan Pajak';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Potongan Pajak</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelPotonganPajak($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            
            // edit
            if(!(empty($input['id_potongan_pajak']))){
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Potongan Pajak';
            }
            // input
            else{
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Potongan Pajak';
            }
            
            $data['pil_golruang_kepegawaian'] = $this->servicemasterpresensi->getPilihanGolongan();
            $data['dataTabel'] = $this->servicemasterpresensi->getDataPotonganPajakForm($input['id_potongan_pajak']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataPotonganPajakForm($input['id_potongan_pajak']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
            $result = $this->servicemasterpresensi->save_update('tb_potongan_pajak', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_potongan_pajak' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_potongan_pajak', $idKey);
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
