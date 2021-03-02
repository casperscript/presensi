<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use system;

class logout extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
    }

    protected function index() {
        $this->login = $this->getSession('SESSION_LOGIN');
        $this->servicemain->logout($this->login['username']);
        $this->desSession();
        $this->servicemain->removeCookie();
        $this->redirect($this->link($this->getProject() . 'login'));
    }

}

?>
