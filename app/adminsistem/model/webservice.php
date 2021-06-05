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

}
