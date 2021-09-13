<?php

namespace app\adminsistem\model;

use system;

class webadapter extends system\Model {

    public function __construct() {
        parent::__construct();
    }
    public function callAPI($endpoint, $operation, $accesskey = array(), $parameter = array()) {
        if (empty($endpoint)) {
            return array('status'=>0,'code'=>20001,'message'=>'URL/EndPoint tidak terdefinisi (kosong)','data'=>'');
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint . $operation);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(key($accesskey) . ':' . current($accesskey)));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);
            curl_close($ch);
            return json_decode($output, true);
        }
    }

}