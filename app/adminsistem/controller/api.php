<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\webservice;
use system;
use comp;

class api extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->webservice = new webservice();
        $getParam = $_POST;
        $getAccesskey = $_SERVER['HTTP_ACCESSKEY'];
        $check = $this->webservice->checkaccesskey($getAccesskey, $getParam);
        if ($check['count']) {
            $this->access = $check['value'][0];
            $this->param = $getParam;
        } else {
            echo 'Request data not allowed!';
            exit;
        }
    }

    protected function index() {
        $func = $this->access['function'];
        header('Content-Type: application/json');
        self::$func();
    }

    protected function svcDataTpp() {
        $nip = isset($this->param['nip']) ? $this->param['nip'] : false;
        $bulan = isset($this->param['bulan']) ? $this->param['bulan'] : false;
        $tahun = isset($this->param['tahun']) ? $this->param['tahun'] : false;

        if ($nip && $bulan && $tahun) {
            // ambil dari data backup
            $getTppBc = $this->webservice->getTppBc($this->param);
            if ($getTppBc['count'] > 0) {
                $msg = ['status' => 'success', 'source' => 'backup', 'data' => $getTppBc['value'][0]];
                echo json_encode($msg);
                exit;
            }

            // ambil dari data utama
            $getTpp = $this->webservice->getTpp($this->param);
            if ($getTpp['count'] > 0) {
                $msg = ['status' => 'success', 'source' => 'currently', 'data' => $getTpp['value'][0]];
                echo json_encode($msg);
                exit;
            }

            $msg = ['status' => 'error', 'msg' => 'Pegawai bukan penerima TPP', 'data' => []];
            echo json_encode($msg);
        } else {
            $msg = ['status' => 'error', 'msg' => 'Parameter yang dikirim tidak sesuai', 'parameter' => $this->param];
            echo json_encode($msg);
        }
    }

    protected function svcListTpp() {
        $bulan = isset($this->param['bulan']) ? $this->param['bulan'] : false;
        $tahun = isset($this->param['tahun']) ? $this->param['tahun'] : false;

        if ($bulan && $tahun) {
            $getListTppBc = $this->webservice->getListTppBc($this->param);
            if ($getListTppBc['count'] > 0) {
                $msg = ['status' => 'success', 'source' => 'backup', 'data' => $getListTppBc['value']];
                echo json_encode($msg);
                exit;
            }

            $getListTppMain = $this->webservice->getListTppMain($this->param);
            if ($getListTppMain['count'] > 0) {
                $msg = ['status' => 'success', 'source' => 'currently', 'data' => $getListTppMain['value']];
                echo json_encode($msg);
                exit;
            }

            $msg = ['status' => 'error', 'msg' => 'Tidak ditemukan data penerima TPP', 'param' => $this->param];
            echo json_encode($msg);
        } else {
            $msg = ['status' => 'error', 'msg' => 'Parameter yang dikirim tidak sesuai', 'parameter' => $this->param];
            echo json_encode($msg);
        }
    }

    protected function svcPresensiBulan() {
        $nip = isset($this->param['nip']) ? $this->param['nip'] : false;
        $bulan = isset($this->param['bulan']) ? $this->param['bulan'] : false;
        $tahun = isset($this->param['tahun']) ? $this->param['tahun'] : false;

        if ($nip && $bulan && $tahun) {
            // ambil dari data backup
            $getPresensiBc = $this->webservice->getPresensiBc($this->param);
            if ($getPresensiBc['count'] > 0) {
                $result = $getPresensiBc['value'][0];
                for ($a = 1; $a <= 31; $a++) {
                    $result['t' . $a] = $this->parsePresensiBc($result['t' . $a]);
                }
                $msg = ['status' => 'success', 'source' => 'backup', 'data' => $result];
                echo json_encode($msg);
                exit;
            }

            // ambil dari data utama
            $getPresensi = $this->webservice->getPresensiMain($this->param);
            if ($getPresensi['count'] > 0) {
                echo json_encode($getPresensi);
                exit;
            }

            // tampilkan error ketika tidak ada data presensi sama sekali
            $msg = ['status' => 'error', 'msg' => 'Tidak ditemukan data presensi', 'param' => $this->param];
            echo json_encode($msg);
        }
    }

    protected function parsePresensiBc($param) {
        $data = json_decode($param, true);
        $result = array();
        if (isset($data[6])) {
            $inArrayField = ['waktu', 'kode'];
            $inArrayCat = ['mk', 'ap', 'pk'];
            foreach ($data[6] as $key => $val) {
                if (in_array($key, $inArrayCat)) {
                    $harian = (array) $val;
                    foreach ($harian as $subKey => $subVal) {
                        if (in_array($subKey, $inArrayField)) {
                            $result[$key][$subKey] = $subVal;
                        }
                    }
                }
            }
            return $result;
        }
    }

}
