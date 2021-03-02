<?php

namespace app\kepalaopd\model;

use system;

class servicemasterpegawai extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_pegawai');
    }
            
    public function getDataPegawaiPresensi($id) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM view_presensi_personal WHERE (nipbaru = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        }
        else {
            return array('nipbaru' => '-', 'nama_personil' => '-', 'pin_absen' => '-');
        }
    }
    
    public function getDataFotoPegawai($id) {
        set_time_limit(0);
        $data = $this->getData('SELECT foto_pegawai FROM view_presensi_personal WHERE (nipbaru = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        }
        else {
            return array('foto_pegawai' => '-');
        }
    }

}

?>
