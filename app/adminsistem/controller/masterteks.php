<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpresensi;
use system;

class masterteks extends system\Controller {
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
        $data['title'] = 'Master Teks';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Teks</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelTeks($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $data['pil_warna'] = [
                'white' => 'Putih',
                'pink lighten-4' => 'Pink',
                'blue lighten-4' => 'Biru',
                'orange lighten-4' => 'Jingga',
                'light-green lighten-3' => 'Hijau',
                'yellow lighten-3' => 'Kuning'
            ];
            $data['pil_lokasi'] = [
                'LOGIN' => 'Halaman Login',
                'BERANDA' => 'Halaman Beranda'
            ];
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $data['dataTabel'] = $this->servicemasterpresensi->getDataTeksForm($input['kd_teks']);

            if (empty($input['kd_teks'])){
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Teks';
            } else {
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Teks';
            }

            $data['pil_lokasi'] = [
                'LOGIN' => 'Halaman Login',
                'BERANDA' => 'Halaman Beranda'
            ];
            $data['pil_bentuk'] = [
                'TEMPEL' => 'Tempel',
                'POPUP' => 'Popup'
            ];

            $data['pil_warna'] = [
                'white' => 'Putih',
                'pink lighten-4' => 'Pink',
                'blue lighten-4' => 'Biru',
                'orange lighten-4' => 'Jingga',
                'light-green lighten-3' => 'Hijau',
                'yellow lighten-3' => 'Kuning',
                'purple lighten-4' => 'Ungu'
            ];

            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $checkbox = ['pns', 'admin_opd', 'kepala_opd', 'admin', 'kepala_bkppd'];
            $data = $this->servicemasterpresensi->getDataTeksForm($input['kd_teks']);
            foreach($data as $key => $value) {
                if(isset($input[$key])) 
                    $data[$key] = $input[$key];
                elseif (in_array($key, $checkbox)) 
                    $data[$key] = 0;
            }

            if (empty($data['kd_teks']))
                $data['created_at'] = date('Y-m-d H:i:s');
            else
                $data['updated_at'] = date('Y-m-d H:i:s');

            $checkbox = ['pns', 'admin_opd', 'kepala_opd', 'admin', 'kepala_bkppd'];
            foreach($checkbox as $i) {
                if (!empty($data[$i]))
                    $data[$i] = 1;
            }

            $result = $this->servicemasterpresensi->save_update('tb_teks', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('kd_teks' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_teks', $idKey);
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