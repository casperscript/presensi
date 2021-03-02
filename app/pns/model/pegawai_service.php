<?php

namespace app\pns\model;

use system;

class pegawai_service extends system\Model {

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

    public function getPilLokasi($input = array()) {
        $field = $this->getTabel('tref_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($input[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $input[$key]);
            }
        }
        $data = $this->getData('SELECT kdlokasi, singkatan_lokasi FROM tref_lokasi_kerja WHERE 1 ' . $q_cari . ' ORDER BY singkatan_lokasi', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $val) {
                $result[$val['kdlokasi']] = $val['singkatan_lokasi'];
            }
        } else {
            $result[] = '';
        }
        return $result;
    }

    /*     * **************** Get Data ************************** */

    public function getDataPersonil($arrCari = array()) {
        $field = $this->getTabel('view_presensi_personal');
        $idKey = array();
        $q_cari = 'WHERE 1 ';
        foreach ($field as $key => $val) {
            if (isset($arrCari[$key])) {
                $q_cari .= 'AND (' . $key .' = ?) ';
                array_push($idKey, $arrCari[$key]);
            }
        }
        $query = 'SELECT * FROM view_presensi_personal ' . $q_cari;
        $result = $this->getData($query, $idKey);
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('view_presensi_personal');
        }
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

    /*     * **************** Get Tabel ************************** */

    public function getTabelPersonil($data) {
        $idKey = array();
        $page = (!empty($data['page'])) ? $data['page'] : 1;
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

    /************* Get Data Array Key From Array Value ************** */

    public function getArrayPersonal($input) {
        $idKey = array();
        foreach ($input as $key => $val) {
            array_push($idKey, $key);
        }
        $inArray = implode(',', array_fill(0, count($input), '?'));
        $result = array();
        $data = $this->getData('SELECT * FROM view_presensi_personal WHERE pin_absen IN (' . $inArray . ')', $idKey);
        foreach ($data['value'] as $kol) {
            $result[$kol['pin_absen']]['nama'] = $kol['nama_personil'];
            $result[$kol['pin_absen']]['nipbaru'] = $kol['nipbaru'];
            $result[$kol['pin_absen']]['pin_absen'] = $kol['pin_absen'];
        }
        return $result;
    }

}
