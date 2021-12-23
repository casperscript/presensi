<?php

namespace app\adminsistem\model;

use system;
use comp;

class servicemain extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    public function regUserAPI($param) {
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE username = ? AND grup_pengguna_kd = "KDGRUP05"', [$param['username']]);
        if ($data['count'] > 0) {
            $dateNow = date('Y-m-d H:i:s');
            $tempAPI['username'] = $param['username'];
            $tempAPI['last_login'] = $dateNow;
            $tempAPI['expired'] = date('Y-m-d H:i:s', (strtotime($dateNow) + 3600));
            $this->save_update('tb_penggunaAPI', $tempAPI);
            return ['status' => 'success'] + $tempAPI;
        } else {
            return ['status' => 'failed'];
        }
    }

    public function cekUserAPI($param) {
        $dateNow = date('Y-m-d H:i:s');
        $idKey = [$param['username'], $param['last_login'], $dateNow];
        $result = $this->getData('SELECT * FROM tb_penggunaAPI WHERE username = ? AND last_login = ? AND expired >= ?', $idKey);
        return $result;
    }

    public function cekLogin($data) {
        set_time_limit(0);
        $username = $data['username'];
        $password = comp\FUNC::encryptor($data['password']);
        $query = "SELECT * FROM tb_pengguna WHERE (username = ?) AND (password = ?)";
        $dataArr = $this->getData($query, array($username, $password));
        return $dataArr['count'];
    }

    public function login($input) {
        set_time_limit(0);
        session_regenerate_id();
        $username = $input['username'];
        $session_id = comp\FUNC::encryptor(session_id());
        $query = "SELECT * FROM tb_pengguna WHERE (username = ?)";
        $data = $this->getData($query, array($username));

        $result['username'] = $data['value'][0]['username'];
        $result['grup_pengguna_kd'] = $data['value'][0]['grup_pengguna_kd'];
        $result['session_id'] = $session_id;
        $result['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        $this->save_update('tb_pengguna', $result);
        $this->createCookie($session_id);

        return $result;
    }

    public function logout($username) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE (username = ?)', array($username));
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

        if (empty($_SESSION_LOGIN)) {
            return false;
        }

        if (!empty($_SESSION_LOGIN) && $_SESSION_LOGIN["grup_pengguna_kd"] === "KDGRUP99") {
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
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE (session_id = ?)', array($session_id));
        if ($data['count'] > 0) {
            $dataUser = $data['value'][0];
            return array('status' => true, 'data' => $dataUser);
        } else {
            return array('status' => false);
        }
    }

    public function cekCookieDB($session_id, $user_agent) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE (session_id = ?) AND (user_agent = ?)', array($session_id, $user_agent));
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

    /*     * ***************** Master Database ******************** */

    public function getDataKrit($con, $tabel, $kriteria, $unset = array(), $cols = array()) {
        parent::setConnection($con);
        $q_cari = '';
        $idKey = array();
        foreach ($unset as $key => $val) {
            unset($kriteria[$val]);
        }

        $field = $this->getTabel($tabel);
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }
        $col = count($cols) > 0 ? join(',', $cols) : '*';

        $data = $this->getData('SELECT ' . $col . ' FROM ' . $tabel . ' WHERE 1 ' . $q_cari, $idKey);
        // $data['value'][0]['query'] = $data['query'];
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $field;
        }
    }

    public function getArrDataKrit($con, $tabel, $kriteria, $unset = array(), $cols = array()) {
        parent::setConnection($con);
        $q_cari = '';
        $idKey = array();
        foreach ($unset as $key => $val) {
            unset($kriteria[$val]);
        }

        $field = $this->getTabel($tabel);
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }
        $col = count($cols) > 0 ? join(',', $cols) : '*';

        $data = $this->getData('SELECT ' . $col . ' FROM ' . $tabel . ' WHERE 1 ' . $q_cari, $idKey);
        if ($data['count'] > 0) {
            return $data['value'];
        } else {
            return [];
        }
    }

    public function getTabelKrit($con, $tabel, $kriteria, $sort = '') {
        parent::setConnection($con);
        $q_cari = '';
        $idKey = array();
        $order = (!empty($sort)) ? 'ORDER BY ' . $sort : '';
        $page = (!empty($kriteria['page'])) ? $kriteria['page'] : 1;
        $batas = (!empty($kriteria['batas'])) ? $kriteria['batas'] : 10;
        $posisi = ($page - 1) * $batas;

        $field = $this->getTabel($tabel);
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }

        if (!empty($kriteria['cari']['value'])) {
            $q_cari .= 'AND (' . $kriteria['cari']['key'] . ' LIKE "%' . $kriteria['cari']['value'] . '%")';
            $page = 1;
        }

        $q_data = 'SELECT * FROM ' . $tabel . ' WHERE 1 ' . $q_cari . $order;
        $j_data = 'SELECT COUNT(*) AS jumlah FROM ' . $tabel . ' WHERE 1 ' . $q_cari;

        $jmlData = $this->getData($j_data, $idKey);
        $dataArr = $this->getData($q_data . ' LIMIT ' . $posisi . ', ' . $batas, $idKey);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = ($jmlData['count'] > 0) ? $jmlData['value'][0]['jumlah'] : 0;
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        return $result;
    }

    public function getPilKrit($con, $tabel, $kriteria, $opsi) {
        parent::setConnection($con);
        $q_cari = '';
        $idKey = array();
        $field = $this->getTabel($tabel);
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }

        $data = $this->getData('SELECT ' . $opsi['value'] . ' AS colValue, ' . $opsi['key'] . ' AS colKey FROM ' . $tabel . ' WHERE 1 ' . $q_cari, $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['colKey']] = $kol['colValue'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    public function getDataSetting($var) {
        parent::setConnection('db_presensi');
        $data = $this->getData('SELECT * FROM tb_setting WHERE `variable` = ?', [$var]);
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_setting');
        }
    }

    /*     * ***************** Get Pilihan ********************** */
}

?>
