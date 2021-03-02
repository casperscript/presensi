<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\pegawai_service;
use system;
use comp;

class integration extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->pegawai_service = new pegawai_service();
    }

    protected function migrate($data) {
        $exp = explode('|', $data);
        if ($exp[0] == date('d') && !empty($exp[1]) && !empty($exp[2])) {
            $tbsource = $exp[1];
            $tbtarget = $exp[2];
            $input = $this->pegawai_service->getData('SELECT * FROM ' . $tbsource);
            $result = self::insertData($tbtarget, $input);
            $msg = ($result['error']) ? 'Data pada tabel ' . $exp[1] . ' telah dimigrasi ke ' . $exp[2] : 'Terjadi kesalahan ketika migrasi';
        } else {
            $msg = 'Anda tidak memiliki hak akses!';
        }
        echo $msg;
    }

    protected function insertData($tb, $input = []) {
        $result = [];
        $field = $this->pegawai_service->getTabel($tb);
        foreach ($input['value'] as $val) :
            $data = [];
            foreach ($field as $keyf => $valf) :
                if (isset($val[$keyf])) :
                    $data[$keyf] = $val[$keyf];
                else :
                    $data[$keyf] = $valf;
                endif;
            endforeach;
            $result = $this->pegawai_service->save_update($tb, $data);
        endforeach;
        return $result;
    }

}
