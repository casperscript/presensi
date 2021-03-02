<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\pegawai_service;
use app\admin\model\presensi_service;
use system;
use comp;

class usermesin extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] == true) {
            $this->presensi_service = new presensi_service();
            $this->pegawai_service = new pegawai_service();
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Data User Fingerprint';
        $data['pil_kel_satker'] = $this->pegawai_service->getPilKelSatker();
        $data['kel_satker'] = 'G09';
        $data['pil_satker'] = array('' => '');
        $this->showView('index', $data, 'theme_admin');
    }

    public function getBackupUserFinger() {
        $input = $this->post(true);
        if ($input) {
//            $dataMesin = $this->presensi_service->getDataRefMesin($input);
            $dataMesin = $this->presensi_service->getDataKrit('view_mesin', $input);
            $ip = $dataMesin['ip_mesin'];
            $port = $dataMesin['port_mesin'];
            $pswdComm = $dataMesin['password_mesin'];

            $errno = "";
            $errstr = "";
            $newLine = "\r\n";

            $dataUser = array();
            $Connect = fsockopen($ip, $port, $errno, $errstr, 1);

            if ($Connect) {
                $soap_request = "<GetUserInfo>
                            <ArgComKey xsi:type=\"xsd:string\">$pswdComm</ArgComKey>
                            <Arg><PIN xsi:type=\"xsd:integer\"></PIN> 
                            </Arg>                  
                        </GetUserInfo>";

                $newLine = "\r\n";


                fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($Connect, "Content-Type: text/xml" . $newLine);
                fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($Connect, $soap_request);
                $buffer = "";
                while ($Response = fgets($Connect, 1024)) {
                    $buffer = $buffer . $Response;
                }
                $buffer = comp\FUNC::Parse_Data($buffer, "<GetUserInfoResponse>", "</GetUserInfoResponse>");
                $buffer = explode("\r\n", $buffer);
                sort($buffer);
                for ($a = 1; $a < count($buffer); $a++) {
                    $data = comp\FUNC::Parse_Data($buffer[$a], "<Row>", "</Row>");
                    $pin = comp\FUNC::Parse_Data($data, "<PIN2>", "</PIN2>");
                    $nama = comp\FUNC::Parse_Data($data, "<Name>", "</Name>");

                    $dataUser[$pin] = $nama;
                }
                $this->setSession('TEMP_FINGERPRINT', array($input['kdlokasi'] => $dataUser));
                $error_msg = array('status' => 'success', 'message' => 'Ambil data user fingerprint berhasil');
            } else {
                $error_msg = array('status' => 'error', 'message' => 'Ambil data user fingerprint gagal');
            }
            echo json_encode($error_msg);
        }
    }

    protected function userDinas() {
        $input = $this->post(true);
        if ($input) {
            $sess_finger = $this->getSession('TEMP_FINGERPRINT');
            $data['dataTabel'] = $this->pegawai_service->getTabelPersonil($input);
            $data['sessUser'] = (isset($sess_finger[$input['kdlokasi']])) ? $sess_finger[$input['kdlokasi']] : array();
            $this->subView('userdinas', $data);
        }
    }

    protected function userFinger() {
        $input = $this->post(true);
        if ($input) {
            $sess_finger = $this->getSession('TEMP_FINGERPRINT');
            $data['dataTabel'] = isset($sess_finger[$input['kdlokasi']]) ? $sess_finger[$input['kdlokasi']] : array();
            $this->subView('userfinger', $data);
//            comp\FUNC::showPre($data);
        }
    }

    public function getPilLokasiFromKelLokasi() {
        $input = $this->post(true);
        if ($input) {
            $result = $this->pegawai_service->getPilLokasi($input);
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    protected function spinner() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('spinner', $data);
    }
    
    protected function script() {
        $data = array();
        $this->subView('script', $data);
    }

}
