<?php

namespace app\pengawas\model;

use system;
use app\pengawas\model\laporan_service;
use app\pengawas\model\pegawai_service;

class backup_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_backup');
        $this->laporan_service = new laporan_service();
        $this->pegawai_service = new pegawai_service();
    }

    public function getDataInduk($data) {
    	$idKey = [$data['kdlokasi'], $data['bulan'], $data['tahun']];
    	$query = 'SELECT * FROM tb_induk WHERE kdlokasi = ? AND bulan = ? AND tahun = ?';
        $dataArr = $this->getData($query, $idKey);

        if ($dataArr['count'] > 0)
        	return $dataArr['value'][0];

        return false;
    }

    public function getDataPersonil($data) {
        $idKey = array(); $dataArr = array();
        $q_cari = 'WHERE 1 ';
        if (!empty($data['induk']['id'])) {
            $q_cari .= 'AND (induk_id = ?)';
            array_push($idKey, $data['induk']['id']);
        }

        if (!empty($data['cari'])) {
            $cari = '%' . $data['cari'] . '%';
            $q_cari .= ' AND ((nama_personil LIKE ?) || (pin_absen LIKE ?)) ';
            array_push($idKey, $cari, $cari);
        }

        $query = 'SELECT *,
            (CASE 
                WHEN LOCATE("I/", golruang) = 0 THEN 4
                WHEN LOCATE("I/", golruang) = 1 THEN 1
                WHEN LOCATE("I/", golruang) = 2 THEN 2
                WHEN LOCATE("I/", golruang) = 3 THEN 3
                ELSE NULL
            END) AS golruang_1,
            RIGHT(golruang, 1) AS golruang_2
        FROM tb_personil ' . $q_cari;

        $query .= ' ORDER BY IF(urutan_sotk = "0" OR urutan_sotk = "" OR urutan_sotk IS NULL, 1, 0), IF(kd_jabatan = "" OR kd_jabatan = "-" OR kd_jabatan IS NULL, 1, 0), nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, nipbaru ASC';

        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }

    public function getTabelPersonil($data) {        
        $idKey = array();
        $page = (!empty($data['page'])) ? $data['page'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $q_cari = 'WHERE 1 ';
         if (!empty($data['induk']['id'])) {
            $q_cari .= 'AND (induk_id = ?)';
            array_push($idKey, $data['induk']['id']);
        }

        if (!empty($data['cari'])) {
            $q_cari .= 'AND (nama_personil LIKE "%'.$data['cari'].'%") ';
        }

        $query = 'SELECT *,
            (CASE 
                WHEN LOCATE("I/", golruang) = 0 THEN 4
                WHEN LOCATE("I/", golruang) = 1 THEN 1
                WHEN LOCATE("I/", golruang) = 2 THEN 2
                WHEN LOCATE("I/", golruang) = 3 THEN 3
                ELSE NULL
            END) AS golruang_1,
            RIGHT(golruang, 1) AS golruang_2
        FROM tb_personil ' . $q_cari;
        $query .= ' ORDER BY IF(urutan_sotk = "0" OR urutan_sotk = "" OR urutan_sotk IS NULL, 1, 0), IF(kd_jabatan = "" OR kd_jabatan = "-" OR kd_jabatan IS NULL, 1, 0), nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, nipbaru ASC';

        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM tb_personil ' . $q_cari;

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

    public function getLaporan($induk_id) {
    	$query = 'SELECT * FROM tb_laporan WHERE induk_id = "'.$induk_id.'"';
        $dataArr = $this->getData($query, []);

        if ($dataArr['count'] > 0)  {
        	$lap = $dataArr['value'][0];

            $plus = [
                'ver_admin_opd' => $lap['nip_admin_opd'],
                'sah_kepala_opd' => $lap['nip_kepala_opd'],
                'ver_admin_kota' => $lap['nip_admin_kota'],
                'sah_kepala_bkppd' => $lap['nip_kepala_bkppd'],
                'sah_final' => $lap['nip_final'],
            ];

            return array_merge($lap, $plus);
        }

        return false;
    }

    public function getDataTpp($induk_id) {
        $query = 'SELECT * FROM tb_tpp WHERE induk_id = "'.$induk_id.'" ';
        $dataArr = $this->getData($query, []);

        if ($dataArr['count'] > 0) 
            return $dataArr['value'][0];

        return false;
    }

    public function getDataPersonilBatch($data, $raw = false) {
        $qcari = '';
        if (isset($data['pin_absen']) && $data['pin_absen'] != '') {
             $qcari = 'AND pin_absen IN ('.$data['pin_absen'].')';
        }

        $query = 'SELECT *,
            (CASE 
                WHEN LOCATE("I/", golruang) = 0 THEN 4
                WHEN LOCATE("I/", golruang) = 1 THEN 1
                WHEN LOCATE("I/", golruang) = 2 THEN 2
                WHEN LOCATE("I/", golruang) = 3 THEN 3
                ELSE NULL
            END) AS golruang_1,
            RIGHT(golruang, 1) AS golruang_2
        FROM tb_personil WHERE tampil_tpp = 1 AND induk_id = "'.$data['induk']['id'].'" '.$qcari;

        $query .= ' ORDER BY IF(urutan_sotk = "0" OR urutan_sotk = "" OR urutan_sotk IS NULL, 1, 0), IF(kd_jabatan = "" OR kd_jabatan = "-" OR kd_jabatan IS NULL, 1, 0), nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, nipbaru ASC';
        $result = $this->getData($query);

        if ($raw)
            return $result;

        $dataArr = [];
        if (isset($result['value']))
            foreach ($result['value'] as $i) {
                $dataArr[$i['pin_absen']] = [
                    'nip' => $i['nipbaru'],
                    'nama' => $i['nama_personil'],
                    'no_' => $i['nama_personil'],
                ];
            }

        return $dataArr;
    }

    public function getBelumBackup($input, $induk) {
        $sudah = [];
        foreach ($induk['value'] as $i) 
            $sudah[] = $i['kdlokasi'];

        $lap = $this->laporan_service->getData('SELECT * FROM tb_laporan
            WHERE bulan = "'.$input['bulan'].'" AND tahun = "'.$input['tahun'].'"
            AND sah_final IS NOT NULL
        ');

        $belum = [];
        foreach ($lap['value'] as $j) {
            if (!in_array($j['kdlokasi'], $sudah))
                $belum[] = $j['kdlokasi'];
        }

        return $belum;
    }

    public function dobackup($input) {
        //$input['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
        $lokasi = $this->pegawai_service->getData('SELECT * FROM tref_lokasi_kerja WHERE status_lokasi_kerja = 1 AND kdlokasi = "'.$input['kdlokasi'].'"');
        if ($lokasi['count'] > 0) {
            $input['satker'] = $lokasi['value'][0]['singkatan_lokasi'];
            $input['nmlokasi']= $lokasi['value'][0]['nmlokasi'];
        }

        //simpan induk
        $tbinduk = $this->save_induk($input);
        
        if ($tbinduk['error']) { //jk berhasil simpan
            //simpan laporan
            $tblaporan = $this->save_laporan($input, $tbinduk);

            //simpan personil
            $tbpersonil = $this->save_personil($input, $tbinduk);

            //simpan tpp
            $tbtpp = $this->save_tpp($input, $tbinduk);

            if (!$tblaporan['error'] || !$tbpersonil['error'] || !$tbtpp['error'])
                $this->hapusBackup($input);
        }

        return $tbinduk;
    }

    private function save_induk($input) {
        $induk = [
            'id' => '',
            'kdlokasi' => $input['kdlokasi'],
            'nmlokasi' => $input['nmlokasi'],
            'singkatan_lokasi' => $input['satker'],
            'bulan' => $input['bulan'],
            'tahun' => $input['tahun'],
            'dateAdd' => date('Y-m-d H:i:s')
        ];
        $tbinduk = $this->save('tb_induk', $induk);
        return $tbinduk;
    }

    private function save_laporan($input, $tbinduk) {
        parent::setConnection('db_presensi');
        $params = [$input['kdlokasi'], $input['bulan'], $input['tahun']];
        $sql = "SELECT * FROM tb_laporan WHERE kdlokasi = ? AND bulan = ? AND tahun = ? AND pin_absen IS NULL";
        $datalap = $this->getData($sql, $params)['value'][0];

        //ambil data personil
        $p = [$datalap['ver_admin_opd'], $datalap['sah_kepala_opd'], $datalap['ver_admin_kota'], $datalap['sah_kepala_bkppd'], $datalap['sah_final']];
        $p = implode(',', array_filter($p));

        parent::setConnection('db_pegawai');
        $person_ver = $this->getData("SELECT vp.nipbaru, vp.nama_personil
            FROM view_presensi_personal vp
            WHERE (vp.nipbaru in (".$p."))", []);

        parent::setConnection('db_presensi'); //ambil jabatan pengguna
        foreach ($person_ver['value'] as $i) {
            if ($datalap['ver_admin_opd'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP01' AND nipbaru = '".$i['nipbaru']."' 
                    AND kdlokasi = '".$input['kdlokasi']."' AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) 
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                $i['tanggal'] = $datalap['dt_ver_admin_opd'];
                $verlap['admin_opd'] = $i;
            }
            if ($datalap['sah_kepala_opd'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP02' AND nipbaru = '".$i['nipbaru']."' 
                    AND kdlokasi = '".$input['kdlokasi']."' AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0)
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];
                    

                $i['tanggal'] = $datalap['dt_sah_kepala_opd'];
                $verlap['kepala_opd'] = $i;
                $verlap['stempel_opd'] = $input['kdlokasi'].'.png';
            }
            if ($datalap['ver_admin_kota'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP03' AND nipbaru = '".$i['nipbaru']."' 
                    AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) 
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                $i['tanggal'] = $datalap['dt_ver_admin_kota'];
                $verlap['admin_kota'] = $i;
            }
            if ($datalap['sah_kepala_bkppd'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP04' AND nipbaru = '".$i['nipbaru']."' 
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) {
                    if ($pengguna['value'][0]['jabatan_pengguna'] != NULL)
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                    $verlap['stempel_bkppd'] = $pengguna['value'][0]['kdlokasi'].'.png';
                }

                $i['tanggal'] = $datalap['dt_sah_kepala_bkppd'];
                $verlap['kepala_bkppd'] = $i;
            }

            if ($datalap['sah_final'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP02' AND nipbaru = '".$i['nipbaru']."' 
                    AND kdlokasi = '".$input['kdlokasi']."' AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) 
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                $i['tanggal'] = $datalap['dt_sah_final'];
                $verlap['final'] = $i;
            }
        }

        parent::setConnection('db_backup');
        $field = ['admin_opd', 'kepala_opd', 'admin_kota', 'kepala_bkppd', 'final'];
        $laporan['id'] = '';
        $laporan['induk_id'] = $tbinduk['inserted_id'];

        foreach ($field as $key) {
            $laporan['nip_'.$key] = $verlap[$key]['nipbaru'];
            $laporan['nama_'.$key] = $verlap[$key]['nama_personil'];
            $laporan['jabatan_'.$key] = $verlap[$key]['jabatan_pengguna'];
            $laporan['dt_'.$key] = $verlap[$key]['tanggal'];
        }
        
        $laporan['stempel_opd'] = $verlap['stempel_opd'];

        $laporan['stempel_bkppd'] = '';
        if (isset($verlap['stempel_bkppd'])) 
            $laporan['stempel_bkppd'] = $verlap['stempel_bkppd'];

        $laporan['dateAdd'] = date('Y-m-d H:i:s');

        $tblaporan = $this->save('tb_laporan', $laporan);
        return $tblaporan;
    }

     public function getDataPersonilTpp($data) {
        parent::setConnection('db_pegawai');

        $idKey = array(); $dataArr = array();
        $q_cari = 'WHERE 1 ';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (pp.kdlokasi = ?)';
            array_push($idKey, $data['kdlokasi']);
        }

        $query = 'SELECT pin_absen, pp.*, npwp, gol_jbtn, nominal_tp, tunjangan_jabatan, s.urutan_sotk, v.kd_jabatan_khusus, v.kd_tp, v.kd_ruang_jab,
            (CASE 
                WHEN LOCATE("I/", pp.golruang) = 0 THEN 4
                WHEN LOCATE("I/", pp.golruang) = 1 THEN 1
                WHEN LOCATE("I/", pp.golruang) = 2 THEN 2
                WHEN LOCATE("I/", pp.golruang) = 3 THEN 3
                ELSE NULL
            END) AS golruang_1,
            RIGHT(pp.golruang, 1) AS golruang_2,
            IF(ISNULL(v.nipbaru), 0, 1) AS tampil_tpp

            FROM view_presensi_personal pp 
            LEFT JOIN tref_sotk s ON s.kdsotk = pp.kdsotk
            LEFT JOIN view_tpp_pegawai v ON v.nipbaru = pp.nipbaru ' . $q_cari;

        $query .= ' ORDER BY IF(urutan_sotk = "0" OR urutan_sotk = "" OR urutan_sotk IS NULL, 1, 0), IF(pp.kd_jabatan = "" OR pp.kd_jabatan = "-" OR pp.kd_jabatan IS NULL, 1, 0), v.nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, pp.nipbaru ASC';
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }

    private function save_personil($input, $tbinduk, $rekap) {
        $pegawai = $this->getDataPersonilTpp($input)['value'];

        parent::setConnection('db_backup');
        $pajak = $this->laporan_service->getArraypajak();

        foreach ($pegawai as $peg) {
            $p = $this->getTabel('tb_personil');
            $field = array_keys($p);

            foreach ($field as $i) {
                if ($i != 'id' && isset($peg[$i]))
                    $p[$i] = $peg[$i];
            }

            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $p['pajak_tpp'] = isset($pajak[$gol]) ? $pajak[$gol] : 0;
            $p['induk_id'] = $tbinduk['inserted_id'];
            $p['dateAdd'] = date('Y-m-d H:i:s');

            $tbpersonil = $this->save('tb_personil', $p);
            if ($tbpersonil['error']) {
                $tbpresensi = $this->save_presensi($rekap, $peg['pin_absen'], $tbpersonil['inserted_id']);
                if (!$tbpresensi['error'])
                    return $tbpresensi;
            }
        }

        return $tbpersonil;
    }

    private function save_tpp($input, $tbinduk) {
        $kepala = $this->laporan_service->getKepala($input['kdlokasi']);
        $bendahara = $this->laporan_service->getBendahara($input['kdlokasi']);

        $tpp = [
            'id' => '',
            'induk_id' => $tbinduk['inserted_id'],
            'tgl_cetak' => date('Y-m-d'),
            'jabatan_kepala' => $kepala['namanya'],
            'nip_kepala' => $kepala['nipbaru'],
            'nama_kepala' => $kepala['nama_personil'],
            'nip_bendahara' => $bendahara['nipbaru'],
            'nama_bendahara' => $bendahara['nama_personil'],
            'dateAdd' => date('Y-m-d H:i:s')
        ];

        $tbtpp = $this->save('tb_tpp', $tpp);
        return $tbtpp;
    }

    public function hapusBackup($data) {
        $params = [$data['kdlokasi'], $data['bulan'], $data['tahun']];
        $induk = $this->getData('SELECT * FROM tb_induk WHERE kdlokasi = ? AND bulan = ? AND tahun = ?', $params);
        
        foreach ($induk['value'] as $ind) {
            $idKeys = ['induk_id' => $ind['id']];
            $this->delete('tb_induk', ['id' => $ind['id']]);
            $this->delete('tb_laporan', $idKeys);
            $this->delete('tb_personil', $idKeys);
            $this->delete('tb_tpp', $idKeys);
        }
    }
 }