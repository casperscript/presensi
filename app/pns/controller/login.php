<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use system;
use comp;

class login extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->redirect($this->link('pns/main'));
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
        $this->showView('index2', $data, 'theme_admin');*/
    }

    public function submitAPI() {
        header("Access-Control-Allow-Origin: *");
        $input = $this->post(false);
        if ($input) {
            $input['username'] = $input['nip'];
            $data = $this->servicemain->regUserAPI($input);

            if ($data['status'] == 'success') {
                $generate = comp\FUNC::encryptor($data['username'] . '|' . $data['last_login']);
                $result['status'] = 'success';
                $result['message'] = 'User access found';
                $result['url'] = 'http://new-presensi.pekalongankota.go.id/pns/login/loginAPI/' . $generate;
                $this->showResponse($result);
            } else {
                $result['status'] = 'failed';
                $result['message'] = 'User access not found!';
                $result['data'] = $data;
                $this->showResponse($result, '404');
            }
            
        }
    }
    
    protected function showResponse($errorMsg, $code = ''){
    	$_content_type = 'application/json';
    	$_code = 200;
        if(empty($code)) $code = $_code;
        header('HTTP/1.1 '.$code);
        header('Content-Type:'.$_content_type);
        echo json_encode($errorMsg);
    }

    public function loginAPI($id) {
        $decrypt = comp\FUNC::decryptor($id);
        if (empty($decrypt)) {
            echo '<h1>Wrong token!!';
            exit;
        }

        $parseId = explode('|', $decrypt);
        $input['username'] = $parseId[0];
        $input['last_login'] = $parseId[1];
        $cekUserAPI = $this->servicemain->cekUserAPI($input);

        if ($cekUserAPI['count'] > 0) {
            $login = $this->servicemain->login($input);
            $this->setSession('SESSION_LOGIN', $login);
            header('location: new-presensi.pekalongankota.go.id/pns/main');
        }
    }

    protected function submit() {
        $input = $this->post(false);
        if ($input) {
            $cek = $this->servicemain->cekLogin($input);
            if ($cek > 0) {
                $login = $this->servicemain->login($input);
                $this->setSession('SESSION_LOGIN', $login);
                $result['data'] = $login;
                $result['status'] = 1;
                $result['pesan'] = 'Sukses, Login PNS Berhasil';
            } else {
                $result['data'] = $input['password'];
                $result['status'] = 0;
                $result['pesan'] = 'Maaf, Login PNS Gagal';
            }

            echo json_encode($result);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
