<?php

namespace app\anggaran\controller;

use app\anggaran\model\servicemain;
use app\anggaran\model\servicemasterpresensi;
use system;

class login extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->servicemaster = new servicemasterpresensi();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->redirect($this->link('anggaran/main'));
        }
        
        $this->reload = $this->getSession('SESSION_RELOAD');
        if ($this->reload) {
            $this->desSession();
            $this->servicemain->removeCookie();
            echo '<script>location.reload()</script>';
        }
    }

    protected function index() {
        $data['title'] = 'Login Area';
        $data['subtitle'] = 'Selamat Datang';
        $data['username'] = '';
        $data['password'] = '';
        $data['teks'] = $this->servicemaster->getTeks('LOGIN');
        $this->showView('index', $data, 'theme_admin');
    }

    protected function submit() {
        $input = $this->post(false);
        if ($input) {
            $cek = $this->servicemain->cekLogin($input);
            if ($cek > 0){
                $login = $this->servicemain->login($input);                
                $this->setNewSession($this->getProjectName($login), 'SESSION_LOGIN', $login);                
                $result['data'] = $login;
                $result['status'] = 1;
                $result['pesan'] = 'Sukses, login berhasil';
                
            } else {
                $result['data'] = $input['password'];
                $result['status'] = 0;
                $result['pesan'] = "Login Gagal <br> username atau password salah";
//                $result['pesan'] = "Maaf, Login New e-Presensi Gagal.\n\rPastikan username dan password Anda telah terdaftar dan benar.\n\rSilakan coba lagi.";
            }
            
            echo json_encode($result);
            
        }
    }

    public function getProjectName($loginData)
    {
        switch ($loginData["grup_pengguna_kd"]) {
            case "KDGRUP01": return "adminopd";
            case "KDGRUP02": return "kepalaopd";
            case "KDGRUP03": return "admin";
            case "KDGRUP04": return "kepalabkppd";
            case "KDGRUP05": return "pns";
            case "KDGRUP06": return "pengawas";
            case "KDGRUP07": return "anggaran";
            case "KDGRUP99": return "adminsistem";
            default: return '';
        }
    }
    
    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
