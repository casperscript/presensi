<?php

namespace app\adminsistem\controller;

use system;
use comp;

class apirequester extends system\Controller {

    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        echo 'Silahkan pilih method';
    }

    public function getBiodata() {
        $input = $this->post(true);
        if ($input) {
            $parameter = array('method' => 'get_nominal_tpp', 'nip' => $input['nip'], 'bulan' => $input['bulan'], 'tahun' => $input['tahun']);
            $accesskey = 'aEFpbEJtUHQzTjA0WlJvRVN1UHV4QT09';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://localhost/git/presensi2021/adminsistem/api/");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("AccessKey:" . $accesskey));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);
            curl_close($ch);
            $data['output'] = $output;
        }
        $data['nip'] = isset($input['nip']) ? $input['nip'] : '';
        $data['bulan'] = isset($input['bulan']) ? $input['bulan'] : date('m');
        $data['tahun'] = isset($input['tahun']) ? $input['tahun'] : date('Y');
        $this->subView('biodata', $data);
    }
    
    public function getPresensi() {
        $input = $this->post(true);
        if ($input) {
            $parameter = array('method' => 'get_presensi', 'nip' => $input['nip'], 'bulan' => $input['bulan'], 'tahun' => $input['tahun']);
            $accesskey = 'TXhmMWs1QjMwUHExVUJDcEZnRWVBZz09';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://localhost/git/presensi2021/adminsistem/api/");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("AccessKey:" . $accesskey));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);
            curl_close($ch);
            $data['output'] = $output;
        }
        $data['nip'] = isset($input['nip']) ? $input['nip'] : '';
        $data['bulan'] = isset($input['bulan']) ? $input['bulan'] : date('m');
        $data['tahun'] = isset($input['tahun']) ? $input['tahun'] : date('Y');
        $this->subView('presensi', $data);
    }

}
