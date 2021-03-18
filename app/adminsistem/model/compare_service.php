<?php

namespace app\adminsistem\model;

use system;
use comp;

class compare_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

}
