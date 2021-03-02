<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\servicemasterpresensi;
use app\adminopd\model\servicemasterpegawai;
use system;
use comp;

class pengaturanprofil extends system\Controller {

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
        $data['title'] = 'Pengaturan Profil';
        $data['table_title'] = 'Tabel Master Mesin';
        $data['breadcrumb'] = '<a href="'.$this->link().'pns" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pengaturan</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Profil</a>';
        $data['dataLogin'] = $this->login;
        
        $nipbaru = $this->login['nipbaru'];
        $grup_pengguna_kd = $this->login['grup_pengguna_kd'];
        
        $data['dataPegawai'] = $this->servicemasterpegawai->getDataPegawaiPresensi($nipbaru);
        $data['dataFotoPegawai'] = $this->servicemasterpegawai->getDataFotoPegawai($nipbaru);
        $data['dataGrupPengguna'] = $this->servicemasterpresensi->getProfilGrupPengguna($grup_pengguna_kd);
        
        $this->showView('index', $data, 'theme_admin');
    }
    
    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $username = $this->login['username'];
            $password_lama = comp\FUNC::encryptor($input['password_lama']);
            $password_lama_session = $this->login['password'];
            
            if($password_lama == $password_lama_session){
                if($input['password_baru']==$input['password_konfirmasi']){
                    $data = $this->servicemasterpresensi->getDataPenggunaForm($username);
                    foreach($data as $key => $value){if(isset($input[$key])) $data[$key] = $input[$key];}
                    $data['password'] = comp\FUNC::encryptor($input['password_baru']);
                    $this->servicemasterpresensi->save_update('tb_pengguna', $data);
                    echo json_encode("ok");
                }
                else{
                    echo json_encode("beda");
                }
            }
            else{
                echo json_encode("gagal");
            }
        }
    }
    
    public function keluar() {
        $this->redirect($this->link('admin/logout'));
    }
    
    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
