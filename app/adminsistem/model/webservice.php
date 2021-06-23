<?php

namespace app\adminsistem\model;

use system;

class webservice extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    public function checkaccesskey($key = '', $param = array()) {
        $method = isset($param['method']) ? $param['method'] : '';
        $data = $this->getData('SELECT * FROM tb_api WHERE accesskey = ? AND method = ?', [$key, $method]);
        return $data;
    }

    public function getTpp($param = array()) {
        parent::setConnection('db_pegawai');
        $idKey = [$param['bulan'], $param['tahun'], $param['nip']];
        $data = $this->getData('SELECT a.`kdlokasi`, e.`nmlokasi`, e.`singkatan_lokasi`, ? AS bulan, ? AS tahun, a.`nipbaru`, a.`pin_absen`,'
                . '     CONCAT(b.gelar_depan, IF((b.gelar_depan <> "")," ",""), b.namapeg, IF((b.gelar_blkg <> "")," ",""), b.gelar_blkg) AS `nama_personil`,'
                . '     IF (a.kd_stspeg = 29, d.nominal * 0.5, d.nominal) AS nominal_tp'
                . ' FROM texisting_kepegawaian a'
                . '     JOIN texisting_personal b ON a.`nipbaru` = b.`nipbaru`'
                . '     LEFT JOIN tref_jabatan_campur c ON c.`kd_jabatan` = a.`kd_jabatan` AND FIND_IN_SET( a.`kode_sert_guru`, c.`kode_sert_guru`)'
                . '     LEFT JOIN tref_tpp_kelas_jabatan d ON c.`kode_kelas` = d.`kode_kelas`'
                . '     JOIN tref_lokasi_kerja e ON a.`kdlokasi` = e.`kdlokasi`'
                . ' WHERE 1'
                . '     AND a.`kd_stspeg` IN ("04", "09")'
                . '     AND a.`tunjangan_jabatan` = 0'
                . '     AND (a.`kode_sert_guru` != "01" OR d.`kelas` IS NOT NULL)'
                . '     AND a.nipbaru = ?', $idKey);
        return $data;
    }

    public function getTppBc($param = array()) {
        parent::setConnection('db_backup');
        $idKey = [$param['nip'], $param['bulan'], $param['tahun']];
        $data = $this->getData('SELECT a.kdlokasi, a.nmlokasi, a.singkatan_lokasi, a.bulan, a.tahun, b.nipbaru, b.pin_absen, b.nama_personil, b.nominal_tp '
                . ' FROM tb_induk a'
                . '  JOIN tb_personil b ON a.`id` = b.`induk_id`'
                . ' WHERE 1 AND b.`nipbaru` = ? AND a.`bulan` = ? AND a.`tahun` = ?', $idKey);
        return $data;
    }
    
    public function getPresensiMain($param = array()) {
        $data['count'] = 0;
        return $data;
    }

    public function getPresensiBc($param = array()) {
        parent::setConnection('db_backup');
        $idKey = [$param['nip'], $param['bulan'], $param['tahun']];
        $data = $this->getData('SELECT a.`kdlokasi`, a.`nmlokasi`, a.`singkatan_lokasi`, a.`bulan`, a.`tahun`, b.`nipbaru`, b.`pin_absen`, b.`nama_personil`,'
                . '     t1, t2, t3, t4, t5, t6, t7, t8, t9, t10, t11, t12, t13, t14, t15, t16, t17, t18, t19, t20, t21, t22, t23, t24, t25, t26, t27, t28, t29, t30, t31'
                . ' FROM tb_induk a'
                . '     JOIN tb_personil b ON a.`id` = b.`induk_id`'
                . '     JOIN tb_presensi c ON b.`id` = c.`personil_id`'
                . ' WHERE 1 AND b.`nipbaru` = ? AND a.`bulan` = ? AND a.`tahun` = ?', $idKey);
        return $data;
    }
    
    public function getListSatker($param = '') {
        parent::setConnection('db_pegawai');
        $field = $this->getTabel('tref_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($param[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $param[$key]);
            }
        }
        $data = $this->getData('SELECT kdlokasi, singkatan_lokasi FROM tref_lokasi_kerja WHERE status_lokasi_kerja = 1 ' . $q_cari . ' ORDER BY singkatan_lokasi', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $val) {
                $result[$val['kdlokasi']] = $val['singkatan_lokasi'];
            }
        } else {
            $result[] = '';
        }
        return $result;
    }
    
    public function getListTppBc($param = array()) {
        parent::setConnection('db_backup');
        $idKey = [$param['bulan'], $param['tahun']];
        $q_where = '';
        if (!empty($param['kdlokasi'])) {
            $q_where .= ' AND a.`kdlokasi` = ?';
            array_push($idKey, $param['kdlokasi']);
        }
        $data = $this->getData('SELECT a.`kdlokasi`, a.`nmlokasi`, a.`singkatan_lokasi`, a.`bulan`, a.`tahun`, b.`nipbaru`, b.`pin_absen`, b.`nama_personil`, b.`nominal_tp`'
                . ' FROM tb_induk a '
                . ' JOIN tb_personil b ON a.id = b.induk_id' 
                . ' WHERE a.bulan = ? AND a.tahun = ?' . $q_where, $idKey);
        return $data;
    }
    
    public function getListTppMain($param = array()) {
        parent::setConnection('db_pegawai');
        $idKey = array();
        $q_carigaji = '';
        if (!empty($param['bulan']) && !empty($param['tahun'])) {
            $bulan = ($param['bulan'] == 12) ? 1 : $param['bulan'] + 1;
            $tahun = ($param['bulan'] == 12) ? $param['tahun'] + 1 : $param['tahun'];
            $q_carigaji .= 'AND (MONTH(gaji.periode) = ? AND YEAR(gaji.periode) = ?) ';
            array_push($idKey, $bulan, $tahun);
        }
        
        $q_cari = '';
        if (!empty($param['kdlokasi'])) {
            $q_cari .= 'AND ((pegawai.kdlokasi = ?) OR (pegawai.kdsublokasi = ?)) ';
            array_push($idKey, $param['kdlokasi'], $param['kdlokasi']);
        }
        
        $data = $this->getData('SELECT 
		pegawai.`nipbaru`               AS nipbaru,
		pegawai.`pin_absen`             AS pin_absen,
		CONCAT(`personal`.`gelar_depan`,IF((`personal`.`gelar_depan` <> "")," ",""),`personal`.`namapeg`,IF((`personal`.`gelar_blkg` <> ""),", ",""),`personal`.`gelar_blkg`) AS `nama_personil`,
		IF((`pegawai`.`kdsublokasi` = ""),`pegawai`.`kdlokasi`,`pegawai`.`kdsublokasi`) AS `kdlokasi`,
		pegawai.`kd_jabatan`            AS kd_jabatan,
		personal.`nama_R_jabatan`       AS nama_R_jabatan,
		pegawai.`golruang`              AS golruang,
                pegawai.`tunjangan_jabatan`     AS tunjangan_jabatan,
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
            ORDER BY ISNULL(kelas.`kelas`), IF(COALESCE(pegawai.`kelas_on_pegawai`), pegawai.`kelas_on_pegawai`, kelas.`kelas`) DESC, nama_personil ASC', $idKey);
        return $data;
    }
    
    

}
