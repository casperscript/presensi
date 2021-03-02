<?php

namespace app\pns\model;

use system;
use comp;

class pegawai_service_moderasi extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_pegawai');
    }

    /*     * ***************** Get Pilihan ********************** */

    //login
    public function cekLogin($data) {
        set_time_limit(0);
        $username = $data['username'];
        $password = md5($data['password']);
        $query = 'SELECT * FROM texisting_kepegawaian WHERE (nipbaru = ?) AND (pns_password = ?)';
        $dataArr = $this->getData($query, array($username, $password));
        return $dataArr['count'];
    }
    
    public function login($input) {
        set_time_limit(0);
        session_regenerate_id();
        $username = $input['username'];
        $session_id = comp\FUNC::encryptor(session_id());
        $query = 'SELECT * FROM texisting_kepegawaian WHERE (nipbaru = ?)';
        $data = $this->getData($query, array($username));
        
        $result['pin_absen'] = $data['value'][0]['pin_absen'];
        $result['nipbaru'] = $data['value'][0]['nipbaru'];
        $result['session_id'] = $session_id;
        
        $this->save_update('texisting_kepegawaian', $result);
        $this->createCookie($session_id);
        
        return $result;
    }
    
    public function logout($username) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM texisting_kepegawaian WHERE (nipbaru = ?)', array($username));
        $result['jmlData'] = $data['count'];
        if ($data['count'] > 0) {
            $result['dataUser'] = $data['value'][0];
            $result['dataUser']['session_login'] = '';
            $result['dataUser']['agent_login'] = '';
            $this->save_update('tb_login', $result['dataUser']);
            $this->removeCookie();
        }
        return $result;
    }
    
    
    // UNTUK FUNGSI LOGIN SESI + COOKIE
    public function cekSession() {
        set_time_limit(0);
        $_SESSION_LOGIN = $this->getSession('SESSION_LOGIN');
        if (!empty($_SESSION_LOGIN)) {
            return $this->cekSessionDB($_SESSION_LOGIN['session_id']);
        } else {
            $cookie = $this->cookie;
            if (!empty($_COOKIE[$cookie])) {
                return $this->cekCookieDB($_COOKIE[$cookie], $_SERVER['HTTP_USER_AGENT']);
            } else
                return array('status' => false);
        }
    }

    public function cekSessionDB($session_id) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM texisting_kepegawaian WHERE (session_id = ?)', array($session_id));
        if ($data['count'] > 0) {
            $dataUser = $data['value'][0];
            return array('status' => true, 'data' => $dataUser);
        } else {
            return array('status' => false);
        }
    }

    public function cekCookieDB($session_id) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM texisting_kepegawaian WHERE (session_id = ?)', array($session_id));
        if ($data['count'] > 0) {
            $dataUser = $data['value'][0];
            return array('status' => true, 'data' => $dataUser);
        } else {
            return array('status' => false);
        }
    }

    public function createCookie($session_id) {
        $cookie = $this->cookie;
        setcookie($cookie, $session_id, time() + COOKIE_EXP, '/');
    }

    public function removeCookie() {
        $cookie = $this->cookie;
        unset($_COOKIE[$cookie]);
        setcookie($cookie, '', time() - COOKIE_EXP, '/');
    }
    //..login..

    public function getPilKelSatker($cari = array()) {   // daftar kelompok lokasi kerja
        $field = $this->getTabel('tref_kelompok_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($cari[$key])) {
                $q_cari = 'AND (' . $key . ' = ?) ';
                array_push($idKey, $cari[$key]);
            }
        }
        $data = $this->getData('SELECT * FROM tref_kelompok_lokasi_kerja WHERE 1 ' . $q_cari . ' ORDER BY kd_kelompok_lokasi_kerja', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['kd_kelompok_lokasi_kerja']] = $kol['nama_kelompok_lokasi_kerja'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    public function getPilSatker($cari = '') {  // daftar lokasi kerja
        $field = $this->getTabel('tref_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($cari[$key])) {
                $q_cari = 'AND (' . $key . ' = ?) ';
                array_push($idKey, $cari[$key]);
            }
        }
        $data = $this->getData('SELECT * FROM tref_lokasi_kerja WHERE 1 ' . $q_cari . ' ORDER BY kelompok', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['id_kelompok']] = $kol['kelompok'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    /****************** Get Data ************************** */
    public function getDataPersonil($id) {
        $query = 'SELECT * FROM view_presensi_personal WHERE (pin_absen = ?)';
        $result = $this->getData($query, array($id));
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('view_presensi_personal');
        }
    }
    
    
    /****************** Get Tabel ************************** */

    public function getTabelPersonil($data) {
        $idKey = array();
        $page = $data['page'];
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $q_cari = 'WHERE 1 ';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (kdlokasi = ?) ';
            array_push($idKey, $data['kdlokasi']);
        }
        $query = 'SELECT * FROM view_presensi_personal ' . $q_cari;
        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM view_presensi_personal ' . $q_cari;

        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($j_query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ', ' . $batas, $idKey);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = ($jmlData['count'] > 0) ? $jmlData['value'][0]['jumlah'] : 0;
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
//        $result['query'] = '';
        return $result;
    }

}
