<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\backup_service;
use system;
use comp;

class integrasi extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain;
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->backup_service = new backup_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    public function tesInt() {
//        parent::setConnection('db_backup');
        $data = $this->backup_service->getData64();
        $result = false;
        if ($data['count'] > 0) {
            $result = $this->backup_service->insertData62($data['value']);
        }
        comp\FUNC::showPre($result);
    }

}
