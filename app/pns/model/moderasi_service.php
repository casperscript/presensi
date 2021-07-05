<?php

namespace app\pns\model;

use system;
use comp;

class moderasi_service extends servicemain {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    public function getDaftarModerasi($param) {
        parent::setConnection('db_presensi');
        $idKey = array();
        $q_cari = 'AND keterangan LIKE "%' . $param['cari'] . '%" ';
        $field = $this->getTabel('view_moderasi');
        foreach ($field as $key => $val) {
            if (isset($param[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $param[$key]);
            }
        }

        if (isset($param['bulan']) || isset($param['tahun'])) {
            $m = $param['bulan'];
            $y = isset($param['tahun']) ? $param['tahun'] : date('Y');
            $q_cari .= 'AND (
            	(MONTH(tanggal_awal) = ? AND YEAR(tanggal_awal) = ?) 
            	OR 
            	(MONTH(tanggal_akhir) = ? AND YEAR(tanggal_akhir) = ?)
            )';
            array_push($idKey, $m, $y, $m, $y);
        }

        if (isset($param['status'])) {
            switch ($param['status']) {
                case 'semua':
                    $c_status = '';
                    break;
                case 'tolak':
                    $c_status = 'AND flag_operator_opd = "0"';
                    break;
                case 'terima':
                    $c_status = 'AND flag_operator_opd = "1"';
                    break;
                case 'null':
                    $c_status = 'AND flag_operator_opd IS NULL';
                    break;
            }
            $q_cari .= $c_status . ' ';
        }

        $result = $this->getData('SELECT * FROM view_moderasi WHERE 1 ' . $q_cari . ' ORDER BY tanggal_awal', $idKey);
        return $result;
    }

    public function chkExistMod($param) {
        parent::setConnection('db_presensi');
        $idKey = array();
        $q_cari = '';

        $jnsMod = implode(',', $param['kd_jenis']);
        // Filter bukan id yang sama
        if (isset($param['id'])) {
            $q_cari .= ' AND id != ?';
            array_push($idKey, $param['id']);
        }

        // Filter pin absen
        if (isset($param['pin_absen'])) {
            $q_cari .= ' AND pin_absen = ?';
            array_push($idKey, $param['pin_absen']);
        }

        // Filter jenis moderasi
        if (isset($param['kd_jenis']) && $param['kd_jenis'] != 'JNSMOD04') {
            $q_cari .= ' AND (kd_jenis IN (?) OR kd_jenis = ?)';
            array_push($idKey, $jnsMod, 'JNSMOD04');
        }

        // Filter tanggal
        if (isset($param['tanggal_awal']) && isset($param['tanggal_akhir']) && $param['tanggal_akhir'] >= $param['tanggal_awal']) {
            $q_cari .= ' AND (
    			? BETWEEN tanggal_awal AND tanggal_akhir 
                            OR 
    			? BETWEEN tanggal_awal AND tanggal_akhir
                            OR 
    			tanggal_awal BETWEEN ? AND ?
                            OR
			tanggal_akhir BETWEEN ? AND ?
    		)';
            array_push($idKey, $param['tanggal_awal'], $param['tanggal_akhir'], $param['tanggal_awal'], $param['tanggal_akhir'], $param['tanggal_awal'], $param['tanggal_akhir']);
        }

        $query = 'SELECT * FROM tb_moderasi WHERE 1 ' . $q_cari;

        $result = $this->getData($query, $idKey);
        return $result;
    }

    public function getCountMod($param, $mod = [], $opt = '') {
        parent::setConnection('db_presensi');
        $idKey = [];
        $where = 'WHERE 1 ';
        if (isset($param['kd_jenis'])) :
            $inArray = '\'' . implode('\',\'', $param['kd_jenis']) . '\'';
            $where .= 'AND `kd_jenis` IN (' . $inArray . ') ';
        endif;
        if (isset($param['pin_absen'])) :
            $where .= 'AND pin_absen = ? ';
            array_push($idKey, $param['pin_absen']);
        endif;
        if (isset($param['tahun'])) :
            $where .= 'AND YEAR(`tanggal_awal`) = ? ';
            array_push($idKey, $param['tahun']);
        endif;
        if (isset($param['bulan'])) :
            $where .= 'AND MONTH(`tanggal_awal`) = ? ';
            array_push($idKey, $param['bulan']);
        endif;
        if (isset($param['not'])) :
            $where .= 'AND tanggal_awal != ? ';
            array_push($idKey, $param['not']);
        endif;
        if (count($mod) > 0) :
            $inArray = '\'' . implode('\',\'', $mod) . '\'';
            $where .= 'AND `kode_presensi` IN (' . $inArray . ') ';
        endif;

        $data = $this->getData('SELECT kd_jenis, COUNT(id) AS jumlah FROM tb_moderasi ' . $where . $opt, $idKey);
        $arrDefault = ['JNSMOD01' => 0, 'JNSMOD02' => 0, 'JNSMOD03' => 0];
        if ($data['count'] > 0) {
            $keys = array_column($data['value'], 'kd_jenis');
            $values = array_column($data['value'], 'jumlah');
            return array_combine($keys, $values) + $arrDefault;
        } else {
            return $arrDefault;
        }
    }

    public function chkLockMod($kdlokasi, $year, $month) {
        parent::setConnection('db_presensi');
        $idKey = [$month, $year, $kdlokasi];
        $getReport = $this->getData('SELECT * FROM tb_laporan WHERE 1 AND bulan = ? AND tahun = ? AND kdlokasi = ?', $idKey);
        return ($getReport['count']) ? true : false;
    }

    public function getReport($param) {
        parent::setConnection('db_presensi');
        $idKey = array();
        $q_cari = '';

        if ($param['tanggal_awal'] > $param['tanggal_akhir']) {
            return false;
        }

        if (isset($param['kdlokasi'])) {     // Filter lokasi
            $q_cari .= ' AND kdlokasi = ?';
            array_push($idKey, $param['kdlokasi']);
        }

        if (isset($param['tanggal_awal']) || isset($param['tanggal_akhir'])) {    // Filter tanggal
            $sdate = strtotime($param['tanggal_awal']);
            $edate = strtotime($param['tanggal_akhir']);
            while ($sdate <= $edate) {
                $dateSeries[] = date('Y-n', $sdate);
                $sdate = strtotime('+1 MONTH', $sdate);
            }
            $impDate = implode('\',\'', $dateSeries);

            $q_cari .= ' AND CONCAT(tahun, "-", bulan) IN (\'' . $impDate . '\')';
        }

        $query = 'SELECT * FROM tb_laporan WHERE 1 ' . $q_cari;
        $result = $this->getData($query, $idKey);
        return $result;
    }

    public function getExceptPeriodMod($param) {
        parent::setConnection('db_presensi');
        extract($param);
        $sdate = $param['tanggal_awal'];
        $edate = $param['tanggal_akhir'];
        $kdlokasi = '%' . $param['kdlokasi'] . '%';

        $idKey = [$kdlokasi, $sdate, $edate, $sdate, $edate, $sdate, $edate];
        $query = 'SELECT * FROM tb_periode_larangan_moderasi 
            WHERE 1 
                AND lokasi_dampak LIKE ?
		AND mulai_berlaku < SYSDATE() 
		AND (
                    ? BETWEEN tgl_mulai AND tgl_akhir
                    OR ? BETWEEN tgl_mulai AND tgl_akhir
                    OR tgl_mulai BETWEEN ? AND ?
                    OR tgl_akhir BETWEEN ? AND ?
		)';
        $result = $this->getData($query, $idKey);
        return $result;
    }

    public function getJenisModerasi($kodeKatMod) {
        parent::setConnection('db_presensi');
        $idKey = array();
        $inArray = implode(',', array_fill(0, count($kodeKatMod), '?'));
        $having = count($kodeKatMod) - 1;
        foreach ($kodeKatMod as $val) {
            array_push($idKey, $val);
        }

        $sql = 'SELECT tkp.kode_presensi, CONCAT("[", tkp.kode_presensi, "] ", tkp.ket_kode_presensi) AS ket_kode_presensi FROM tb_jenis_kode_presensi tjkp '
                . 'INNER JOIN tb_kode_presensi tkp ON tjkp.kode_presensi = tkp.kode_presensi '
                . 'WHERE tjkp.kd_jenis IN (' . $inArray . ') AND tjkp.access_level = "pns" '
                . 'GROUP BY tkp.kode_presensi '
                . 'HAVING COUNT(tkp.kode_presensi) > ' . $having . ' '
                . 'ORDER BY tkp.kode_presensi';

        return $this->getData($sql, $idKey);
    }

    public function getListPegawai($kdlokasi = '') {
        parent::setConnection('db_pegawai');
        $idKey = ['%' . $kdlokasi . '%'];
        $sql = 'SELECT kep.nipbaru, kep.pin_absen, per.namapeg, per.gelar_depan, per.gelar_blkg '
                . 'FROM texisting_kepegawaian kep '
                . 'JOIN texisting_personal per ON kep.nipbaru = per.nipbaru '
                . 'WHERE kep.kdlokasi LIKE ?';
        return $this->getData($sql, $idKey)['value'];
    }

    public function getPegawai($nipbaru = '') {
        parent::setConnection('db_pegawai');
        $idKey = [$nipbaru];
        $sql = 'SELECT kep.nipbaru, kep.pin_absen, per.namapeg, per.gelar_depan, per.gelar_blkg '
                . 'FROM texisting_kepegawaian kep '
                . 'JOIN texisting_personal per ON kep.nipbaru = per.nipbaru '
                . 'WHERE kep.nipbaru = ?';
        return $this->getData($sql, $idKey)['value'];
    }

    public function getListTahun($pinabsen) {
        parent::setConnection('db_presensi');
        $idKey = array($pinabsen);

        $sql = 'SELECT YEAR(tanggal_awal) as tahun FROM tb_moderasi '
                . 'WHERE pin_absen = ? '
                . 'GROUP BY YEAR(tanggal_awal) ORDER BY tanggal_awal ASC';
        $data = $this->getData($sql, $idKey);
        return ($data['count'] > 0) ? array_column($data['value'], 'tahun') : array();
    }

    public function getListBulan($pegawai, $tahun) {
        parent::setConnection('db_presensi');
        $idKey = array($tahun);
        $inArray = implode(',', array_fill(0, count($pegawai), '?'));
        foreach ($pegawai as $val) {
            array_push($idKey, $val['pin_absen']);
        }

        $sql = 'SELECT MONTH(tanggal_awal) AS bulan FROM tb_moderasi '
                . 'WHERE YEAR(tanggal_awal) = ? '
                . 'AND pin_absen IN (' . $inArray . ') '
                . 'GROUP BY MONTH(tanggal_awal) '
                . 'ORDER BY MONTH(tanggal_awal) ASC';
        $data = $this->getData($sql, $idKey);
//        comp\FUNC::showPre($data);
        if ($data['count'] > 0) {
            $keyBulan = array_column($data['value'], 'bulan');
            $listBulan = array_intersect_key(comp\FUNC::$namabulan1, array_flip($keyBulan));
            return $listBulan;
        } else {
            return array();
        }
    }

//Untuk adminopd
//    public function getListTahun($pegawai) {
//        parent::setConnection('db_presensi');
//        $idKey = array();
//        $inArray = implode(',', array_fill(0, count($pegawai), '?'));
//        foreach ($pegawai as $val) {
//            array_push($idKey, $val['pin_absen']);
//        }
//
//        $sql = 'SELECT YEAR(tanggal_awal) as tahun FROM tb_moderasi '
//                . 'WHERE pin_absen IN (' . $inArray . ') '
//                . 'GROUP BY YEAR(tanggal_awal) ORDER BY tanggal_awal ASC';
//        $data = $this->getData($sql, $idKey);
//        return ($data['count'] > 0) ? array_column($data['value'], 'tahun') : array();
//    }
//    public function getListBulan($pegawai, $tahun) {
//        parent::setConnection('db_presensi');
//        $idKey = array($tahun);
//        $inArray = implode(',', array_fill(0, count($pegawai), '?'));
//        foreach ($pegawai as $val) {
//            array_push($idKey, $val['pin_absen']);
//        }
//
//        $sql = 'SELECT MONTH(tanggal_awal) AS bulan FROM tb_moderasi '
//                . 'WHERE YEAR(tanggal_awal) = ? '
//                . 'AND pin_absen IN (' . $inArray . ') '
//                . 'GROUP BY MONTH(tanggal_awal) '
//                . 'ORDER BY MONTH(tanggal_awal) ASC';
//        $data = $this->getData($sql, $idKey);
//        if ($data['count'] > 0) {
//            $keyBulan = array_column($data['value'], 'bulan');
//            $listBulan = array_intersect_key(comp\FUNC::$namabulan1, array_flip($keyBulan));
//            return $listBulan;
//        } else {
//            return array();
//        }
//    }
//
}
