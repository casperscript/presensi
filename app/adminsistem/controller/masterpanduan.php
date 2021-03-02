<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpresensi;
use system;
use comp;

class masterpanduan extends system\Controller {

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
        $data['title'] = 'Master Panduan';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Panduan</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelPanduan($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data['link_file'] = $this->link('upload/panduan/');
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
                        
            // edit
            if(!(empty($input['kd_panduan']))){
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Panduan';
            }
            // input
            else{
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Panduan';
                
                // code : PAN001
                $code = $this->servicemasterpresensi->getDataPanduanLast();
                $last_code = $code['kd_panduan'];
                $data['last_code'] = comp\FUNC::autonumber($last_code, 3, 3);
            }
            
            $data['dataTabel'] = $this->servicemasterpresensi->getDataPanduanForm($input['kd_panduan']);
            $this->subView('form', $data);
        }
    }
        
    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataPanduanForm($input['kd_panduan']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
          
            if (!empty($_FILES['file']['name'])) {
                $rand_number = rand(0, 999);
                $nmfile_server = date('YmdHis') . $rand_number .'_'. $_FILES['file']['name'];
                $opt_upload = array(
                    'fileName' => $nmfile_server,
                    'fileType' => $this->file_type,
                    'maxSize' => $this->max_size,
                    'folder' => $this->dir_panduan,
                    'session' => true,
                );
                $result = $this->files->upload($_FILES['file'], $opt_upload);
                if ($result['status'] == "success") { //upload sukses
                    $data['file_panduan'] = $nmfile_server;
                    $result = $this->servicemain->save_update('tb_panduan', $data);
                    $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
                }
                else{ //upload gagal
                    $error_msg = array('status' => 'error', 'message' => 'File gagal diupload');
                }
            }
            else{
                $result = $this->servicemain->save_update('tb_panduan', $data);
                $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            }
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_panduan' => $input['id']);
            $data = $this->servicemasterpresensi->getDataPanduanForm($input['id']);
            unlink($this->dir_panduan . '/' . $data['file_panduan']);
            $result = $this->servicemasterpresensi->delete('tb_panduan', $idKey);
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
