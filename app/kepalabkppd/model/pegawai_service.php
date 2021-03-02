<?php

namespace app\kepalabkppd\model;

use system;

class pegawai_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_pegawai');
    }

    public function getDataSatker($id) {
        $query = 'SELECT * FROM tref_lokasi_kerja WHERE (kdlokasi = ?)';
        $result = $this->getData($query, array($id));
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('tref_lokasi_kerja');
        }
    }
}