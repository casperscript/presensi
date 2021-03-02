<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use system;

class tunjangan extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            //$this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'TUNJANGAN';
        $data['subtitle'] = 'Halaman Master - Tunjangan';
        $data['table_title'] = 'Tabel Master Tunjangan';
        $data['breadcrumb'] = '<nav class="custom">
          <div class="nav-wrapper">
            <div class="col s12">
              <a href="'.$this->link().'" class="breadcrumb">Beranda</a>
              <a href="#!" class="breadcrumb">Master Tunjangan</a>
            </div>
          </div>
        </nav>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            //$dataTabel = $this->servicemain->getTabelBarang($input);
            //$data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Pengguna';
            //$data = array_merge($data, $dataTabel);
            $data['title'] =  '';
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $data['form_title'] = (!(empty($input['id_tunjangan']))) ? 'Ubah Data Tunjangan' : 'Tambah Data Tunjangan';
            //$data['dataTabel'] = $this->servicemain->getDataKategoriForm($input['id_kategori']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemain->getDataBarangForm($input['id_barang']);
            foreach ($data as $key => $value) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }
            $result = $this->servicemain->save_update('tb_barang', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_barang' => $input['id']);
            $result = $this->servicemain->delete('tb_barang', $idKey);
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
