<?php

namespace app\adminopd\model;

use system;
use comp;

class servicemain extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }
    
    public function cekLogin($data) {
        set_time_limit(0);
        $username = $data['username'];
        $password = comp\FUNC::encryptor($data['password']);
        $query = "SELECT * FROM tb_pengguna WHERE (username = ?) AND (password = ?) AND grup_pengguna_kd = 'KDGRUP01'";
        $dataArr = $this->getData($query, array($username, $password));
        return $dataArr['count'];
        //print_r($dataArr);
        //exit();
    }
    
    public function getMenu($active) {
        $data = $this->getData('SELECT * FROM tb_menu WHERE level = ? ORDER BY parent, urut', ['adminopd']);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $val) {
                if (empty($val['parent'])) {
                    $val['active'] = ($active == $val['path']) ? true : false;
                    $menu[$val['id']] = $val;
                } else {
                    $p = $val['parent'];
                    $id = $val['id'];
                    
                    //menu aktif
                    $active_menu = ($active == $val['path']) ? true : false;
                    $val['active'] = $active_menu;
                    
                    $menu[$p]['active'] = isset($menu[$p]['active']) && ($menu[$p]['active'] == true) ? true : $active_menu;
//                    $menu[$p]['active'] = isset($menu[$p]['active']) ? true : $active_menu;
//                    $menu[$p]['cek'][] = $val['path'] . ' || ' . $active . ' || ' . $active_menu . ' || ' . $menu[$p]['active'] . ' || ' . $p;
                    
                    $menu[$p]['sub'][$id] = $val;
                }
            }
            return $menu;
        } else {
            return [];
        }
    }
    
    public function login($input) {
        set_time_limit(0);
        session_regenerate_id();
        $username = $input['username'];
        $session_id = comp\FUNC::encryptor(session_id());
        $query = "SELECT * FROM tb_pengguna WHERE (username = ?) AND grup_pengguna_kd = 'KDGRUP01'";
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
    
    public function menuModul(){
        
    }
    

}

?>
