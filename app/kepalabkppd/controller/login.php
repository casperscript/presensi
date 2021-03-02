<?php

namespace app\kepalabkppd\controller;

use app\kepalabkppd\model\servicemain;
use system;

class login extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->redirect($this->link('kepalabkppd/main'));
        }
        
        $this->reload = $this->getSession('SESSION_RELOAD');
        if ($this->reload) {
            $this->desSession();
            $this->servicemain->removeCookie();
            echo '<script>location.reload()</script>';
        }
        
    }

    protected function index() {
        $this->redirect($this->link('main'));
        /*
        $data['title'] = 'Login Area';
        $data['subtitle'] = 'Selamat Datang';
        $data['username'] = '';
        $data['password'] = '';
        $this->showView('index', $data, 'theme_admin');
        */
    }

    protected function submit() {
        $input = $this->post(false);
        if ($input) {
            $cek = $this->servicemain->cekLogin($input);
            if ($cek > 0){
                $login = $this->servicemain->login($input);
                $this->setSession('SESSION_LOGIN', $login);
                $result['data'] = $login;
                $result['status'] = 1;
                $result['pesan'] = 'Sukses, Login Kepala BKPPD Berhasil';
            }
            else{
                $result['data'] = $input['password'];
                $result['status'] = 0;
                $result['pesan'] = 'Maaf, Login Kepala BKPP Gagal';
            }
            
            echo json_encode($result);
            
        }
    }
    
    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
