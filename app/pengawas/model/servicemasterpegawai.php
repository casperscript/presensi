<?php

namespace app\pengawas\model;

use system;

class servicemasterpegawai extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_pegawai');
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
    
    public function getTabelPilihanLokasiKerja() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tref_lokasi_kerja WHERE (status_lokasi_kerja = "1")', array());
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
        }
        else {
            return array('nipbaru' => '-', 'nama_personil' => '-', 'pin_absen' => '-');
        }
    }
    
    public function getDataFotoPegawai($id) {
        set_time_limit(0);
        $data = $this->getData('SELECT foto_pegawai FROM view_presensi_personal WHERE (nipbaru = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        }
        else {
            return array('foto_pegawai' => '-');
        }
    }

}

?>
