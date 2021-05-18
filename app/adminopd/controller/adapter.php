<?php

namespace app\adminopd\controller;

use system;
use comp;

class api extends system\Controller {

    public function __construct() {
        parent::__construct();
    }
    
    const WS_NIK_URL = 'http://192.168.254.213/api/diskominfo_pekalongan/svc_ktp_prov/';
    const WS_NIP_URL = 'http://192.168.254.213/api/diskominfo_pekalongan/svc_presensi/';
    const METHOD_NIK = 'get_biodata_by_nik';
    const METHOD_NIP = 'get_data_pegawai';

    public function getBioByNIK() {
        $nipk = '3375011310900004';

        $accesskey = '51b94130';
        $request = array('NIK' => $nipk);
        $data = self::callAPI(self::WS_NIK_URL, self::METHOD_NIK, $accesskey, $request, false, 'POST');

        comp\FUNC::showPre($data);


//        if (isset($data[self::METHOD_NIK]) && count($data[self::METHOD_NIK]) > 0) {
//            $results = $data[self::METHOD_NIK][0];
//            $results = IO::mappingKel($results, $results['NO_KEC'], $results['NO_KEL']);
//
//            if ($toJson) {
//                return Json::encode($results);
//            }
//
//            return $results;
//        }
    }
    
    public function getPegawaiByNIP() {
        $nipk = '3375011310900004';

        $accesskey = 'f78tjg3j25';
        $request = array('nipbaru' => $nipk);
        $data = self::callAPI(self::WS_NIP_URL, self::METHOD_NIP, $accesskey, $request, false, 'POST');

        comp\FUNC::showPre($data);


//        if (isset($data[self::METHOD_NIK]) && count($data[self::METHOD_NIK]) > 0) {
//            $results = $data[self::METHOD_NIK][0];
//            $results = IO::mappingKel($results, $results['NO_KEC'], $results['NO_KEL']);
//
//            if ($toJson) {
//                return Json::encode($results);
//            }
//
//            return $results;
//        }
    }

    ########################################## TEST CALL API ##################################################

    public static function callAPI($endpoint, $operation, $accesskey = '', $parameter = array(), $xmlformat = true, $callmethod = 'REST', $agent = "MANTRA") {
        $result = false;
        $axml = array();
        $rootkeytag = 'response';
        $callmethod = strtoupper($callmethod);

        if (empty($endpoint)) {
            $response = array('status' => 0, 'code' => 10001, 'message' => 'Empty URL/EndPoint', 'data' => '');
            if ($xmlformat) {
                $result = self::setArray2XML('response', $response);
            } else {
                $result = array('response' => $response);
            }
            return $result;
        }
        $endpoint .= substr($endpoint, -1) == '/' ? '' : '/';

        $rest_pars = '';
        $par = array();
        if ($callmethod == 'REST' && !empty($parameter)) {
            $apar = array();
            foreach ($parameter as $key => $value) {
                $apar[$key] = urlencode($value);
            }
            $rest_pars = http_build_query($apar);
        }

        if ($callmethod == 'RESTFULL' && !empty($parameter)) {
            $apar = array();
            foreach ($parameter as $key => $value) {
                $apar[$key] = urlencode($value);
            }
            $rest_pars = implode('/', $apar);
        }

        if ($callmethod == 'RESTFULLPAR' && !empty($parameter)) {
            $rest_pars = "";
            foreach ($parameter as $key => $value) {
                $rest_pars .= '/' . $key . '/' . urlencode($value);
            }
            $rest_pars = substr($rest_pars, 1);
        }

        if (in_array($callmethod, array('GET', 'POST')) && !empty($parameter)) {
            $par = http_build_query($parameter);
        }

        //susun uri
        $uri = $endpoint;
        if (!empty($operation)) {
            $uri .= substr($uri, -1) == '/' ? $operation : '/' . $operation;
        }
        if (!empty($rest_pars)) { //tambah parameter untuk method REST, RESTFULL, dan RESTFULLPAR
            $uri .= substr($uri, -1) == '/' ? $rest_pars : '/' . $rest_pars;
        }
        if (!empty($par) && $callmethod == 'GET') { //tambah parameter untuk method GET
            $uri = substr($uri, -1) == '/' ? substr($uri, 0, -1) : $uri;
            $uri .= strpos($uri, "?") === false ? "?" . $par : "&" . $par;
        }


        if (empty($uri)) {
            $response = array('status' => 0, 'code' => 10002, 'message' => 'Empty method', 'data' => '');
            if ($xmlformat) {
                $result = self::setArray2XML('response', $response);
            } else {
                $result = array('response' => $response);
            }
        } else {
            $ch = curl_init();
            // URL target koneksi
            curl_setopt($ch, CURLOPT_URL, $uri);
            if ($agent != '') {
                curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            }
            // Output dengan header=true hanya untuk meta document xml/json
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            if ($accesskey != '') {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("AccessKey:" . $accesskey));
            }
            // Mendapatkan tanggapan
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, FALSE);
            curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);

            // Menggunakan metode HTTP GET
            if (in_array($callmethod, array('GET', 'REST', 'RESTFULL', 'RESTFULLPAR'))) {
                curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
            }

            // Menggunakan metode HTTP POST 
            if ($callmethod == 'POST') {
                curl_setopt($ch, CURLOPT_POST, TRUE);
                // Sisipkan parameter    
                curl_setopt($ch, CURLOPT_POSTFIELDS, $par);
            }


            // Buka koneksi dan dapatkan tanggapan
            $content = curl_exec($ch);
            $errno = curl_errno($ch);
            $errmsg = curl_error($ch);
//            return curl_getinfo($ch);
            // Periksa kesalahan
            if ($errno != 0) {
                $response = array('status' => 0, 'code' => $errno, 'message' => $errmsg, 'data' => '');
                $axml = array($rootkeytag => $response);
            } else {
                //if(APP_ENC) $content=dec64data($content);
                if (substr($content, 0, 5) == '<?xml' || substr($content, 0, 5) == '<ows:') {
                    $acontent = setXML2Array($content);
                    if (!isset($acontent[$rootkeytag]['status'])) {
                        $response = array('status' => 1, 'code' => 200, 'message' => 'OK', 'data' => $acontent);
                        $axml = array($rootkeytag => $response);
                    } else {
                        $axml = $acontent;
                    }
                } elseif (substr($content, 0, 1) == '{' && substr($content, -1) == '}') {
                    $acontent = json_decode($content, true);
                    if (!isset($acontent[$rootkeytag]['status'])) {
                        $response = array('status' => 1, 'code' => 200, 'message' => 'OK', 'data' => $acontent);
                        $axml = array($rootkeytag => $response);
                    } else {
                        $axml = $acontent;
                    }
                } else {
                    $acontent = unserialize($content);
                    if (!$acontent) {
                        $acontent = $content;
                    }
                    if (!isset($acontent[$rootkeytag]['status'])) {
                        $response = array('status' => 1, 'code' => 200, 'message' => 'OK', 'data' => $acontent);
                        $axml = array($rootkeytag => $response);
                    } else {
                        $axml = $acontent;
                    }
                }
            }

            curl_close($ch);
        }

        if (!empty($axml)) {
            if ($xmlformat) {
                try {
                    $result = setArray2XML($rootkeytag, $axml[$rootkeytag]);
                } catch (exception $e) {
                    $response = array('status' => 0, 'code' => 20003, 'message' => $e->getMessage(), 'data' => '');
                    $axml = array($rootkeytag => $response);
                    $result = setArray2XML($rootkeytag, $axml[$rootkeytag]);
                }
            } else {
                $result = $axml;
            }
        }

        return $result;
    }

    public static function setXML2Array($xmldata) {
        return comp\XML2Array::createArray($xmldata);
    }

    public static function setArray2XML($nodename, $data) {
        $xml = comp\Array2XML::createXML($nodename, $data);
        return $xml->saveXML();
    }

}
