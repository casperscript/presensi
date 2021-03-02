<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpresensi;
use app\adminsistem\model\servicemasterpegawai;
use system;
use comp;

class cetakuser extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemasterpresensi = new servicemasterpresensi();
        $this->servicemasterpegawai = new servicemasterpegawai();
        $this->servicemain = new servicemain();
    }

    protected function index() {
        
            
            $penggunapegawai = $this->servicemasterpegawai->getDataUserCetak();
            $penggunapresensi = $this->servicemasterpresensi->getDataUserCetak();
            
            
            
            
            //$username = $penggunapresensi['username'];
//            $x = array();
//            
//            foreach ($penggunapresensi as $value) {
//                
//                $x[] = $value['username'];
//                $x[] = $value['password'];
//                $x[] = $value['nipbaru'];
//                
//                //$penggunapegawai = $this->servicemasterpegawai->getDataUserCetak();
//                
//            }
            
//            $x = array();
//
//            foreach($penggunapegawai as $index => $val) {
//                if(!array_key_exists($index, $partIds)) {
//                    throw OutOfBoundsException();
//                }
//
//                $x[] = array(
//                    'username'  => $val,
//                    'password' => $partIds[$index]
//                );
//            }
            
            
            $data = [];

            foreach ($penggunapresensi as $presensi) {
                foreach ($penggunapegawai as $peg) {
                    if ($presensi["nipbaru"] === $peg["nipbaru"]) {
                        $presensi['password'] = comp\FUNC::decryptor($presensi['password']);
                        $data[] = array_merge($presensi, $peg);
                        break;
                    }
                }
            }
            
            
            //$data = array_merge($penggunapresensi, $penggunapegawai);
            //print_r($penggunapresensi+$penggunapegawai);
            //print_r($data);
            //echo comp\FUNC::showPre($data);
            header('Content-Type: application/json');
            echo json_encode($data);
    }

}

?>
