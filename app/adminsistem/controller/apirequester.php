<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\webservice;
use system;
use comp;

class apirequester extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->webservice = new webservice();
    }

    public function index() {
        echo 'Silahkan pilih method';
    }
    
    public function getBiodata() {
        $input = $this->post(false);
        if ($input) {
            $parameter = array('method' => 'get_nominal_tpp', 'nip' => $input['nip'], 'bulan' => $input['bulan'], 'tahun' => $input['tahun']);
            $accesskey = 'aEFpbEJtUHQzTjA0WlJvRVN1UHV4QT09';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://new-presensi.pekalongankota.go.id/adminsistem/api/");
//            curl_setopt($ch, CURLOPT_URL, "http://localhost/git/presensi2021/adminsistem/api/");
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

    public function getListTPP() {
        $input = $this->post(false);
        if ($input) {
            $parameter = array('method' => 'get_list_tpp', 'bulan' => $input['bulan'], 'tahun' => $input['tahun'], 'kdlokasi' => $input['kdlokasi']);
            $accesskey = 'T3hNWFZUeEluNnZyOVhsalZVa1FDUT09';

            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, "http://new-presensi.pekalongankota.go.id/adminsistem/api/");
            curl_setopt($ch, CURLOPT_URL, "http://localhost/git/presensi2021/adminsistem/api/");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("AccessKey:" . $accesskey));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);
//            comp\FUNC::showPre($parameter);
//            comp\FUNC::showPre(curl_getinfo($ch));
            curl_close($ch);
            $data['output'] = $output;
        }
        $data['bulan'] = isset($input['bulan']) ? $input['bulan'] : date('m');
        $data['tahun'] = isset($input['tahun']) ? $input['tahun'] : date('Y');
        $data['kdlokasi'] = isset($input['kdlokasi']) ? $input['kdlokasi'] : '';
        $data['listSatker'] = ['' => ':: Semua Satker ::'] + $this->webservice->getListSatker();
        $this->subView('listtpp', $data);
    }

    public function getPresensi() {
        $input = $this->post(false);
        if ($input) {
            $parameter = array('method' => 'get_presensi', 'nip' => $input['nip'], 'bulan' => $input['bulan'], 'tahun' => $input['tahun']);
            $accesskey = 'TXhmMWs1QjMwUHExVUJDcEZnRWVBZz09';

            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, "http://new-presensi.pekalongankota.go.id/adminsistem/api/");
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
    
    public function getPoinByOPD() {
        $input = $this->post(false);
        if ($input) {
            $parameter = array('opd' => $input['opd'], 'bulan' => $input['bulan'], 'tahun' => $input['tahun']);
            $accesskey = 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://pamomong.pekalongankota.go.id/e-kinerja-beta/super/api/poin");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("kinerja-key:" . $accesskey));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);
            curl_close($ch);
            $data['output'] = $output;
        }
        
        $data['bulan'] = isset($input['bulan']) ? $input['bulan'] : date('m');
        $data['tahun'] = isset($input['tahun']) ? $input['tahun'] : date('Y');
        $data['opd'] = isset($input['opd']) ? $input['opd'] : '';
        $data['listSatker'] = ['' => ':: Semua Satker ::'] + $this->webservice->getListSatker();
        $this->subView('poinopd', $data);
    }

}
