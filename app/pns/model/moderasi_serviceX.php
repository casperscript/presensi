<?php

namespace app\pns\model;

use system;

class moderasi_service extends system\Model {

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
            $m = $param['bulan'] + 1;
            $y = $param['tahun'];
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
        extract($param);
        parent::setConnection('db_presensi');
        $idKey = array();
        $q_cari = '';

        // Filter bukan id yang sama
        if (isset($id)) {
            $q_cari .= ' AND id != ?';
            array_push($idKey, $id);
        }

        // Filter pin absen
        if (isset($pin_absen)) {
            $q_cari .= ' AND pin_absen = ?';
            array_push($idKey, $pin_absen);
        }

        // Filter jenis moderasi
        if (isset($kd_jenis) && $kd_jenis != 'JNSMOD04') {
            $q_cari .= ' AND (kd_jenis = ? OR kd_jenis = ?)';
            array_push($idKey, $kd_jenis, 'JNSMOD04');
        }

        // Filter tanggal
        if (isset($tanggal_awal) && isset($tanggal_akhir) && $tanggal_akhir >= $tanggal_awal) {
            $q_cari .= ' AND (
    			? BETWEEN tanggal_awal AND tanggal_akhir 
    				OR 
    			? BETWEEN tanggal_awal AND tanggal_akhir
    				OR 
    			tanggal_awal BETWEEN ? AND ?
					OR
				tanggal_akhir BETWEEN ? AND ?
    		)';
            array_push($idKey, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir, $tanggal_awal, $tanggal_akhir);
        }

        $query = 'SELECT * FROM tb_moderasi WHERE 1 ' . $q_cari;

        $result = $this->getData($query, $idKey);
        return $result;
    }

    public function getReportX($param) {
        extract($param);
        parent::setConnection('db_presensi');
        $idKey = array();
        $q_cari = '';

        // Filter lokasi
        if (isset($kdlokasi)) {
            $q_cari .= ' AND kdlokasi = ?';
            array_push($idKey, $kdlokasi);
        }

        // Filter tanggal
        if (isset($tanggal_awal) || isset($tanggal_akhir)) {
            $sdate = date('Y-n', strtotime($tanggal_awal));
            $edate = date('Y-n', strtotime($tanggal_akhir));

            $q_cari .= ' AND CONCAT(tahun, "-", bulan) BETWEEN ? AND ?';
            array_push($idKey, $sdate, $edate);
        }

        $query = 'SELECT * FROM tb_laporan WHERE 1 ' . $q_cari;

        $result = $this->getData($query, $idKey);
        return $result;
    }

    public function getReport($param) {
        extract($param);
        parent::setConnection('db_presensi');
        $idKey = array();
        $q_cari = '';

        // Filter lokasi
        if (isset($kdlokasi)) {
            $q_cari .= ' AND kdlokasi = ?';
            array_push($idKey, $kdlokasi);
        }

        // Filter tanggal
        if (isset($tanggal_awal) || isset($tanggal_akhir)) {
            $sdate = strtotime($tanggal_awal);
            $edate = strtotime($tanggal_akhir);
            while ($sdate <= $edate) {
                $dateSeries[] = date('Yn', $sdate);
                $sdate = strtotime('+1 MONTH', $sdate);
            }
            $impDate = implode('\',\'', $dateSeries);
            
            $q_cari .= ' AND CONCAT(tahun, bulan) IN (\'' . $impDate . '\')';
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
        $sql = "SELECT tjm.kd_jenis, tjm.nama_jenis, tkp.kode_presensi, tkp.ket_kode_presensi, tjm.nama_jenis FROM tb_jenis_moderasi tjm INNER JOIN tb_jenis_kode_presensi tjkp ON tjm.kd_jenis = tjkp.kd_jenis INNER JOIN tb_kode_presensi tkp ON tjkp.kode_presensi = tkp.kode_presensi WHERE tkp.moderasi_kode_presensi = 1 AND tjm.kd_jenis = ? AND tkp.kode_presensi NOT IN('TF') ORDER BY tjm.kd_jenis, tkp.ket_kode_presensi";
        return $this->getData($sql, [$kodeKatMod])["value"];
    }

}
