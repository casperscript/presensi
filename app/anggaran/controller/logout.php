<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use system;

class logout extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
    }

    protected function index() {
        $this->login = $this->getSession('SESSION_LOGIN');
        $this->servicemain->logout($this->login['id_login']);
        $this->desSession();
        $this->servicemain->removeCookie();
        $this->redirect($this->link($this->getProject() . 'login'));
    }

}

?>
