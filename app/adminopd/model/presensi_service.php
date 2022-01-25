<?php

namespace app\adminopd\model;

use system;

class presensi_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

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

    public function getPilKrit($tabel, $kriteria, $opsi, $unset = array()) {
        $q_cari = '';
        $idKey = array();
        $field = $this->getTabel($tabel);
        $order = isset($kriteria['order']) ? ' ORDER BY ' . $kriteria['order'] : '';

        if (count($unset) > 0) {
            foreach ($unset as $key => $val) {
                unset($kriteria[$val]);
            }
        }

        foreach ($field as $key => $val) {
            if (isset($kriteria[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $kriteria[$key]);
            }
        }

        $data = $this->getData('SELECT * FROM ' . $tabel . ' WHERE 1 ' . $q_cari . $order, $idKey);
//        $result['count'] = $data['count'];
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol[$opsi['key']]] = $kol[$opsi['value']];
            }
        } else {
            $result = [];
        }
        return $result;
    }

    /*     * ***************** Get Data ********************** */

    public function getSetting($variable) {
        $idKey = [$variable];
        $data = $this->getData('SELECT value FROM tb_setting WHERE ?', $idKey);
        $result = ($data['count'] > 0) ? $data['value'][0] : '';
        return $result;
    }

    public function getDataLastUpdate($id) {
        $data = $this->getData('SELECT MAX(tanggal_log_presensi) AS last_update FROM tb_log_presensi WHERE (id_mesin = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_log_presensi');
        }
    }

    public function checkCrashJadwal($input) {
        $idKey = array($input['pin_absen'], $input['sdate'], $input['edate']);
        $data = $this->getData('SELECT COUNT(id_jadwal) AS jumlah FROM tb_jadwal '
                . 'WHERE (pin_absen = ?) AND ((? BETWEEN sdate AND edate) OR (? BETWEEN sdate AND edate))', $idKey);
        $result['jumlah'] = $data['value'][0]['jumlah'];
        $result['query'] = $data['query'];
        return $result;
    }

    /*     * ***************** Get Pilihan ********************** */

    public function getPilUnitShift() {
        return array('harian' => 'Harian', 'mingguan' => 'Mingguan');
    }

    public function getPilShift($cari = array()) {
        $idKey = array($cari['kdlokasi']);
        $query = 'SELECT * FROM tb_shift WHERE (kdlokasi = ? OR kdlokasi = "system") ORDER BY FIELD(kdlokasi, "system") DESC, nama_shift ASC';
        $data = $this->getData($query, $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['id_shift']] = $kol['nama_shift'];
            }
        } else {
            $result[0] = '==Data Kosong==';
        }
        return $result;
    }

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

    public function getListAuthor($cari = array()) {
        $inArray = '"' . implode('","', array_column($cari, 'author')) . '"';
        $query = 'SELECT * FROM tb_pengguna WHERE username IN (' . $inArray . ') GROUP BY username';
        $data = $this->getData($query, []);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['username']] = $kol;
            }
        } else {
            $result = array();
        }
        return $result;
    }

    /*     * **************** Get Tabel ************************** */

    public function getTabelShiftDetail($id) {
        $data = $this->getData('SELECT * FROM view_shift_jam WHERE (id_shift = ?) ORDER BY sdays', array($id));
        if ($data['count'] > 0) {
            foreach ($data['value'] as $val) {
                $result[$val['sdays']]['id_jam_kerja'] = $val['id_jam_kerja'];
                $result[$val['sdays']]['stime'] = $val['stime'];
                $result[$val['sdays']]['etime'] = $val['etime'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    public function getTabelMesin($data) {
        $idKey = array();
        $page = $data['page'];
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $q_cari = 'WHERE 1 ';
        if (!empty($data['nama_mesin'])) {
            $q_cari .= 'AND (nama_mesin LIKE ?) ';
            array_push($idKey, '%' . $data['nama_mesin'] . '%');
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
        $page = (!empty($data['page'])) ? $data['page'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $query = 'SELECT  jadwal.*, presensi.* FROM tb_log_presensi presensi '
                . 'LEFT JOIN view_jadwal jadwal ON presensi.`pin_absen` = jadwal.`pin_absen` AND presensi.`tanggal_log_presensi` = jadwal.`tanggal` '
                . 'WHERE (presensi.`pin_absen` = ?) AND (tanggal_log_presensi BETWEEN ? AND ?) '
                . 'ORDER BY tanggal_log_presensi, jam_log_presensi';

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

    public function getTabelShift($data) {
        $q_cari = '';
        $idKey = array();
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (kdlokasi = ?) ';
            array_push($idKey, $data['kdlokasi']);
        }

        $page = (!empty($data['page'])) ? $data['page'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 100;

        $query = 'SELECT * FROM tb_shift WHERE 1 ' . $q_cari;
        $j_query = 'SELECT COUNT(id_shift) AS jumlah FROM tb_shift WHERE 1 ' . $q_cari;

        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($j_query, $idKey);
        // $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ', ' . $batas, $idKey);
        $dataArr = $this->getData($query, $idKey);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = ($jmlData['count'] > 0) ? $jmlData['value'][0]['jumlah'] : 0;
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
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
        
        
        $data = $this->getData('SELECT '
                . ' absen.`pin_absen`            AS `pin_absen`, '
                . ' absen.`tanggal_log_presensi` AS `tanggal`, '
                . ' CONCAT(DATE_FORMAT(`j_detail`.`masuk`,"%H:%i")," - ",DATE_FORMAT(`j_detail`.`pulang`,"%H:%i")) AS `jamkerja`, '
                . ' absen.`jam_log_presensi`     AS `jam`, '
                . ' absen.`status_log_presensi`  AS `stsMesin`, '
                . ' IF(((TIME_TO_SEC(`absen`.`jam_log_presensi`) BETWEEN TIME_TO_SEC(`jamkerja`.`mulai_masuk`) AND IF((TIME_TO_SEC(`jamkerja`.`mulai_masuk`) > TIME_TO_SEC(`jamkerja`.`akhir_masuk`)),(TIME_TO_SEC("24:00:00") + TIME_TO_SEC(`jamkerja`.`akhir_masuk`)),TIME_TO_SEC(`jamkerja`.`akhir_masuk`))) AND (`absen`.`status_log_presensi` = "0")),"0",IF(((TIME_TO_SEC(`absen`.`jam_log_presensi`) BETWEEN TIME_TO_SEC(`jamkerja`.`mulai_pulang`) AND IF((TIME_TO_SEC(`jamkerja`.`mulai_pulang`) > TIME_TO_SEC(`jamkerja`.`akhir_pulang`)),(TIME_TO_SEC("24:00:00") + TIME_TO_SEC(`jamkerja`.`akhir_pulang`)),TIME_TO_SEC(`jamkerja`.`akhir_pulang`))) AND (`absen`.`status_log_presensi` = "1")),"1","-")) AS `stsHadir`, '
                . ' IF(((SELECT `stsHadir`) = "0"),CONCAT(DATE_FORMAT(`jamkerja`.`mulai_masuk`,"%H:%i")," - ",DATE_FORMAT(`jamkerja`.`akhir_masuk`,"%H:%i")),CONCAT(DATE_FORMAT(`jamkerja`.`mulai_pulang`,"%H:%i")," - ",DATE_FORMAT(`jamkerja`.`akhir_pulang`,"%H:%i"))) AS `batasScan`, '
                . ' FLOOR(IF((((SELECT `stsHadir`) = "0") AND (`absen`.`jam_log_presensi` > `j_detail`.`masuk`)),((TIME_TO_SEC(`absen`.`jam_log_presensi`) - TIME_TO_SEC(`j_detail`.`masuk`)) / 60),IF((((SELECT `stsHadir`) = "1") AND (`absen`.`jam_log_presensi` < `j_detail`.`pulang`)),((TIME_TO_SEC(`j_detail`.`pulang`) - TIME_TO_SEC(`absen`.`jam_log_presensi`)) / 60),"0"))) AS `telat`, '
                . ' absen.`verifikasi`           AS `verifikasi`, '
                . ' absen.`id_mesin`             AS `id_mesin`, '
                . ' jadwal.`kdlokasi`            AS `kdlokasi`'
                . 'FROM tb_log_presensi `absen` '
                . ' JOIN tb_jadwal `jadwal` '
                . '     ON absen.`pin_absen` = `jadwal`.`pin_absen` '
                . ' JOIN tb_jadwal_detail `j_detail` '
                . '     ON jadwal.`id_jadwal` = j_detail.`id_jadwal` AND absen.`tanggal_log_presensi` = j_detail.`tanggal` '
                . ' LEFT JOIN tb_jam_kerja `jamkerja` '
                . '     ON j_detail.`id_jam_kerja` = jamkerja.`id_jam_kerja` '
                . 'WHERE 1 AND (tanggal BETWEEN ? AND ?) AND absen.`pin_absen` IN (' . $inArray . ') '
                . 'GROUP BY `pin_absen`, tanggal, stsHadir', $idKey);
        
        
//        $data = $this->getData('SELECT * FROM view_lap_harian '
//                . 'WHERE (tanggal BETWEEN ? AND ?) AND pin_absen IN (' . $inArray . ') '
//                . 'GROUP BY pin_absen, tanggal, stsHadir', $idKey);
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

    public function getArrPersonilJadwal($personil = array()) {
        $idKey = array();
        foreach ($personil as $val) {
            array_push($idKey, $val['pin_absen']);
        }
        $inArray = implode(',', array_fill(0, count($personil), '?'));
        $data = $this->getData('SELECT * FROM tb_jadwal jadwal '
                . 'JOIN tb_shift shift ON jadwal.id_shift = shift.id_shift '
                . 'WHERE jadwal.pin_absen IN (' . $inArray . ') '
                . 'AND DATE_FORMAT(SYSDATE(), "%Y-%m-%d") BETWEEN jadwal.sdate AND jadwal.edate', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $val) {
                $result[$val['pin_absen']]['nama_shift'] = $val['nama_shift'];
            }
        } else {
            $result = array();
        }
        return $result;
//        return $personil;
    }

}
