<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\servicemasterpresensi;
use app\adminopd\model\servicemasterpegawai;
use system;
use comp;

class pengaturanpengguna extends system\Controller {

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
            $this->redirect($this->link('adminopd/login'));
        }
    }

    protected function index() {
        $data['title'] = 'Pengaturan Pengguna';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pengaturan</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pengguna</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $kdlokasi = $this->login['kdlokasi'];
            $dataTabel = $this->servicemasterpresensi->getTabelPenggunaAdminOPD($input, $kdlokasi);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $data['nama_lokasi'] = $this->servicemasterpegawai->getTabelPilihanLokasiKerja();
            $data['nama_personil'] = $this->servicemasterpegawai->getTabelPilihanNamaPersonil();
//            comp\FUNC::showPre($dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $kdlokasi = $this->login['kdlokasi'];
            $data['dataTabel'] = $this->servicemasterpresensi->getDataPenggunaForm($input['username']);
            $data['pil_lokasi'] = array('' => '-- PILIH LOKASI KERJA --') + $this->servicemasterpegawai->getTabelPilihanLokasiKerjaAdminOPD($kdlokasi);
            $data['pil_grup_pengguna'] = array('' => '-- PILIH GRUP PENGGUNA --') + $this->servicemasterpresensi->getPilihanGrupPenggunaAdminOPD();
            $data['pil_status_pengguna'] = $this->servicemasterpegawai->getPilihanStatusPengguna();
            
            // edit
            if(!(empty($input['username']))){
                $data['form_title'] = 'Ubah Data Pengguna';
                $data['op'] = 'edit';
                $data['pil_nipbaru'] = $this->servicemasterpegawai->getPilihanAdmin($data['dataTabel']['kdlokasi']);
                $password = array('password' => comp\FUNC::decryptor($data['dataTabel']['password']));
                $data['dataTabel'] = array_merge($data['dataTabel'], $password);
            }
            // input
            else{
                $data['form_title'] = 'Tambah Data Pengguna';
                $data['op'] = 'input';
                $data['pil_nipbaru'] = array('' => '-- PILIH ADMIN --');
            }
            
            $this->subView('form', $data);
        }
    }
    
    protected function piladmin() {
        $input = $this->post(true);
        if ($input) {
            $pil_admin = $this->servicemasterpegawai->getPilihanAdmin($input['kdlokasi']);
            echo comp\MATERIALIZE::inputSelect('nipbaru', $pil_admin, '', '');            
        }
    }
    
    public function cekprimary() {
        $input = $this->post(true);
        if ($input) {            
            $cek = $this->servicemasterpresensi->cekPrimaryKodePengguna($input['username']);
            echo ($cek>0) ? json_encode("ada") : json_encode("kosong");
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemasterpresensi->getDataPenggunaForm($input['username']);
            foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
            $data['password'] = comp\FUNC::encryptor($input['password']);
            $result = $this->servicemasterpresensi->save_update('tb_pengguna', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('username' => $input['id']);
            $result = $this->servicemasterpresensi->delete('tb_pengguna', $idKey);
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
