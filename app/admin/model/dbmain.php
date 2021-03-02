<?php

namespace app\admin\model;

use system;

class dbmain extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('dbmain');
    }
    
}

?>
