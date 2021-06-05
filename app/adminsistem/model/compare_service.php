<?php

namespace app\adminsistem\model;

use system;
use comp;

class compare_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }
    
    public function getArrayLaporan($param) {
        $idKey = [$param['tahun'], $param['bulan']];
        $data = $this->getData('SELECT * FROM tb_laporan WHERE 1 AND tahun = ? AND bulan = ?', $idKey);
        if ($data['count'] > 0) {
            return $data['value'];
        } else {
            return [];
        }
    }
    
    public function getArraypajak() {
        parent::setConnection('db_presensi');
        $data = $this->getData("SELECT * FROM tb_potongan_pajak", []);

        $dataArr = [];
        foreach ($data['value'] as $i) {
            $dataArr[$i['golruang_kepegawaian']] = $i['potongan_pajak'];
        }
        return $dataArr;
    }
    
    public function getDataSetting($var) {
        parent::setConnection('db_presensi');
        $data = $this->getData('SELECT * FROM tb_setting WHERE `variable` = ?', [$var]);
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_setting');
        }
    }
    
    // TPP sebelum backup
    public function getDataPersonilTpp_bb($data) {
        parent::setConnection('db_pegawai');
        
        $idKey = array();
        $q_carigaji = '';
        if (!empty($data['bulan']) && !empty($data['tahun'])) {
            $bulan = ($data['bulan'] == 12) ? 1 : $data['bulan'] + 1;
            $tahun = ($data['bulan'] == 12) ? $data['tahun'] + 1 : $data['tahun'];
            $q_carigaji .= 'AND (MONTH(gaji.periode) = ? AND YEAR(gaji.periode) = ?) ';
            array_push($idKey, $bulan, $tahun);
        }
        
        $q_cari = '';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND ((pegawai.kdlokasi = ?) OR (pegawai.kdsublokasi = ?)) ';
            array_push($idKey, $data['kdlokasi'], $data['kdlokasi']);
        }
        
        $query = 'SELECT 
		pegawai.`nipbaru`               AS nipbaru,
		pegawai.`pin_absen`             AS pin_absen,
		jabatan.`kdsotk`                AS kdsotk,
		CONCAT(`personal`.`gelar_depan`,IF((`personal`.`gelar_depan` <> "")," ",""),`personal`.`namapeg`,IF((`personal`.`gelar_blkg` <> "")," ",""),`personal`.`gelar_blkg`) AS `nama_personil`,
		IF((`pegawai`.`kdsublokasi` = ""),`pegawai`.`kdlokasi`,`pegawai`.`kdsublokasi`) AS `kdlokasi`,
		pegawai.`kd_jabatan`            AS kd_jabatan,
		personal.`npwp`                 AS npwp,
		personal.`nama_R_jabatan`       AS nama_R_jabatan,
		pegawai.`golruang`              AS golruang,
		personal.`path_foto_pegawai`    AS foto_pegawai,
		jabatan.`kode_kelas`            AS kode_kelas,
		IF (pegawai.`kd_stspeg` = 29, kelas.`nominal` * 0.5, kelas.`nominal`) AS nominal_tp,
		IF (pegawai.kelas_on_pegawai != "", pegawai.kelas_on_pegawai, kelas.`kelas`) AS kelas,
		gaji.`total`                    AS totgaji,
		pegawai.`kode_sert_guru`        AS kode_sert_guru
            FROM `texisting_kepegawaian` `pegawai` 
		JOIN `texisting_personal` `personal` ON pegawai.`nipbaru` = personal.`nipbaru` 
		LEFT JOIN `tref_jabatan_campur` `jabatan` ON jabatan.`kd_jabatan` = pegawai.`kd_jabatan` AND FIND_IN_SET(pegawai.`kode_sert_guru`, jabatan.`kode_sert_guru`)
		LEFT JOIN `tref_tpp_kelas_jabatan` `kelas` ON jabatan.`kode_kelas` = kelas.`kode_kelas`
		LEFT JOIN `data_gaji` `gaji` ON pegawai.`nipbaru` = gaji.`nipbaru` ' . $q_carigaji . '
            WHERE 1 ' . $q_cari . '
                AND pegawai.`kd_stspeg` IN ("04", "29")
                AND pegawai.`tunjangan_jabatan` = 0
                AND (pegawai.`kode_sert_guru` != "01" OR jabatan.`kelas` IS NOT NULL)
            ORDER BY ISNULL(kelas.`kelas`), IF(COALESCE(pegawai.`kelas_on_pegawai`), pegawai.`kelas_on_pegawai`, kelas.`kelas`) DESC, nama_personil ASC';
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }
    
    public function getRekapAll($param) {
        $moderasi = $this->getArrayModerasi($param);
        
    }
    
    public function getArrayModerasi($param) {
        $kdlokasi = $param['kdlokasi'];
        $sdate = $param['tahun'] . '-' . $param['bulan'] . '-01';
        $edate = date('Y-m-t', strtotime($sdate));
        $idKey = [$kdlokasi, $sdate, $edate, $sdate, $edate, $sdate, $edate];
        
        $mod = $this->getData('SELECT * FROM tb_moderasi '
                . 'WHERE kdlokasi = ? '
                . ' AND (? BETWEEN tanggal_awal AND tanggal_akhir OR ? BETWEEN tanggal_awal AND tanggal_akhir OR tanggal_awal BETWEEN ? AND ? OR tanggal_akhir BETWEEN ? AND ?)', $idKey);
        $data['hitung'] = $this->hitungHari($mod['value']);
        return $data;
    }
    
    public function hitungHari($data) {
        $hitung = [];
        foreach ($data as $i) {
            if ($i['kd_jenis'] != 'JNSMOD04') {
                continue;
            }

            $pin = $i['pin_absen'];
            $kode = $i['kode_presensi'];
            $awal = date_create($i['tanggal_awal']);
            $akhir = date_create($i['tanggal_akhir']);
            $diff = date_diff($awal, $akhir)->d + 1;

            if (!isset($hitung[$kode])) {
                $hitung[$pin][$kode] = $diff;
            } else {
                $hitung[$pin][$kode] += $diff;
            }
        }
        return $hitung;
    }

}
