<?php

namespace app\pns\model;

use system;

class presensi_service_moderasi extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    /******************* Get Data ********************** */
    public function getDataMesin($id) {
        $data = $this->getData('SELECT * FROM tb_mesin WHERE (id_mesin = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_mesin');
        }
    }
    
    public function getDataLastUpdate($id) {
        $data = $this->getData('SELECT MAX(tanggal_log_presensi) AS last_update FROM tb_log_presensi WHERE (id_mesin = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_log_presensi');
        }
    }
    
    /******************* Get Pilihan ********************** */
    public function getPilKelMesin($cari = array()) {   // daftar kelompok lokasi kerja
        $field = $this->getTabel('tb_kelompok_mesin');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($cari[$key])) {
                $q_cari = 'AND (' . $key . ' = ?) ';
                array_push($idKey, $cari[$key]);
            }
        }
        $data = $this->getData('SELECT * FROM tb_kelompok_mesin WHERE 1 ' . $q_cari . ' ORDER BY orderColumn', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['id_kelompok_mesin']] = $kol['nama_kelompok'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }


    /****************** Get Tabel ************************** */

    public function getTabelMesin($data) {
        $idKey = array();
        $page = $data['page'];
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $q_cari = 'WHERE 1 ';
        if (!empty($data['name_mesin'])) {
            $q_cari .= 'AND (name_mesin LIKE ?) ';
            array_push($idKey, '%' . $data['name_mesin'] . '%');
        }
        if (!empty($data['id_kelompok_mesin'])) {
            $q_cari .= 'AND (id_kelompok_mesin = ?) ';
            array_push($idKey, $data['id_kelompok_mesin']);
        }
        $query = 'SELECT tb_mesin.*, max(tb_log_presensi.`tanggal_log_presensi`) AS last_record FROM tb_mesin '
                . 'LEFT JOIN tb_log_presensi ON tb_mesin.id_mesin = tb_log_presensi.id_mesin ' . $q_cari
                . 'GROUP BY tb_mesin.id_mesin';
        $j_query = 'SELECT COUNT(id_mesin) AS jumlah FROM tb_mesin ' . $q_cari;

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

    public function getTabelRecordPersonil($data) {
        $idKey = array($data['pin_absen'], $data['sdate'], $data['edate']);
        $page = (!empty($data['pageDetail'])) ? $data['pageDetail'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $query = 'SELECT * FROM tb_log_presensi '
                . 'WHERE (pin_absen = ?) AND (tanggal_log_presensi BETWEEN ? AND ?) '
                . 'ORDER BY tanggal_log_presensi DESC, jam_log_presensi DESC';
        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM tb_log_presensi '
                . 'WHERE (pin_absen = ?) AND (tanggal_log_presensi BETWEEN ? AND ?)';
        
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

    //esti 6-12-17
    public function getDataKetidakhadiran($id) {
        $query = "SELECT * FROM tb_presensi WHERE (pin_absen = ?) AND (jam_masuk_presensi='00:00:00' OR jam_pulang_presensi='00:00:00')";
        $dataArr = $this->getData($query , array($id));
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        return $result;
    }
    
}
