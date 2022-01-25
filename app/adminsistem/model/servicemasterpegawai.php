<?php

namespace app\adminsistem\model;

use system;

class servicemasterpegawai extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_pegawai');
    }
    
    # Get List Data
    public function getListNamaPersonil($arrNip = array()) {
        $q_where = 'WHERE 1 ';
        if (count($arrNip) > 0) :
            $inArray = implode('\',\'', $arrNip);
            $q_where .= 'AND nipbaru IN (\'' . $inArray . '\')';
        endif;
        $dataArr = $this->getData('SELECT '
                . 'nipbaru, CONCAT(gelar_depan, IF((gelar_depan <> "")," ",""), namapeg, IF((gelar_blkg <> "")," ",""), gelar_blkg) AS nama_personil '
                . 'FROM texisting_personal ' 
                . $q_where, []);
        $key = array_column($dataArr['value'], 'nipbaru');
        $column = array_column($dataArr['value'], 'nama_personil');
//        return $dataArr['query'];
        return array_combine($key, $column);
    }

    public function getListLokasiKerja($arrKdlokasi = array()) {
        $q_where = 'WHERE 1 AND status_lokasi_kerja = 1 ';
        if (count($arrKdlokasi) > 0) :
            $inArray = implode('","', $arrKdlokasi);
            $q_where .= 'AND kdlokasi IN ("' . $inArray . '")';
        endif;
        $dataArr = $this->getData('SELECT * FROM tref_lokasi_kerja ' . $q_where, []);
        $key = array_column($dataArr['value'], 'kdlokasi');
        $column = array_column($dataArr['value'], 'singkatan_lokasi');
        return array_combine($key, $column);
    }



    // START DUMP CETAK USER
    public function getDataUserCetak() {
        set_time_limit(0);
        $data = $this->getData('SELECT a.nipbaru, a.nama_personil, b.nmlokasi, b.singkatan_lokasi FROM view_presensi_personal a, tref_lokasi_kerja b WHERE a.kdlokasi=b.kdlokasi ORDER BY b.singkatan_lokasi', array());
        return $data['value'];
    }

    // END DUMP CETAK USER
    // START MASTER JENIS MODERASI
//    public function getPilihanSatuanPotongan() {
//        return array('Hari Kerja' => 'Hari Kerja', 'Bulan Kerja' => 'Bulan Kerja');
//    }

    public function getTabelPilihanLokasiKerja($param = []) {
        set_time_limit(0);
        $data = array();
        $idKey = [];
        $q_cari = 'WHERE 1 ';
        if (isset($param['status_lokasi_kerja']) && $param['status_lokasi_kerja'] == 'all') {
            $q_cari .= '';
        } else {
            $q_cari .= 'AND status_lokasi_kerja = 1';
        }
        $dataArr = $this->getData('SELECT * FROM tref_lokasi_kerja ' . $q_cari . ' ORDER BY singkatan_lokasi ASC', $idKey);
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kdlokasi']] = $kol['singkatan_lokasi'];
        }
        return $data;
    }
    
    

    public function getTabelPilihanLokasiKerjaAdminOPD($kdlokasi) {
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tref_lokasi_kerja WHERE (kdlokasi = ?) AND  (status_lokasi_kerja = "1")', array($kdlokasi));
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kdlokasi']] = $kol['singkatan_lokasi'];
        }
        return $data;
    }

//    public function getPilAdmin($id) {
//        set_time_limit(0);
//        $data = $this->getData('SELECT * FROM view_presensi_personal WHERE (kdlokasi = ?)', array($id));
//        if ($data['count'] > 0) {
//            foreach ($data['value'] as $val) {
//                $valData[$val['nipbaru']] = $val['nama_personil'];
//            }
//        } else {
//            $valData[] = '';
//        }
//        return $valData;
//    }

    public function getPilihanAdmin($id) {
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM view_presensi_personal WHERE (kdlokasi = ?)', array($id));
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['nipbaru']] = $kol['nama_personil'];
        }
        return $data;
    }

    public function getPilihanStatusPengguna() {
        return array('enable' => 'Enable', 'disable' => 'Disable');
    }

    public function getTabelPilihanNamaPersonil() {
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM view_presensi_personal', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['nipbaru']] = $kol['nama_personil'];
        }
        return $data;
    }

    public function getDataPegawaiPresensi($id) {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM view_presensi_personal WHERE (nipbaru = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return array('nipbaru' => '-', 'nama_personil' => '-', 'pin_absen' => '-');
        }
    }

    public function getDataFotoPegawai($id) {
        set_time_limit(0);
        $data = $this->getData('SELECT foto_pegawai FROM view_presensi_personal WHERE (nipbaru = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return array('foto_pegawai' => '-');
        }
    }

    //added by daniek
    public function getDataPegawai($p) {
        $data = $this->getData('SELECT vw.*, lokasi.nmlokasi, lokasi.singkatan_lokasi FROM view_presensi_personal vw
            JOIN tref_lokasi_kerja lokasi ON vw.kdlokasi = lokasi.kdlokasi
            WHERE nipbaru IN (' . $p . ')', []);

        $result = [];
        foreach ($data['value'] as $kol) {
            $result[$kol['nipbaru']] = [
                'nipbaru' => $kol['nipbaru'],
                'nama_personil' => $kol['nama_personil'],
                'pin_absen' => $kol['pin_absen'],
                'singkatan_lokasi' => $kol['singkatan_lokasi'],
                'nmlokasi' => $kol['nmlokasi']
            ];
        }

        return $result;
    }

}

?>
