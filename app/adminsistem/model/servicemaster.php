<?php

namespace app\adminsistem\model;

use system;
use comp;

class servicemaster extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }
    
    
    // START MASTER JENIS MODERASI
    public function getPilihanSatuanPotongan() {
        return array('Hari Kerja' => 'Hari Kerja', 'Bulan Kerja' => 'Bulan Kerja');
    }
    
    public function getTabelJenisModerasi($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_jenis_moderasi LIKE ?) || (nama_jenis_moderasi LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jenis_moderasi' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanJenisModerasi() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jenis_moderasi', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_jenis_moderasi']] = $kol['nama_jenis_moderasi'];
        }
        return $data;
    }
    public function cekPrimaryKodeJenisModerasi($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_jenis_moderasi WHERE (kd_jenis_moderasi = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataJenisModerasiForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jenis_moderasi WHERE (kd_jenis_moderasi = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jenis_moderasi');
        }
    }
    // END MASTER JENIS MODERASI
    
    
    // START MASTER JENIS CUTI
    public function getTabelJenisCuti($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_jenis_cuti LIKE ?) || (nama_jenis_cuti LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jenis_cuti' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanJenisCuti() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jenis_cuti', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_jenis_cuti']] = $kol['nama_jenis_cuti'];
        }
        return $data;
    }
    public function cekPrimaryKodeJenisCuti($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_jenis_cuti WHERE (kd_jenis_cuti = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataJenisCutiForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jenis_cuti WHERE (kd_jenis_cuti = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jenis_cuti');
        }
    }
    // END MASTER JENIS CUTI
    
    
    // START MASTER JENIS DINAS
    public function getTabelJenisDinas($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_jenis_dinas LIKE ?) || (nama_jenis_dinas LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jenis_dinas' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanJenisDinas() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jenis_dinas', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_jenis_dinas']] = $kol['nama_jenis_dinas'];
        }
        return $data;
    }
    public function cekPrimaryKodeJenisDinas($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_jenis_dinas WHERE (kd_jenis_dinas = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataJenisDinasForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jenis_dinas WHERE (kd_jenis_dinas = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jenis_dinas');
        }
    }
    // END MASTER JENIS DINAS
    
    
    // START MASTER GRUP PENGGUNA
    public function getTabelGrupPengguna($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_grup_pengguna LIKE ?) || (nama_grup_pengguna LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_grup_pengguna' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanGrupPengguna() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_grup_pengguna', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_grup_pengguna']] = $kol['nama_grup_pengguna'];
        }
        return $data;
    }
    public function cekPrimaryKodeGrupPengguna($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_grup_pengguna WHERE (kd_grup_pengguna = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataGrupPenggunaForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_grup_pengguna WHERE (kd_grup_pengguna = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_grup_pengguna');
        }
    }
    // END MASTER GRUP PENGGUNA
    
    
    // START MASTER PENGGUNA
    public function getTabelPengguna($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((username LIKE ?) || (kd_grup_pengguna LIKE ?) || (nama_grup_pengguna LIKE ?)) ' : '';
        $query = 'SELECT * FROM view_pengguna' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanPengguna() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM view_pengguna', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['username']] = $kol['username'];
        }
        return $data;
    }
    public function cekPrimaryKodePengguna($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_pengguna WHERE (username = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataPenggunaForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE (username = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_pengguna');
        }
    }
    // END MASTER PENGGUNA
    
    
    public function cekLogin($data) {
        set_time_limit(0);
        $username = $data['username'];
        $password = comp\FUNC::encryptor($data['password']);
        $query = 'SELECT * FROM tb_user WHERE (login_id = ?) AND (password_user = ?)';
        $dataArr = $this->getData($query, array($username, $password));
        return $dataArr['count'];
    }
    
    public function login($input) {
        set_time_limit(0);
        session_regenerate_id();
        $username = $input['username'];
        $session_id = comp\FUNC::encryptor(session_id());
        $query = 'SELECT * FROM tb_user WHERE (login_id = ?)';
        $data = $this->getData($query, array($username));
        
        $result['id_login'] = $data['value'][0]['login_id'];
        $result['session_login'] = $session_id;
        $result['agent_login'] = $_SERVER['HTTP_USER_AGENT'];
        
        $this->save_update('tb_login', $result);
        $this->createCookie($session_id);
        
        return $result;
    }
    
    public function logout($username) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_login WHERE (id_login = ?)', array($username));
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
            return $this->cekSessionDB($_SESSION_LOGIN['session_login']);
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
        $data = $this->getData('SELECT * FROM tb_login WHERE (session_login = ?)', array($session_id));
        if ($data['count'] > 0) {
            $dataUser = $data['value'][0];
            return array('status' => true, 'data' => $dataUser);
        } else
            return array('status' => false);
    }

    public function cekCookieDB($session_id, $user_agent) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_login WHERE (session_login = ?) AND (agent_login = ?)', array($session_id, $user_agent));
        if ($data['count'] > 0) {
            $dataUser = $data['value'][0];
            return array('status' => true, 'data' => $dataUser);
        } else
            return array('status' => false);
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
    

}

?>
