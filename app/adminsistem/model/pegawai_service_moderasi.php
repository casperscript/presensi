<?php

namespace app\adminsistem\model;

use system;

class pegawai_service_moderasi extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_pegawai');
    }

    /*     * ***************** Get Pilihan ********************** */

    public function getPilKelSatker($cari = array()) {   // daftar kelompok lokasi kerja
        $field = $this->getTabel('tref_kelompok_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($cari[$key])) {
                $q_cari = 'AND (' . $key . ' = ?) ';
                array_push($idKey, $cari[$key]);
            }
        }
        $data = $this->getData('SELECT * FROM tref_kelompok_lokasi_kerja WHERE 1 ' . $q_cari . ' ORDER BY kd_kelompok_lokasi_kerja', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['kd_kelompok_lokasi_kerja']] = $kol['nama_kelompok_lokasi_kerja'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    public function getPilSatker($cari = '') {  // daftar lokasi kerja
        $field = $this->getTabel('tref_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($cari[$key])) {
                $q_cari = 'AND (' . $key . ' = ?) ';
                array_push($idKey, $cari[$key]);
            }
        }
        $data = $this->getData('SELECT * FROM tref_lokasi_kerja WHERE 1 ' . $q_cari . ' ORDER BY kelompok', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['id_kelompok']] = $kol['kelompok'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    /****************** Get Data ************************** */
    public function getDataPersonil($id) {
        $query = 'SELECT * FROM view_presensi_personal WHERE (pin_absen = ?)';
        $result = $this->getData($query, array($id));
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('view_presensi_personal');
        }
    }
    
    
    /****************** Get Tabel ************************** */

    public function getTabelPersonil($data) {
        $idKey = array();
        $page = $data['page'];
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $q_cari = 'WHERE 1 ';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (kdlokasi = ?) ';
            array_push($idKey, $data['kdlokasi']);
        }
        $query = 'SELECT * FROM view_presensi_personal ' . $q_cari;
        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM view_presensi_personal ' . $q_cari;

        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($j_query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ', ' . $batas, $idKey);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = ($jmlData['count'] > 0) ? $jmlData['value'][0]['jumlah'] : 0;
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
//        $result['query'] = '';
        return $result;
    }

}
