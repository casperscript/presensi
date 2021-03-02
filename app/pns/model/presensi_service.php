<?php

namespace app\pns\model;

use system;

class presensi_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    /*     * ***************** Get Data ********************** */

    public function getDataMesin($id) {
        $data = $this->getData('SELECT * FROM tb_mesin WHERE (id_mesin = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_mesin');
        }
    }

    /*     * ***************** Get Pilihan ********************** */
    /*     * ***************** Master ******************** */

    public function getDataKrit($tabel, $kriteria, $unset = array()) {
        $q_cari = '';
        $idKey = array();
        foreach ($unset as $key => $val) {
            unset($kriteria[$val]);
        }

        $field = $this->getTabel($tabel);
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }

        $data = $this->getData('SELECT * FROM ' . $tabel . ' WHERE 1 ' . $q_cari, $idKey);
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $field;
        }
    }

    public function getTabelKrit($tabel, $kriteria, $sort = '') {
        $q_cari = '';
        $idKey = array();
        $order = (!empty($sort)) ? 'ORDER BY ' . $sort : '';
        $page = (!empty($kriteria['page'])) ? $kriteria['page'] : 1;
        $batas = (!empty($kriteria['batas'])) ? $kriteria['batas'] : 10;
        $posisi = ($page - 1) * $batas;

        $field = $this->getTabel($tabel);
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }

        $q_data = 'SELECT * FROM ' . $tabel . ' WHERE 1 ' . $q_cari . $order;
        $j_data = 'SELECT COUNT(*) AS jumlah FROM ' . $tabel . ' WHERE 1 ' . $q_cari;

        $jmlData = $this->getData($j_data, $idKey);
        $dataArr = $this->getData($q_data . ' LIMIT ' . $posisi . ', ' . $batas, $idKey);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = ($jmlData['count'] > 0) ? $jmlData['value'][0]['jumlah'] : 0;
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        return $result;
    }

    public function getPilKrit($tabel, $kriteria, $opsi) {
        $q_cari = '';
        $idKey = array();
        $field = $this->getTabel($tabel);
		$order = isset($kriteria['order']) ? ' ORDER BY ' . $kriteria['order'] : '';
        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }

        $data = $this->getData('SELECT * FROM ' . $tabel . ' WHERE 1 ' . $q_cari . $order, $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol[$opsi['key']]] = $kol[$opsi['value']];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    /*     * **************** Get Tabel ************************** */

    public function getArrayShift($input = array()) {
        $idKey = array();
        foreach ($input as $key => $val) {
            array_push($idKey, $key);
        }
        $inArray = implode(',', array_fill(0, count($input), '?'));
        $result = array();
        $data = $this->getData('SELECT * FROM tb_shift WHERE id_shift IN (' . $inArray . ')', $idKey);
        foreach ($data['value'] as $kol) {
            $result[$kol['id_shift']] = $kol['nama_shift'];
        }
        return $result;
    }

    public function getTabelRecordPersonil($data) {
        $idKey = array($data['pin_absen'], $data['sdate'], $data['edate']);
        $page = (!empty($data['page'])) ? $data['page'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $query = 'SELECT  jadwal.*, presensi.* FROM tb_log_presensi presensi '
                . 'LEFT JOIN view_jadwal jadwal ON presensi.`pin_absen` = jadwal.`pin_absen` AND presensi.`tanggal_log_presensi` = jadwal.`tanggal` '
                . 'WHERE (presensi.`pin_absen` = ?) AND (tanggal_log_presensi BETWEEN ? AND ?) '
                . 'ORDER BY tanggal_log_presensi DESC, jam_log_presensi DESC';
        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM tb_log_presensi '
                . 'WHERE (pin_absen = ?) AND (tanggal_log_presensi BETWEEN ? AND ?)';

        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($j_query, $idKey);
        $dataArr = $this->getData($query . '  LIMIT ' . $posisi . ', ' . $batas, $idKey);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = ($jmlData['count'] > 0) ? $jmlData['value'][0]['jumlah'] : 0;
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
//        $result['query'] = '';
        return $result;
    }

    /*     * *********** Get Data Array Key From Array Value ************** */
    public function getArrayRecord($input = array()) {
        $idKey = array($input['sdate'], $input['edate']);
        foreach ($input['pin_absen'] as $key => $val) {
            array_push($idKey, $key);
        }
        $inArray = implode(', ', array_fill(0, count($input['pin_absen']), ' ? '));
        $result = array();
        $data = $this->getData('SELECT *, IF (stsHadir = 0, MIN(jam), MAX(jam)) AS jam FROM view_lap_harian '
                . 'WHERE (tanggal BETWEEN ? AND ?) AND pin_absen IN (' . $inArray . ') '
                . 'GROUP BY pin_absen, tanggal, stsHadir', $idKey);
        foreach ($data['value'] as $kol) {
            $result[$kol['pin_absen']][$kol['tanggal']]['jamkerja'] = $kol['jamkerja'];
            $result[$kol['pin_absen']][$kol['tanggal']]['jam'] = $kol['jam'];
            $result[$kol['pin_absen']][$kol['tanggal']]['verifikasi'] = $kol['verifikasi'];
            $result[$kol['pin_absen']][$kol['tanggal']]['id_mesin'] = $kol['id_mesin'];

            if (isset($result[$kol['pin_absen']][$kol['tanggal']]['masuk']) && $kol['stsHadir'] == '0' && $result[$kol['pin_absen']][$kol['tanggal']]['masuk'] < $kol['jam']) {
                $result[$kol['pin_absen']][$kol['tanggal']]['telat_masuk'] = $kol['telat'];
                $result[$kol['pin_absen']][$kol['tanggal']]['masuk'] = $kol['jam'];
            } elseif (!isset($result[$kol['pin_absen']][$kol['tanggal']]['masuk']) && $kol['stsHadir'] == '0') {
                $result[$kol['pin_absen']][$kol['tanggal']]['telat_masuk'] = $kol['telat'];
                $result[$kol['pin_absen']][$kol['tanggal']]['masuk'] = $kol['jam'];
            }
            if (isset($result[$kol['pin_absen']][$kol['tanggal']]['pulang']) && $kol['stsHadir'] == '1' && $result[$kol['pin_absen']][$kol['tanggal']]['pulang'] > $kol['jam']) {
                $result[$kol['pin_absen']][$kol['tanggal']]['telat_pulang'] = $kol['telat'];
                $result[$kol['pin_absen']][$kol['tanggal']]['pulang'] = $kol['jam'];
            } elseif (!isset($result[$kol['pin_absen']][$kol['tanggal']]['pulang']) && $kol['stsHadir'] == '1') {
                $result[$kol['pin_absen']][$kol['tanggal']]['telat_pulang'] = $kol['telat'];
                $result[$kol['pin_absen']][$kol['tanggal']]['pulang'] = $kol['jam'];
            }
        }
        $result['query'] = $data['query'];
        return $result;
    }

}
