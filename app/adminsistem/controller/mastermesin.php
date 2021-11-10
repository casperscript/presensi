<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpresensi;
use system;
use comp;

class mastermesin extends system\Controller
{

    public function __construct()
    {
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

    protected function index()
    {
        $data['title'] = 'Master Mesin';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
            . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
            . 'Master</a><a class="breadcrumb white-text" style="font-size: 13px;">'
            . 'Mesin</a>';
        $data['listStatus'] = [0 => ':: Semua Status ::', 'enable' => 'Enable', 'disable' => 'Disable'];
        $data['listShowData'] = [5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100];
        $data['nama_kelompok'] = [0 => ':: Semua kelompok ::'] + $this->servicemasterpresensi->getPilihanKelompokMesin();
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel()
    {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->servicemasterpresensi->getTabelMesin($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form()
    {
        $input = $this->post(true);
        if ($input) {

            // edit
            if (!(empty($input['id_mesin']))) {
                $data['op'] = 'edit';
                $data['form_title'] = 'Ubah Data Mesin';
            }
            // input
            else {
                $data['op'] = 'input';
                $data['form_title'] = 'Tambah Data Mesin';
            }

            $data['pil_kelompok_mesin'] = array('' => '-- PILIH KELOMPOK MESIN --') + $this->servicemasterpresensi->getPilihanKelompokMesin();
            $data['dataTabel'] = $this->servicemasterpresensi->getDataMesinForm($input['id_mesin']);
            $this->subView('form', $data);
        }
    }

    public function simpan()
    {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataMesinForm($input['id_mesin']);
            foreach ($data as $key => $value) {
                if (isset($input[$key])) $data[$key] = $input[$key];
            }
            $result = $this->servicemasterpresensi->save_update('tb_mesin', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus()
    {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_mesin' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_mesin', $idKey);
            $error_msg = ($result['error']) ? array('title' => 'Berhasil', 'message' => 'Data telah dihapus', 'status' => 'success') :
                array('title' => 'Gagal', 'message' => 'Terjadi kesalahan ketika menghapus data', 'status' => 'error');
            echo json_encode($error_msg);
        }
    }

    public function script()
    {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}
