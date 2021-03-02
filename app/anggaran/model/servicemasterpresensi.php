<?php

namespace app\anggaran\model;

use system;

class servicemasterpresensi extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
        $this->defaultValue = array(
            'status_login' => 'offline',
            'id_shift' => null,
            'id_potongan_pajak' => null,
            'id_mesin' => null,
            'id_menu' => null,
            'id_kode_presensi' => null,
        );
    }
    
    // START DUMP CETAK USER
        public function getDataUserCetak() {
            set_time_limit(0);
            $data = $this->getData('SELECT a.username, a.password, a.nipbaru, b.nama_grup_pengguna FROM tb_pengguna_ACAK a, tb_grup_pengguna b WHERE a.grup_pengguna_kd=b.kd_grup_pengguna', array());
            return $data['value'];
        }
    // END DUMP CETAK USER
    
    // START MASTER JENIS MODERASI
    public function getPilihanSatuanPotongan() {
        return array('Hari Kerja' => 'Hari Kerja', 'Bulan Kerja' => 'Bulan Kerja');
    }
    public function getPilihanPetugasModerasi() {
        return array('OPD' => 'OPD', 'PNS' => 'PNS');
    }
    public function getDataJenisModerasiLast() {
        set_time_limit(0);
        $data = $this->getData('SELECT kd_jenis FROM tb_jenis_moderasi ORDER BY kd_jenis DESC LIMIT 1', array());
        return $data['value'][0];
    }
    public function getTabelJenisModerasi($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_jenis LIKE ?) || (nama_jenis LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jenis_moderasi' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getDataJenisModerasiForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jenis_moderasi WHERE (kd_jenis = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jenis_moderasi');
        }
    }
    // END MASTER JENIS MODERASI
    
    
    // START MASTER JENIS CUTI
    public function getDataJenisCutiLast() {
        set_time_limit(0);
        $data = $this->getData('SELECT kd_jenis_cuti FROM tb_jenis_cuti ORDER BY kd_jenis_cuti DESC LIMIT 1', array());
        return $data['value'][0];
    }
    public function getTabelJenisCuti($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_jenis_cuti LIKE ?) || (nama_jenis_cuti LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jenis_cuti' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanJenisCuti() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jenis_cuti', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_jenis_cuti']] = $kol['nama_jenis_cuti'];
        }
        return $data;
    }
    public function getDataJenisCutiForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jenis_cuti WHERE (kd_jenis_cuti = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jenis_cuti');
        }
    }
    // END MASTER JENIS CUTI
    
    
    // START MASTER JENIS DINAS
    public function getDataJenisDinasLast() {
        set_time_limit(0);
        $data = $this->getData('SELECT kd_jenis_dinas FROM tb_jenis_dinas ORDER BY kd_jenis_dinas DESC LIMIT 1', array());
        return $data['value'][0];
    }
    public function getTabelJenisDinas($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_jenis_dinas LIKE ?) || (nama_jenis_dinas LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jenis_dinas' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanJenisDinas() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jenis_dinas', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_jenis_dinas']] = $kol['nama_jenis_dinas'];
        }
        return $data;
    }
    public function getDataJenisDinasForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jenis_dinas WHERE (kd_jenis_dinas = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jenis_dinas');
        }
    }
    // END MASTER JENIS DINAS
    
    
    // START MASTER GRUP PENGGUNA
    public function getDataGrupPenggunaLast() {
        set_time_limit(0);
        $data = $this->getData('SELECT kd_grup_pengguna FROM tb_grup_pengguna ORDER BY kd_grup_pengguna DESC LIMIT 1', array());
        return $data['value'][0];
    }
    public function getTabelGrupPengguna($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_grup_pengguna LIKE ?) || (nama_grup_pengguna LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_grup_pengguna' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanGrupPengguna() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_grup_pengguna', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_grup_pengguna']] = $kol['nama_grup_pengguna'];
        }
        return $data;
    }
    public function getDataGrupPenggunaForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_grup_pengguna WHERE (kd_grup_pengguna = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_grup_pengguna');
        }
    }
    // END MASTER GRUP PENGGUNA
    
    
    // START MASTER PENGGUNA
    public function getTabelPengguna($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((username LIKE ?) || (grup_pengguna_kd LIKE ?)  || (kdlokasi LIKE ?) || (nama_grup_pengguna LIKE ?)) ' : '';
        $query = 'SELECT * FROM view_pengguna' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanPengguna() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM view_pengguna', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['username']] = $kol['username'];
        }
        return $data;
    }
    public function cekPrimaryKodePengguna($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_pengguna WHERE (username = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataPenggunaForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE (username = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_pengguna');
        }
    }
    // END MASTER PENGGUNA
    
    // START MASTER JAM KERJA
    public function getTabelJamKerja($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((nama_jam_kerja LIKE ?) || (jam_masuk LIKE ?) || (jam_pulang LIKE ?) || (mulai_masuk LIKE ?) || (akhir_masuk LIKE ?) || (mulai_pulang LIKE ?) || (akhir_pulang LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_jam_kerja' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari, $cari, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanJamKerja() {
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jam_kerja', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['id_jam_kerja']] = $kol['nama_jam_kerja'];
        }
        return $data;
    }
    public function getDataJamKerjaForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_jam_kerja WHERE (id_jam_kerja = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_jam_kerja');
        }
    }
    // END MASTER JAM KERJA
    
    // START MASTER ATURAN
    public function getDataAturanLast() {
        set_time_limit(0);
        $data = $this->getData('SELECT kd_aturan FROM tb_aturan ORDER BY kd_aturan DESC LIMIT 1', array());
        return $data['value'][0];
    }
    public function getPilihanStatusAturan() {
        return array('masuk' => 'Masuk', 'pulang' => 'Pulang', 'lupafinger' => 'Lupa Finger');
    }
    public function getTabelAturan($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kd_aturan LIKE ?) || (indikator_aturan LIKE ?) || (status_aturan LIKE ?) || (batas_waktu_aturan LIKE ?) || (potongan_tpp_aturan LIKE ?) || (dasar_hukum_aturan LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_aturan' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanAturan() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_aturan', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_aturan']] = $kol['indikator_aturan'];
        }
        return $data;
    }
    public function getDataAturanForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_aturan WHERE (kd_aturan = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_aturan');
        }
    }
    // END MASTER ATURAN
    
    
    // START MASTER SHIFT
    public function getPilihanUnitShift() {
        return array('harian' => 'Harian', 'mingguan' => 'Mingguan');
    }
    public function getTabelShift($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((nama_shift LIKE ?) || (tanggal_mulai_shift LIKE ?) || (tanggal_akhir_shift LIKE ?) || (unit_shift LIKE ?) || (status_shift LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_shift' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanShift() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_shift', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['id_shift']] = $kol['nama_shift'];
        }
        return $data;
    }
    public function getDataShiftForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_shift WHERE (id_shift = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_shift');
        }
    }
    // END MASTER SHIFT
    
    
    // START MASTER POTONGAN PAJAK
    public function getPilihanGolongan() {
        return array('I' => 'I', 'II' => 'II', 'III' => 'III', 'IV' => 'IV');
    }
    public function getTabelPotonganPajak($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((id_potongan_pajak LIKE ?) || (golruang_kepegawaian LIKE ?) || (potongan_pajak LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_potongan_pajak' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanPotonganPajak() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_potongan_pajak', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['id_potongan_pajak']] = $kol['potongan_pajak'];
        }
        return $data;
    }
    public function getDataPotonganPajakForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_potongan_pajak WHERE (id_potongan_pajak = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_potongan_pajak');
        }
    }
    // END MASTER POTONGAN PAJAK
    
    
    // START MASTER MESIN
    public function getPilihanKelompokMesin() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_kelompok_mesin', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['id_kelompok_mesin']] = $kol['nama_kelompok'];
        }
        return $data;
    }
    public function getTabelMesin($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((nama_mesin LIKE ?) || (ip_mesin LIKE ?) || (serial_mesin LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_mesin' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanMesin() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_mesin', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['id_mesin']] = $kol['nama_mesin'];
        }
        return $data;
    }
    public function getDataMesinForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_mesin WHERE (id_mesin = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_mesin');
        }
    }
    // END MASTER MESIN
    
    
    // START MASTER PANDUAN
    public function getDataPanduanLast() {
        set_time_limit(0);
        $data = $this->getData('SELECT kd_panduan FROM tb_panduan ORDER BY kd_panduan DESC LIMIT 1', array());
        return $data['value'][0];
    }
    public function getTabelPanduan($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((nama_panduan LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_panduan' . $q_cari . '';
        $idKey = array();
        array_push($idKey, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanPanduan() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_panduan', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_panduan']] = $kol['nama_panduan'];
        }
        return $data;
    }
    public function getDataPanduanForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_panduan WHERE (kd_panduan = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_panduan');
        }
    }
    // END MASTER PANDUAN
    
    
    // START MASTER KODE PRESENSI
    public function getPilihanJenisModerasi() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_jenis_moderasi', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_jenis']] = $kol['nama_jenis'];
        }
        return $data;
    }
    public function getTabelKodePresensi($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((kode_presensi LIKE ?) || (ket_kode_presensi LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_kode_presensi' . $q_cari . ' ORDER BY kode_presensi ASC';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function cekPrimaryKodePresensi($id) {
        set_time_limit(0);
        $dataArr = $this->getData('SELECT * FROM tb_kode_presensi WHERE (kode_presensi = ?)', array($id));
        return $dataArr['count'];
    }
    public function getDataKodePresensiForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_kode_presensi WHERE (id_kode_presensi = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_kode_presensi');
        }
    }
    // END MASTER KODE PRESENSI
    
    
    // START PENGATURAN PROFIL
    public function getProfilGrupPengguna($id) {        
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_grup_pengguna WHERE (kd_grup_pengguna = ?)', array($id));
        return $data['value'][0];
    }
    // END MASTER PENGATURAN PROFIL
    
    // START PENGATURAN PENGGUNA ADMIN OPD
    public function getTabelPenggunaAdminOPD($data, $kdlokasi) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((username LIKE ?) || (grup_pengguna_kd LIKE ?) || (nama_grup_pengguna LIKE ?)) ' : '';
        $query = 'SELECT * FROM view_pengguna' . $q_cari . 'AND (kdlokasi="'.$kdlokasi.'") AND (kd_grup_pengguna = "KDGRUP05")';
        $idKey = array();
        array_push($idKey, $cari, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getPilihanGrupPenggunaAdminOPD() {        
        set_time_limit(0);
        $data = array();
        $dataArr = $this->getData('SELECT * FROM tb_grup_pengguna WHERE (kd_grup_pengguna = "KDGRUP05")', array());
        foreach ($dataArr['value'] as $kol) {
            $data[$kol['kd_grup_pengguna']] = $kol['nama_grup_pengguna'];
        }
        return $data;
    }
    // END PENGATURAN PENGGUNA ADMIN OPD
    
    // START MASTER LIBUR
    public function getTabelLibur($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((keterangan LIKE ?) || (tgl_libur LIKE ?))' : '';
        $query = 'SELECT * FROM tb_libur' . $q_cari . ' ORDER BY id_libur DESC';
        $idKey = array();
        array_push($idKey, $cari, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    public function getDataLiburForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_libur WHERE (id_libur = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_libur');
        }
    }
    // END MASTER LIBUR
    
    public function getTeks($lokasi)
    {
        $dataArr = $this->getData('SELECT * FROM tb_teks WHERE lokasi=? AND admin = 1 AND tampil = 1', array($lokasi));
        $tempel = []; $popup = [];
        foreach ($dataArr['value'] as $i) {
            $bentuk = $i['bentuk'];
            if ($bentuk == 'TEMPEL')
                $tempel[$i['kd_teks']] = [
                    'isi_teks' => $i['isi_teks'],
                    'bg_color' => $i['bg_color']
                ];
            else
                $popup[$i['kd_teks']] = [
                    'isi_teks' => $i['isi_teks'],
                    'bg_color' => $i['bg_color']
                ];            
        }

        $teks = [
            'tempel' => $tempel,
            'popup' => $popup
        ];

        return $teks;
    }

}

?>
