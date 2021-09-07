<?php

namespace app\adminsistem\model;

use comp;
use system;
use app\adminsistem\model\laporan_service;
use app\adminsistem\model\pegawai_service;
use app\adminsistem\model\webadapter;

class backup_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_backup');
        $this->laporan_service = new laporan_service();
        $this->pegawai_service = new pegawai_service();
        $this->webadapter = new webadapter();
    }
    
    public function getData64() {
        parent::setConnection('db_backup_64');
        $data = $this->getData('SELECT * FROM tb_presensi LIMIT 0, 10', []);
        return $data;
    }
    
    public function insertData62($data) {
        parent::setConnection('db_backup');
        $result = [];
        foreach ($data as $a) {
            $result[] = $this->save('tb_presensi', $a);
        }
        return $result;
    }

    public function getDataInduk($data) {
        $idKey = [$data['kdlokasi'], $data['bulan'], $data['tahun']];
        $query = 'SELECT * FROM tb_induk WHERE kdlokasi = ? AND bulan = ? AND tahun = ?';
        $dataArr = $this->getData($query, $idKey);

        if ($dataArr['count'] > 0) :
            return $dataArr['value'][0];
        endif;

        return false;
    }

    public function getDataPersonil($data) {
        $idKey = array();
        $dataArr = array();
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
            $q_cari .= 'AND (nama_personil LIKE "%' . $data['cari'] . '%") ';
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
        $query = 'SELECT * FROM tb_laporan WHERE induk_id = "' . $induk_id . '"';
        $dataArr = $this->getData($query, []);

        if ($dataArr['count'] > 0) {
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
        $query = 'SELECT * FROM tb_tpp WHERE induk_id = "' . $induk_id . '" ';
        $dataArr = $this->getData($query, []);

        if ($dataArr['count'] > 0)
            return $dataArr['value'][0];

        return false;
    }

    public function getDataPersonilBatch($data, $raw = false) {
        $qcari = '';
        if (isset($data['pin_absen']) && $data['pin_absen'] != '') {
            $qcari = 'AND pin_absen IN (' . $data['pin_absen'] . ')';
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
        FROM tb_personil WHERE tampil_tpp = 1 AND induk_id = "' . $data['induk']['id'] . '" ' . $qcari;

        $query .= ' ORDER BY IF(urutan_sotk = "0" OR urutan_sotk = "" OR urutan_sotk IS NULL, 1, 0), IF(kd_jabatan = "" OR kd_jabatan = "-" OR kd_jabatan IS NULL, 1, 0), nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, nipbaru ASC';
        $result = $this->getData($query);

        if ($raw)
            return $result;

        $dataArr = [];
        if (isset($result['value'])) :
            foreach ($result['value'] as $i) {
                $dataArr[$i['pin_absen']] = [
                    'nip' => $i['nipbaru'],
                    'nama' => $i['nama_personil'],
                    'no_' => $i['nama_personil'],
                ];
            }
        endif;

        return $dataArr;
    }
    
    public function getDataPersonilBatch_v2($data) {
        $q_cari = '';
        if (isset($data['pin_absen']) && $data['pin_absen'] != '') {
            $q_cari .= 'AND pin_absen IN (' . $data['pin_absen'] . ')';
        }
        $result = $this->getData('SELECT * FROM tb_personil '
                . 'WHERE tampil_tpp = 1 AND induk_id = "' . $data['induk']['id'] . '" ' . $q_cari
                . 'ORDER BY kelas DESC, nama_personil ASC');
        return $result;
    }

    public function getBelumBackup($input, $induk) {
        $idKey = [$input['bulan'], $input['tahun']];
        $sudah = [];
        foreach ($induk['value'] as $i) :
            $sudah[] = $i['kdlokasi'];
        endforeach;

        $lap = $this->laporan_service->getData('SELECT * FROM tb_laporan '
                . 'WHERE bulan = ? AND tahun = ? AND sah_final IS NOT NULL ', $idKey);

        $belum = [];
        foreach ($lap['value'] as $j) {
            if (!in_array($j['kdlokasi'], $sudah)) :
                $belum[] = $j['kdlokasi'];
            endif;
        }

        return $belum;
    }

    public function dobackup($input, $w_presensi = true) {
        //$input['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
        $lokasi = $this->pegawai_service->getData('SELECT * FROM tref_lokasi_kerja WHERE status_lokasi_kerja = 1 AND kdlokasi = "' . $input['kdlokasi'] . '"');
        if ($lokasi['count'] > 0) {
            $input['singkatan_lokasi'] = $lokasi['value'][0]['singkatan_lokasi'];
            $input['nmlokasi'] = $lokasi['value'][0]['nmlokasi'];
        }

        $rekap = [];
        $laporan = $this->laporan_service->getLaporan($input);
        for ($i = 1; $i <= 6; $i++) {
            $input['tingkat'] = $i;
            $rekap[$i] = $this->getRekapAll($input, $laporan, true);
        }

        //simpan induk
        parent::setConnection('db_backup');
        $tbinduk = $this->save_induk($input);

        if ($tbinduk['error']) { //jk berhasil simpan
            //simpan laporan
            $tblaporan = $this->save_laporan($input, $tbinduk);

            //simpan personil
            $preData['input'] = $input;
            $preData['induk'] = $tbinduk;
            $preData['rekap'] = $rekap;
            $preData['presensi'] = $w_presensi;
            $tbpersonil = $this->save_personil($input, $tbinduk, $rekap, $w_presensi);

            //simpan tpp
            $tbtpp = $this->save_tpp($input, $tbinduk);

            if (!$tblaporan['error'] || !$tbpersonil['error'] || !$tbtpp['error']) {
                $this->hapusBackup($input);
                $result['error'] = false;
                return $result;
            }
        }

        return $tbinduk;
    }

    public function save_induk($input) {
        $induk = [
            'id' => '',
            'kdlokasi' => $input['kdlokasi'],
            'nmlokasi' => $input['nmlokasi'],
            'singkatan_lokasi' => $input['singkatan_lokasi'],
            'bulan' => $input['bulan'],
            'tahun' => $input['tahun'],
            'dateAdd' => date('Y-m-d H:i:s')
        ];
        $tbinduk = $this->save('tb_induk', $induk);
        return $tbinduk;
    }

    public function save_laporan($input, $tbinduk) {
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
            WHERE (vp.nipbaru in (" . $p . "))", []);

        parent::setConnection('db_presensi'); //ambil jabatan pengguna
        foreach ($person_ver['value'] as $i) {
            if ($datalap['ver_admin_opd'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP01' AND nipbaru = '" . $i['nipbaru'] . "' 
                    AND kdlokasi = '" . $input['kdlokasi'] . "' AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) :
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];
                endif;

                $i['tanggal'] = $datalap['dt_ver_admin_opd'];
                $verlap['admin_opd'] = $i;
            }
            if ($datalap['sah_kepala_opd'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP02' AND nipbaru = '" . $i['nipbaru'] . "' 
                    AND kdlokasi = '" . $input['kdlokasi'] . "' AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) :
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];
                endif;


                $i['tanggal'] = $datalap['dt_sah_kepala_opd'];
                $verlap['kepala_opd'] = $i;
                $verlap['stempel_opd'] = $input['kdlokasi'] . '.png';
            }
            if ($datalap['ver_admin_kota'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP03' AND nipbaru = '" . $i['nipbaru'] . "' 
                    AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) :
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];
                endif;

                $i['tanggal'] = $datalap['dt_ver_admin_kota'];
                $verlap['admin_kota'] = $i;
            }
            if ($datalap['sah_kepala_bkppd'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP04' AND nipbaru = '" . $i['nipbaru'] . "' 
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) {
                    if ($pengguna['value'][0]['jabatan_pengguna'] != NULL) :
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];
                    endif;

                    $verlap['stempel_bkppd'] = $pengguna['value'][0]['kdlokasi'] . '.png';
                }

                $i['tanggal'] = $datalap['dt_sah_kepala_bkppd'];
                $verlap['kepala_bkppd'] = $i;
            }

            if ($datalap['sah_final'] == $i['nipbaru']) {
                $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                    WHERE grup_pengguna_kd = 'KDGRUP02' AND nipbaru = '" . $i['nipbaru'] . "' 
                    AND kdlokasi = '" . $input['kdlokasi'] . "' AND jabatan_pengguna IS NOT NULL
                ", []);

                $i['jabatan_pengguna'] = '';
                if ($pengguna['count'] > 0) :
                    $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];
                endif;

                $i['tanggal'] = $datalap['dt_sah_final'];
                $verlap['final'] = $i;
            }
        }

        parent::setConnection('db_backup');
        $field = ['admin_opd', 'kepala_opd', 'admin_kota', 'kepala_bkppd', 'final'];
        $laporan['id'] = '';
        $laporan['induk_id'] = $tbinduk['inserted_id'];

        foreach ($field as $key) {
            $laporan['nip_' . $key] = $verlap[$key]['nipbaru'];
            $laporan['nama_' . $key] = $verlap[$key]['nama_personil'];
            $laporan['jabatan_' . $key] = $verlap[$key]['jabatan_pengguna'];
            $laporan['dt_' . $key] = $verlap[$key]['tanggal'];
        }

        $laporan['stempel_opd'] = $verlap['stempel_opd'];

        $laporan['stempel_bkppd'] = '';
        if (isset($verlap['stempel_bkppd'])) :
            $laporan['stempel_bkppd'] = $verlap['stempel_bkppd'];
        endif;

        $laporan['dateAdd'] = date('Y-m-d H:i:s');

        $tblaporan = $this->save('tb_laporan', $laporan);
        return $tblaporan;
    }

    public function getDataPersonilTpp($data) {
        parent::setConnection('db_pegawai');

        $idKey = array();
        $dataArr = array();
        $q_cari = 'WHERE 1 ';
        /* acil 20200802 */
        $q_carigaji = '';
        if (!empty($data['bulan']) && !empty($data['tahun'])) {
            $bulan = ($data['bulan'] == 12) ? 1 : $data['bulan'] + 1;
            $tahun = ($data['bulan'] == 12) ? $data['tahun'] + 1 : $data['tahun'];
            $q_carigaji .= 'AND (MONTH(gaji.periode) = ? AND YEAR(gaji.periode) = ?) ';
            array_push($idKey, $bulan, $tahun);
        }

        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (pp.kdlokasi = ?)';
            array_push($idKey, $data['kdlokasi']);
        }

        $query = 'SELECT pin_absen, pp.*, gaji.total AS totgaji, npwp, gol_jbtn, nominal_tp, tunjangan_jabatan, s.urutan_sotk, v.kd_jabatan_khusus, v.kd_tp, v.kd_ruang_jab,
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
            LEFT JOIN data_gaji gaji ON pp.nipbaru = gaji.nipbaru ' . $q_carigaji . '
            JOIN view_tpp_pegawai v ON v.nipbaru = pp.nipbaru ' . $q_cari;

        $query .= ' ORDER BY IF(urutan_sotk = "0" OR urutan_sotk = "" OR urutan_sotk IS NULL, 1, 0), IF(pp.kd_jabatan = "" OR pp.kd_jabatan = "-" OR pp.kd_jabatan IS NULL, 1, 0), v.nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, pp.nipbaru ASC';
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }

    public function save_personil_v2($input, $tbinduk, $rekap, $w_presensi) {
//        $pegawai = $this->laporan_service->getDataPersonilSatker_v2($input);
        $pegawai = $this->laporan_service->getDataPersonilTpp_v2($input, true);
        $pajak = $this->laporan_service->getArraypajak();
//        comp\FUNC::showPre($input);exit;

        parent::setConnection('db_backup');
        foreach ($pegawai['value'] as $peg) {
            $field = array_keys($this->getTabel('tb_personil'));

            foreach ($field as $i) :
                $p[$i] = (isset($peg[$i])) ? $peg[$i] : '';
            endforeach;

            if (empty($p['nominal_tp']) || $p['tunjangan_jabatan'] == 1) :
                $p['tampil_tpp'] = 0;
            else :
                $p['tampil_tpp'] = 1;
            endif;

            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $p['pajak_tpp'] = isset($pajak[$gol]) ? $pajak[$gol] : 0;
            $p['induk_id'] = $tbinduk['inserted_id'];
            $p['dateAdd'] = date('Y-m-d H:i:s');

            $tbpersonil = $this->save('tb_personil', $p);
            
            #simpan presensi
            $input['kinerja'] = 0;
            if ($tbpersonil['error'] && $w_presensi) {
                $peg['pajak_tpp'] = $p['pajak_tpp'];
                $tbpresensi = $this->save_presensi_v2($rekap, $peg, $tbpersonil['inserted_id'], $input['kenabpjs'], $input);
                if (!$tbpresensi['error']) :
                    return $tbpresensi;
                endif;
            }
        }
        return $tbpresensi;
    }
    
    public function save_personil_v3($input, $tbinduk, $rekap, $w_presensi) {
        $pegawai = $this->laporan_service->getDataPersonilTppToBackup_v3($input, true);
//        $pegawai = $this->laporan_service->getDataPersonilSatker_v2($input);
        $pajak = $this->laporan_service->getArraypajak();
        
        $arrPin = array_column($pegawai['value'], 'pin_absen');
        $impPin = implode(',', $arrPin);
        
        ################## Begin Get API Kinerja ####################
        //ambil data kinerja
        $url = 'http://pamomong.pekalongankota.go.id/e-kinerja-beta/super/api/';
        $method = 'poin_pns';
        $accesskey = ['kinerja-key' => 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0'];
        $request = array('pin' => $impPin, 'tahun' => $input['tahun'], 'bulan' => $input['bulan']);
        $kinerja = $this->webadapter->callAPI($url, $method, $accesskey, $request);
        if (count($kinerja) == 0) {
            return false;
        }
        
        $poin = [];
        if (!empty($kinerja)) {
            $arrNip = array_column($kinerja['data'], 'nip');
            $arrPoin = array_column($kinerja['data'], 'poin');
            $poin = array_combine($arrNip, $arrPoin);
        }
        ################# End Get API Kinerja ####################

        parent::setConnection('db_backup');
        foreach ($pegawai['value'] as $peg) {
            $field = array_keys($this->getTabel('tb_personil'));

            foreach ($field as $i) :
                $p[$i] = (isset($peg[$i])) ? $peg[$i] : '';
            endforeach;

            if (empty($p['nominal_tp']) || $p['tunjangan_jabatan'] == 1) :
                $p['tampil_tpp'] = 0;
            else :
                $p['tampil_tpp'] = 1;
            endif;

            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $p['pajak_tpp'] = isset($pajak[$gol]) ? $pajak[$gol] : 0;
            $p['induk_id'] = $tbinduk['inserted_id'];
            $p['dateAdd'] = date('Y-m-d H:i:s');

            $tbpersonil = $this->save('tb_personil', $p);
            
            ################## Begin Get API Kinerja ####################
            //ambil data kinerja
//            $url = 'http://pamomong.pekalongankota.go.id/e-kinerja-beta/super/api/';
//            $method = 'poin_pns';
//            $accesskey = ['kinerja-key' => 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0'];
//            $request = array('pin' => $input['personil'], 'tahun' => $input['tahun'], 'bulan' => $input['bulan']);
//            $kinerja = $this->webadapter->callAPI($url, $method, $accesskey, $request);
//            $poin = [];
//            if (!empty($kinerja)) {
//                $arrNip = array_column($kinerja['data'], 'nip');
//                $arrPoin = array_column($kinerja['data'], 'poin');
//                $poin = array_combine($arrNip, $arrPoin);
//            }
//
            ################# End Get API Kinerja ####################
            
            $input['kinerja'] = $poin;
//            comp\FUNC::showPre($input); exit;
            #simpan presensi
            if ($tbpersonil['error'] && $w_presensi) {
                $peg['pajak_tpp'] = $p['pajak_tpp'];
                $tbpresensi = $this->save_presensi_v3($rekap, $peg, $tbpersonil['inserted_id'], $input['kenabpjs'], $input);
                if (!$tbpresensi['error']) :
                    return $tbpresensi;
                endif;
            }
        }
        return $tbpresensi;
    }

    private function save_personil($input, $tbinduk, $rekap, $w_presensi) {
        $pegawai = $this->getDataPersonilTpp($input)['value'];

        parent::setConnection('db_backup');
        $pajak = $this->laporan_service->getArraypajak();

        foreach ($pegawai as $peg) {
            $p = $this->getTabel('tb_personil');
            $field = array_keys($p);

            foreach ($field as $i) {
                if ($i != 'id' && isset($peg[$i])) :
                    $p[$i] = $peg[$i];
                endif;
            }

            if ($peg['nominal_tp'] == 0 || $peg['tunjangan_jabatan'] == 1) :
                $p['tampil_tpp'] = 0;
            else :
                $p['tampil_tpp'] = 1;
            endif;

            //remove whitespace-- ambil % pajak
            $clean = str_replace(" ", "", $peg['golruang']);
            $gol = explode("/", $clean)[0];
            $p['pajak_tpp'] = isset($pajak[$gol]) ? $pajak[$gol] : 0;
            $p['induk_id'] = $tbinduk['inserted_id'];
            $p['dateAdd'] = date('Y-m-d H:i:s');

            $tbpersonil = $this->save('tb_personil', $p);
            //simpan presensi
            if ($tbpersonil['error'] && $w_presensi) {
                $peg['pajak_tpp'] = $p['pajak_tpp'];
                $tbpresensi = $this->save_presensi($rekap, $peg, $tbpersonil['inserted_id'], $input['kenabpjs']);
                if (!$tbpresensi['error']) :
                    return $tbpresensi;
                endif;
            }
        }

        return $tbpersonil;
    }

    public function save_presensi($rekap, $peg, $personil_id, $kenabpjs = []) {
        parent::setConnection('db_backup');
        $saveto = [];
        $pot_penuh = [];
        $sum_pot = [];
        $pin_absen = $peg['pin_absen'];
        $nominal_tp40 = $peg['nominal_tp'] * 40 / 100;
        $nominal_tp60 = $peg['nominal_tp'] * 60 / 100;

        for ($i = 1; $i <= 6; $i++) {
            $get = $rekap[$i][$pin_absen];
            $pot_penuh[$i] = $get['pot_penuh'];
            $sum_pot[$i] = $get['sum_pot'];
            foreach ($get as $tgl => $isi) {
                $saveto[$tgl][$i] = $isi;
            }
        }

        $final = ($sum_pot[6]['all'] > 100 ? 100 : $sum_pot[6]['all']);
        $presensi = [
            'id' => '',
            'personil_id' => $personil_id,
            'pot_penuh' => json_encode($pot_penuh),
            'sum_pot' => json_encode($sum_pot),
            'pot_final' => $final,
            'tpp_kotor' => $peg['nominal_tp']
        ];

        $pot = round(((is_numeric($final) ? $final : 100) / 100 * $nominal_tp60), -1); // potongan 60% dari nominal TPP
        $tpp_kotor = ($nominal_tp40 + $nominal_tp60) - $pot;
        $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);
        $presensi['tpp_bersih'] = $tpp_kotor - $pot_pajak;

        $checkBpjsGaji = round((($peg['nominal_tp'] + $peg['totgaji']) > $kenabpjs['value']) ?
                ($kenabpjs['value'] - $peg['totgaji']) * 0.01 :
                $peg['nominal_tp'] * 0.01);
        $pot_bpjs = ($presensi['tpp_bersih'] > $checkBpjsGaji) ? $checkBpjsGaji : $presensi['tpp_bersih'];
        $terima_potbpjs = $presensi['tpp_bersih'] - $pot_bpjs;
        $presensi['pot_bpjskes'] = $pot_bpjs;
        $presensi['tpp_terima'] = $terima_potbpjs;

        for ($i = 1; $i <= 31; $i++) {
            $presensi['t' . $i] = (isset($saveto[$i]) ? json_encode($saveto[$i]) : "{}");
        }
        $presensi['dateAdd'] = date('Y-m-d H:i:s');

        $tbpresensi = $this->save('tb_presensi', $presensi);
        if ($tbpresensi['error']) :
            $this->update('tb_personil', ['backup_presensi' => 1], ['id' => $personil_id]);
        endif;

        return $tbpresensi;
    }

    public function save_presensi_v2($rekap, $peg, $personil_id, $kenabpjs = [], $input = []) {
        parent::setConnection('db_backup');
        $saveto = [];
        $pot_penuh = [];
        $sum_pot = [];
        $pin_absen = $peg['pin_absen'];

        for ($i = 1; $i <= 6; $i++) {
            $get = $rekap[$i][$pin_absen];
            $pot_penuh[$i] = $get['pot_penuh'];

            ################# Start Mengenolkan TPP Sekolah ####################
            $bln_tanpapot = [4];
            $thn_tanpapot = [2021];
            $grub_tanpapot = ['G13', 'G14', 'G15', 'G16'];
            if (in_array($input['bulan'], $bln_tanpapot) && in_array($input['tahun'], $thn_tanpapot) && in_array($input['satker']['kd_kelompok_lokasi_kerja'], $grub_tanpapot) && $i == 6) {
                $get['sum_pot'] = ['mk' => 0, 'ap' => 0, 'pk' => 0, 'all' => 0];
            }
            ################# End Mengenolkan TPP Sekolah ####################

            $sum_pot[$i] = $get['sum_pot'];
            foreach ($get as $tgl => $isi) {
                $saveto[$tgl][$i] = $isi;
            }
        }

        $nominal_tpp = isset($peg['nominal_tp']) ? $peg['nominal_tp'] : 0;
        $final = ($sum_pot[6]['all'] > 100 ? 100 : $sum_pot[6]['all']);

        $presensi = [
            'id' => '',
            'personil_id' => $personil_id,
            'pot_penuh' => json_encode($pot_penuh),
            'sum_pot' => json_encode($sum_pot),
            'pot_final' => $final,
            'poin_kinerja' => isset($input['kinerja'][$peg['nipbaru']]) ? $input['kinerja'][$peg['nipbaru']] : 0,
            'tpp_kotor' => $nominal_tpp
        ];

        $nominal_tp40 = $nominal_tpp * 40 / 100;
        $nominal_tp60 = $nominal_tpp * 60 / 100;

        $pot = round((is_numeric($final) ? $final : 100) / 100 * $nominal_tp60, -1);

        $tpp_kotor = $nominal_tp40 + $nominal_tp60 - $pot;
        $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);
        $presensi['tpp_bersih'] = $tpp_kotor - $pot_pajak;

        $checkBpjsGaji = round((($nominal_tpp + $peg['totgaji']) > $kenabpjs['value']) ?
                ($kenabpjs['value'] - $peg['totgaji']) * 0.01 :
                $nominal_tpp * 0.01);
        $pot_bpjs = ($presensi['tpp_bersih'] > $checkBpjsGaji) ? $checkBpjsGaji : $presensi['tpp_bersih'];
        $terima_potbpjs = $presensi['tpp_bersih'] - $pot_bpjs;
        $presensi['pot_bpjskes'] = $pot_bpjs;
        $presensi['tpp_terima'] = $terima_potbpjs;

        for ($i = 1; $i <= 31; $i++) {
            $presensi['t' . $i] = (isset($saveto[$i]) ? json_encode($saveto[$i]) : "{}");
        }
        $presensi['dateAdd'] = date('Y-m-d H:i:s');
//        comp\FUNC::showPre($presensi);//exit;


        $tbpresensi = $this->save('tb_presensi', $presensi);
        if ($tbpresensi['error']) :
            $this->update('tb_personil', ['backup_presensi' => 1], ['id' => $personil_id]);
        endif;

        return $tbpresensi;
    }
    
    public function save_presensi_v3($rekap, $peg, $personil_id, $kenabpjs = [], $input = []) {
        parent::setConnection('db_backup');
        $saveto = [];
        $pot_penuh = [];
        $sum_pot = [];
        $pin_absen = $peg['pin_absen'];

        for ($i = 1; $i <= 6; $i++) {
            $get = $rekap[$i][$pin_absen];
            $pot_penuh[$i] = $get['pot_penuh'];

            ################# Begin Mengenolkan TPP Sekolah ####################
            $bln_tanpapot = [4];
            $thn_tanpapot = [2021];
            $grub_tanpapot = ['G13', 'G14', 'G15', 'G16'];
            if (in_array($input['bulan'], $bln_tanpapot) && in_array($input['tahun'], $thn_tanpapot) && in_array($input['satker']['kd_kelompok_lokasi_kerja'], $grub_tanpapot) && $i == 6) {
                $get['sum_pot'] = ['mk' => 0, 'ap' => 0, 'pk' => 0, 'all' => 0];
            }
            ################# End Mengenolkan TPP Sekolah ####################

            $sum_pot[$i] = $get['sum_pot'];
            foreach ($get as $tgl => $isi) {
                $saveto[$tgl][$i] = $isi;
            }
        }

        $nominal_tpp = isset($peg['nominal_tp']) ? $peg['nominal_tp'] : 0;
        $final = ($sum_pot[6]['all'] > 100 ? 100 : $sum_pot[6]['all']);

        $presensi = [
            'id' => '',
            'personil_id' => $personil_id,
            'pot_penuh' => json_encode($pot_penuh),
            'sum_pot' => json_encode($sum_pot),
            'pot_final' => $final,
            'poin_kinerja' => isset($input['kinerja'][$peg['nipbaru']]) ? $input['kinerja'][$peg['nipbaru']] : 0,
            'tpp_kotor' => $nominal_tpp
        ];

//        $nominal_tp40 = $nominal_tpp * 40 / 100;
        $nominal_tp36 = round($nominal_tpp * 36 / 100);
        $nominal_tp24 = round($nominal_tpp * 24 / 100);

        $pot_tp36 = round((is_numeric($final) ? $final : 100) / 100 * $nominal_tp36, 0);
        $pot_tp24 = round((100 - $presensi['poin_kinerja']) / 100 * $nominal_tp24, 0);

        $tpp_kotor = $nominal_tpp - ($pot_tp36 + $pot_tp24);
        $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);
        $presensi['tpp_bersih'] = $tpp_kotor - $pot_pajak;

        $checkBpjsGaji = round((($nominal_tpp + $peg['totgaji']) > $kenabpjs['value']) ?
                ($kenabpjs['value'] - $peg['totgaji']) * 0.01 :
                $nominal_tpp * 0.01);
        $pot_bpjs = ($presensi['tpp_bersih'] > $checkBpjsGaji) ? $checkBpjsGaji : $presensi['tpp_bersih'];
        $terima_potbpjs = $presensi['tpp_bersih'] - $pot_bpjs;
        $presensi['pot_bpjskes'] = $pot_bpjs;
        $presensi['tpp_terima'] = $terima_potbpjs;

        for ($i = 1; $i <= 31; $i++) {
            $presensi['t' . $i] = (isset($saveto[$i]) ? json_encode($saveto[$i]) : "{}");
        }
        $presensi['dateAdd'] = date('Y-m-d H:i:s');
//        comp\FUNC::showPre($presensi);//exit;


        $tbpresensi = $this->save('tb_presensi', $presensi);
        if ($tbpresensi['error']) :
            $this->update('tb_personil', ['backup_presensi' => 1], ['id' => $personil_id]);
        endif;

        return $tbpresensi;
    }

    public function save_tpp($input, $tbinduk) {
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
            /* $this->delete('tb_laporan', $idKeys);
              $this->delete('tb_personil', $idKeys);
              $this->delete('tb_tpp', $idKeys); */
        }

        return true;
    }

    public function getRekapAllView($induk_id, $pin_absen = '') {
        $cond = "";
        if ($pin_absen != '')
            $cond = " AND tb_personil.pin_absen IN (" . $pin_absen . ")";

        $presensi = $this->getData('SELECT tb_presensi.* FROM tb_presensi
            JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
            JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
            WHERE tb_induk.id="' . $induk_id . '"' . $cond, []);

        $response = [];
        if ($presensi['count'] > 0) {
            $get = $presensi['value'];
            foreach ($get as $isi) {
                $response[$isi['personil_id']] = $isi;
            }
        }

        return $response;
    }

    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * ****************   BACKUP DATA PRESENSI LAPORAN    ******************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */
    /*     * *********************************************************************** */

    public function checkMasuk($data, $jadwal = []) {
        $check = [];
        foreach ($data as $i) {
            $pin = $i['pin_absen'];
            $tgl = (int) date('d', strtotime($i['tanggal_log_presensi']));
            $finger = strtotime($i['jam_log_presensi']);

            //default jadwal
            $awal = strtotime('05:45:00');
            $batas = strtotime('07:16:00');
            $akhir = strtotime('11:30:00');
            $libur = false;

            if (isset($jadwal[$pin][$tgl])) {
                if ($jadwal[$pin][$tgl] != 'HL') {
                    $awal = strtotime($jadwal[$pin][$tgl]['awal']);
                    $batas = strtotime($jadwal[$pin][$tgl]['batas']);
                    $akhir = strtotime($jadwal[$pin][$tgl]['akhir']);

                    //jk batas akhir melewati 00:00
                    if ($akhir < $awal) {
                        if ($awal > $batas)
                            $awal = strtotime($jadwal[$pin][$tgl]['awal'] . ' -24 Hour');
                        if ($akhir < $batas)
                            $akhir = strtotime($jadwal[$pin][$tgl]['akhir'] . ' +24 Hour');
                    }
                } else
                    $libur = true;
            }

            $kode = '';
            $waktu = substr($i['jam_log_presensi'], 0, 5);
            if ($libur) {//libur tapi finger
                //$waktu = ($format == 'A' ? 'M1' : substr($i['jam_log_presensi'], 0, 5));
                $kode = 'M1';
            } elseif ($finger < $awal || $finger > $akhir) {
                //$masuk = 'M0';
                $kode = 'M0';
            } elseif ($finger <= $batas) {
                //$masuk = ($format == 'A' ? 'M1' : substr($i['jam_log_presensi'], 0, 5));
                $kode = 'M1';
            } else {
                $telat = $finger - $batas;
                if ($telat < 960)  //15*60*60
                    $kode = 'M2';
                elseif ($telat < 1860)
                    $kode = 'M3';
                elseif ($telat < 3600)
                    $kode = 'M4';
                else
                    $kode = 'M5';
            }

            $masuk = [
                'waktu' => $waktu,
                'kode' => $kode
            ];

            //handle multiple fingerprint
            if (isset($check[$pin][$tgl]) && !in_array($check[$pin][$tgl]['kode'], ['M2', 'M3', 'M4', 'M5', 'M0']))
                continue;

            if (isset($check[$pin][$tgl]) && in_array($masuk['kode'], ['M2', 'M3', 'M4', 'M5', 'M0'])) {
                $isi = $check[$pin][$tgl];
                $angka_isi = substr($isi['kode'], 1, 1);
                $angka_masuk = substr($masuk['kode'], 1, 1);

                if ($masuk['kode'] == 'M0' || ($isi['kode'] != 'M0' && $angka_masuk > $angka_isi))
                    continue;
            }

            $check[$pin][$tgl] = $masuk;
        }

        foreach ($jadwal as $key => $i) {
            foreach ($i as $tgl => $val) {
                if (!is_array($val) && $val == 'HL' && !isset($check[$key][$tgl])) {
                    //$check[$key][$tgl] = 'HL';
                    $check[$key][$tgl] = [
                        'waktu' => 'HL',
                        'kode' => 'HL'
                    ];
                }
            }
        }

        return $check;
    }

    public function checkPulang($data, $jadwal = []) {
        $check = [];
        $bln = null;
        $thn = null;
        foreach ($data as $i) {
            $pin = $i['pin_absen'];
            $finger = strtotime($i['jam_log_presensi']);
            $tgl = (int) date('d', strtotime($i['tanggal_log_presensi']));
            $hari = date('l', strtotime($i['tanggal_log_presensi']));

            if (!$bln)
                $bln = (int) date('m', strtotime($i['tanggal_log_presensi']));

            if (!$thn)
                $thn = (int) date('Y', strtotime($i['tanggal_log_presensi']));

            //default jadwal
            $awal = strtotime('11:30:00');
            $batas = strtotime('15:45:00');
            $akhir = strtotime('19:46:00');
            if ($hari == 'Friday') {
                $awal = strtotime('10:52:00');
                $batas = strtotime('14:30:00');
                $akhir = strtotime('18:31:00');
            }

            $sebelum = $tgl - 1;
            $libur = false;
            $unset = false;
            $bedabulan = false;
            if ((int) date('m', strtotime($i['tanggal_log_presensi'])) != $bln) {
                $bedabulan = true;
                $sebelum = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
            }

            if (isset($jadwal[$pin][$sebelum]) && $jadwal[$pin][$sebelum] != 'HL' && $jadwal[$pin][$sebelum]['shiftmalam'] && $finger <= strtotime($jadwal[$pin][$sebelum]['akhir'])) {
                $tgl = $sebelum;
            } elseif ($bedabulan)
                continue;

            if (isset($jadwal[$pin][$tgl])) {
                if ($jadwal[$pin][$tgl] != 'HL') {
                    $awal = strtotime($jadwal[$pin][$tgl]['awal']);
                    $batas = strtotime($jadwal[$pin][$tgl]['batas']);
                    $akhir = strtotime($jadwal[$pin][$tgl]['akhir']);

                    //jk batas akhir melewati 00:00
                    if ($akhir < $awal) {
                        if ($awal > $batas)
                            $awal = strtotime($jadwal[$pin][$tgl]['awal'] . ' -24 Hour');
                        if ($akhir < $batas)
                            $akhir = strtotime($jadwal[$pin][$tgl]['akhir'] . ' +24 Hour');
                    }
                } else
                    $libur = true;
            }

            $kode = '';
            $waktu = substr($i['jam_log_presensi'], 0, 5);
            if ($libur) {//libur tapi finger
                //$pulang = ($format == 'A' ? 'P1' : substr($i['jam_log_presensi'], 0, 5));
                $kode = 'P1';
            } elseif ($finger < $awal || $finger > $akhir) { //jk finger tidak sesuai ketentuan
                //$pulang = 'P0';
                $kode = 'P0';
            } elseif ($finger >= $batas) {
                //$pulang = ($format == 'A' ? 'P1' : substr($i['jam_log_presensi'], 0, 5));
                $kode = 'P1';
            } else {
                $dahulu = $batas - $finger;
                if ($dahulu < 960)  //15*60*60
                    $kode = 'P2';
                elseif ($dahulu < 1860)
                    $kode = 'P3';
                elseif ($dahulu < 3600)
                    $kode = 'P4';
                else
                    $kode = 'P5';
            }

            $pulang = [
                'waktu' => $waktu,
                'kode' => $kode
            ];

            //handle multiple fingerprint
            if (isset($check[$pin][$tgl]) && !in_array($check[$pin][$tgl]['kode'], ['P2', 'P3', 'P4', 'P5', 'P0']))
                continue;

            if (isset($check[$pin][$tgl]) && in_array($pulang['kode'], ['P2', 'P3', 'P4', 'P5', 'P0'])) {
                $isi = $check[$pin][$tgl];
                $angka_isi = substr($isi['kode'], 1, 1);
                $angka_pulang = substr($pulang['kode'], 1, 1);

                if ($pulang['kode'] == 'P0' || ($isi['kode'] != 'P0' && $angka_pulang > $angka_isi))
                    continue;
            }

            $check[$pin][$tgl] = $pulang;
        }

        foreach ($jadwal as $key => $i) {
            foreach ($i as $tgl => $val) {
                if (!is_array($val) && $val == 'HL' && !isset($check[$key][$tgl])) {
                    //$check[$key][$tgl] = 'HL';
                    $check[$key][$tgl] = [
                        'waktu' => 'HL',
                        'kode' => 'HL'
                    ];
                }
            }
        }

        return $check;
    }

    public function getLogPersonil($data) {
        parent::setConnection('db_presensi');

        $params = [$data['bulan'], $data['tahun']];
        $masuk = "SELECT * FROM tb_log_presensi WHERE pin_absen in (" . $data['pin_absen'] . ")
             AND MONTH(tanggal_log_presensi) = ? AND YEAR(tanggal_log_presensi) = ? AND status_log_presensi = 0 ORDER BY tanggal_log_presensi ASC, jam_log_presensi ASC";
        $get_masuk = $this->getData($masuk, $params);

        //ambil data bulan itu s.d tgl 1 bln brikutnya
        $batas_awal = $data['tahun'] . '-' . $data['bulan'] . '-1';
        $thn_akhir = $data['bulan'] == 12 ? ($data['tahun'] + 1) : $data['tahun'];
        $bln_akhir = $data['bulan'] == 12 ? 1 : ($data['bulan'] + 1);
        $batas_akhir = $thn_akhir . '-' . $bln_akhir . '-1';
        $params = [$batas_awal, $batas_akhir];
        $pulang = "SELECT * FROM tb_log_presensi WHERE pin_absen in (" . $data['pin_absen'] . ")
             AND (tanggal_log_presensi BETWEEN ? AND ?) AND status_log_presensi = 1 ORDER BY 
             tanggal_log_presensi ASC, jam_log_presensi DESC";
        $get_pulang = $this->getData($pulang, $params);

        $jadwal = $this->laporan_service->getJadwalkerja($data);
        $check['masuk'] = $this->checkMasuk($get_masuk['value'], $jadwal['masuk']);
        $check['pulang'] = $this->checkPulang($get_pulang['value'], $jadwal['pulang']);

        return $check;
    }

    public function getBatalApel($data) {
        parent::setConnection('db_presensi');
        $get = $this->getData("SELECT * FROM tb_batal_apel WHERE MONTH(tanggal_apel) = ? AND YEAR(tanggal_apel) = ? AND status_batal = 1", [$data['bulan'], $data['tahun']]);

        $rekap = [];
        foreach ($get['value'] as $i) {
            $tgl = (int) date('d', strtotime($i['tanggal_apel']));
            $rekap[$i['pin_absen']][$tgl] = true;
        }

        return $rekap;
    }

    public function getMasukkerja($data) {
        parent::setConnection('db_presensi');
        //$params = [$data['kdlokasi'], $data['bulan'], $data['tahun']];
        /* $get = $this->getData("SELECT * FROM view_jadwal WHERE kdlokasi = ? 
          AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
          ", $params); */

        $params = [$data['bulan'], $data['tahun']];
        $get = $this->getData("SELECT * FROM view_jadwal 
            WHERE pin_absen in (" . $data['personil'] . ") AND MONTH(tanggal) = ? 
            AND YEAR(tanggal) = ?
        ", $params);

        $masuk = [];
        foreach ($get['value'] as $i) {
            $tgl = (int) date('d', strtotime($i['tanggal']));
            $pin = $i['pin_absen'];

            if ($i['masuk'] == '00:00:00')
                $masuk[$pin][$tgl] = 'HL';
            else
                $masuk[$pin][$tgl] = $i['masuk'];
        }
        return $masuk;
    }

    public function getArrayJam($id = null) {
        parent::setConnection('db_presensi');

        $q_cari = '';
        if ($id)
            $q_cari = ' AND WHERE id_jam_apel = "' . $id . '"';

        $data = $this->getData('SELECT * FROM tb_jam_apel WHERE is_default=0 ' . $q_cari . ' ORDER BY id_jam_apel DESC');
        $jamapel = [];
        foreach ($data['value'] as $i) {
            for ($a = strtotime($i['tanggal_mulai']); $a <= strtotime($i['tanggal_akhir']);) {
                $jamapel[date('Y-m-d', $a)] = [
                    'awal' => $i['mulai_apel'],
                    'akhir' => $i['akhir_apel']
                ];
                $a += 86400;
            }
        }
        return $jamapel;
    }

    public function compare($tanggal, $finger, $jamapel, $jammasuk = '') {
        parent::setConnection('db_presensi');

        if (!isset($jamapel[$tanggal])) {
            $default = $this->getData('SELECT * FROM tb_jam_apel WHERE is_default=1 ORDER BY id_jam_apel DESC', []);
            if ($default['count'] > 0) {
                $awal = $default['value'][0]['mulai_apel'];
                $akhir = $default['value'][0]['akhir_apel'];
            } else {
                $awal = '07:15:00';
                $akhir = '08:00:00';
            }
        } else {
            $awal = $jamapel[$tanggal]['awal'];
            $akhir = $jamapel[$tanggal]['akhir'];
        }

        $awal = strtotime($awal);
        $akhir = strtotime($akhir);
        $apel = strtotime($finger);

        if ($jammasuk != '' && ($jammasuk < $awal || $jammasuk >= $akhir)) {
            return 'NR';
        }

        if ($apel > $awal && $apel <= $akhir)
            return 1;
        else
            return 0;
    }

    public function getRecordApel($data) {
        parent::setConnection('db_presensi');
        $idKey = array($data['bulan'], $data['tahun']);
        $batal = $this->getBatalApel($data);
        /* $query = 'SELECT * FROM view_apel_all '
          . 'WHERE (MONTH(tanggal_log_presensi) = ?) AND (YEAR(tanggal_log_presensi) = ?) AND pin_absen in ('.$data['personil'].') '
          . 'ORDER BY tanggal_log_presensi DESC, jam_log_presensi DESC'; */
        $query = 'SELECT * FROM tb_log_presensi '
                . 'WHERE (MONTH(tanggal_log_presensi) = ?) AND (YEAR(tanggal_log_presensi) = ?) AND pin_absen in (' . $data['personil'] . ') AND status_log_presensi = 2 '
                . 'ORDER BY tanggal_log_presensi DESC, jam_log_presensi DESC';
        $dataArr = $this->getData($query, $idKey);

        $result = [];
        //if ($dataArr['count'] == 0)
        //return $result;

        $masuk = $this->getMasukkerja($data);
        $jadwal_apel = $this->getArrayJam();
        foreach ($dataArr['value'] as $i) {
            $tgl = (int) date('d', strtotime($i['tanggal_log_presensi']));
            $pin_absen = $i['pin_absen'];

            $jammasuk = '';
            if (isset($masuk[$pin_absen][$tgl])) {
                if ($masuk[$pin_absen][$tgl] != 'HL') {
                    $jammasuk = \DateTime::createFromFormat('H:i:s', $masuk[$pin_absen][$tgl]);
                    $jammasuk = strtotime($jammasuk->modify('+1 minutes')->format('H:i:s'));
                    unset($masuk[$pin_absen][$tgl]);
                }
            }

            $compare = $this->compare($i['tanggal_log_presensi'], $i['jam_log_presensi'], $jadwal_apel, $jammasuk);

            $kode = '';
            $waktu = substr($i['jam_log_presensi'], 0, 5);
            if ($compare == 1) {
                if (isset($batal[$pin_absen][$tgl]))
                    $kode = 'A0';
                else
                    $kode = 'A1';
            } elseif ($compare == 0) {
                $kode = 'A0';
            }

            if ($compare && $compare == 'NR')
                $kode = 'NR';

            if (isset($result[$pin_absen][$tgl]) && $result[$pin_absen][$tgl]['kode'] == 'A1' && $kode = 'A0')
                continue;

            $result[$pin_absen][$tgl] = [
                'waktu' => $waktu,
                'kode' => $kode
            ];
        }

        foreach ($masuk as $key => $i) {
            foreach ($i as $tgl => $val) {
                $hari = date("l", strtotime($data['tahun'] . '-' . $data['bulan'] . '-' . $tgl));
                if (!is_array($val) && $val == 'HL') {
                    if ($hari == 'Saturday' || $hari == 'Sunday')
                        $kode = 'HL';
                    else
                        $kode = 'NR';

                    $result[$key][$tgl] = [
                        'waktu' => $kode,
                        'kode' => $kode
                    ];
                } else {
                    $jammasuk = \DateTime::createFromFormat('H:i:s', $val);
                    $jammasuk = strtotime($jammasuk->modify('+1 minutes')->format('H:i:s'));

                    $bln = ($data['bulan'] < 10 ? '0' : '') . $data['bulan'];
                    $tgl_full = $data['tahun'] . '-' . $bln . '-' . ($tgl < 10 ? '0' : '') . $tgl;
                    $compare = $this->compare($tgl_full, '00:00:00', $jadwal_apel, $jammasuk);

                    if ($compare && $compare == 'NR') {
                        $kode = 'NR';
                        $result[$key][$tgl] = [
                            'waktu' => $kode,
                            'kode' => $kode
                        ];
                    }

                    //jk masuk hari sabtu / minggu NR
                    if ($hari == 'Saturday' || $hari == 'Sunday') {
                        $kode = 'NR';
                        $result[$key][$tgl] = [
                            'waktu' => $kode,
                            'kode' => $kode
                        ];
                    }
                }
            }
        }

        return $result;
    }

    public function getRekapAll($data, $laporan, $hitungpot = false) {
//        comp\FUNC::showPre($data);exit;
        $moderasi = $this->laporan_service->getArraymodAll($data, $laporan);
        $libur = $this->laporan_service->getLibur($data);
        $data_pot = $this->laporan_service->getArraypot();
        $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        $hitungmod = $moderasi['hitung'];

        $data['pin_absen'] = $data['personil'];
        $log = $this->getLogPersonil($data);
        $masuk = $log['masuk'];
        $pulang = $log['pulang'];
        $apel = $this->getRecordApel($data);

        $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $tot = 0;
            $key = $peg['pin_absen'];
            $sum_mk = 0;
            $sum_ap = 0;
            $sum_pk = 0;
            $pot_penuh = [];
            $jumlah_tk = 0;
            $hitungpot = true;
            for ($i = 1; $i <= $hitungtgl; $i++) {
                //bln desember 2018 ttp dihitung brdasarkan presensi s/d tgl 14
                if ($data['bulan'] == 12 && $data['tahun'] == 2018 && $i > 14) :
                    $hitungpot = false;
                endif;

                $tgl = $data['tahun'] . '-' . $data['bulan'] . '-' . $i;
                $hari = date("l", strtotime($tgl));

                $kd_masuk = '';
                $kd_apel = '';
                $kd_pulang = '';
                $pot_masuk = 0;
                $pot_apel = 0;
                $pot_pulang = 0;
                $color1 = '';
                $color2 = '';
                $color3 = '';
                $hl = false;
                if (isset($masuk[$key][$i])) {

                    if ($masuk[$key][$i]['kode'] == 'HL') :
                        $hl = true;
                    else :
                        $kd_masuk = $masuk[$key][$i]['kode'];
                    endif;

                    if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0'])) :
                        $color1 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color1 = 'yellow accent-2';
                    $kd_masuk = 'M0';
                }

                if (isset($apel[$key][$i])) {
                    if ($apel[$key][$i]['kode'] != 'HL') :
                        $kd_apel = $apel[$key][$i]['kode'];
                    endif;

                    if ($kd_apel == 'A0') :
                        $color2 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color2 = 'yellow accent-2';
                    $kd_apel = 'A0';
                }

                if (isset($pulang[$key][$i])) {
                    if ($pulang[$key][$i]['kode'] != 'HL') :
                        $kd_pulang = $pulang[$key][$i]['kode'];
                    endif;

                    if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0'])) :
                        $color3 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color3 = 'yellow accent-2';
                    $kd_pulang = 'P0';
                }

                $gabung = false;
                $tampil_mod = true;
                if (in_array($i, $libur)) {
                    $tampil_mod = false;
                    $kd_masuk = 'HL';
                    $kd_apel = 'HL';
                    $kd_pulang = 'HL';
                    $color1 = '';
                    $color2 = '';
                    $color3 = '';
                    //libur nasional tpi finger
                    if (isset($masuk[$key][$i]) && $masuk[$key][$i]['kode'] != 'HL') {
                        $tampil_mod = true;
                        $kd_masuk = $masuk[$key][$i]['kode'];
                        if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                            $color1 = 'yellow accent-2';
                    }
                    if (isset($pulang[$key][$i]) && $pulang[$key][$i]['kode'] != 'HL') {
                        $tampil_mod = true;
                        $kd_pulang = $pulang[$key][$i]['kode'];
                        if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0']))
                            $color3 = 'yellow accent-2';
                    }
                } elseif (strtotime($tgl) > strtotime(date('Y-m-d'))) {
                    $tampil_mod = false;
                    $kd_masuk = '';
                    $kd_pulang = '';
                    $color1 = '';
                    $color2 = '';
                    $color3 = '';
                }

                if ($tampil_mod && isset($moderasi[$key][$i])) {
                    foreach ($moderasi[$key][$i] as $jnsmod => $modr) {
                        $ver = $moderasi[$key][$i][$jnsmod]['verified'];
                        if ($ver != null && ($ver == 0 || $ver == 3)) :
                            continue;
                        endif;

                        if ($ver == null) :
                            $allverified = false;
                        endif;

                        if ($kd_masuk && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD01')) {
                            $color1 = 'red accent-3';
                            $kd_masuk = $modr['kode'];
                        }
                        if ($kd_apel && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD02')) {
                            $color2 = 'red accent-3';
                            $kd_apel = $modr['kode'];
                        }
                        if ($kd_pulang && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD03')) {
                            $color3 = 'red accent-3';
                            $kd_pulang = $modr['kode'];
                        }

                        //jk jenisnya semuanya atau kode moderasi masuk, apel, pulang sama dalam 1 hari maka potongan dijasikan 1
                        if ($jnsmod == 'JNSMOD04' || ($kd_apel == $kd_masuk && $kd_pulang == $kd_masuk)) :
                            $gabung = true;
                        endif;
                        /*
                          //jk jenisnya semuanya, potongan dijadikan 1
                          if ($jnsmod == 'JNSMOD04')
                          $gabung = true;
                         */
                    }
                }

                //jk M0 && A0 && P0 ---> jadi TK (tidak masuk kerja tanpa alasan yg sah)
                if ($kd_masuk == 'M0' && ($kd_apel == 'A0' || $kd_apel == 'NR') && $kd_pulang == 'P0') {
                    $kd_masuk = 'TK';
                    $kd_apel = 'TK';
                    $kd_pulang = 'TK';
                    $color2 = 'yellow accent-2';
                }

                if ($hitungpot) {
                    $hitung = 1;
                    if ($kd_masuk != 'M0') :
                        $hitung = isset($hitungmod[$key][$kd_masuk]) ? $hitungmod[$key][$kd_masuk] : 1;
                    endif;

                    if ($kd_masuk && isset($data_pot[$kd_masuk])) {
                        foreach ($data_pot[$kd_masuk] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_masuk = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_masuk == 100) :
                            $pot_penuh[] = $kd_masuk;
                        endif;
                    }

                    if ($kd_apel != 'A0')
                        $hitung = isset($hitungmod[$key][$kd_apel]) ? $hitungmod[$key][$kd_apel] : 1;

                    if ($kd_apel && isset($data_pot[$kd_apel])) {
                        foreach ($data_pot[$kd_apel] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_apel = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_apel == 100)
                            $pot_penuh[] = $kd_apel;

                        //jk kode sama, potongan jadi 1
                        if ($kd_apel == $kd_masuk)
                            $pot_apel = 0;
                    }

                    if ($kd_pulang != 'P0')
                        $hitung = isset($hitungmod[$key][$kd_pulang]) ? $hitungmod[$key][$kd_pulang] : 1;

                    if ($kd_pulang && isset($data_pot[$kd_pulang])) {
                        foreach ($data_pot[$kd_pulang] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_pulang = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_pulang == 100)
                            $pot_penuh[] = $kd_pulang;

                        //jk kode sama, potongan jadi 1
                        if ($kd_pulang == $kd_masuk || $kd_pulang == $kd_apel)
                            $pot_pulang = 0;
                    }
                }

                if ($gabung) {
                    $pot_apel = 0;
                    $pot_pulang = 0;
                }

                $subtot = $pot_masuk + $pot_apel + $pot_pulang;
                $all[$key][$i] = [
                    'mk' => [
                        'waktu' => (isset($masuk[$key][$i]) ? $masuk[$key][$i]['waktu'] : $kd_masuk),
                        'kode' => $kd_masuk,
                        'pot' => ($pot_masuk > 0 ? $pot_masuk : ''),
                        'color' => $color1
                    ],
                    'ap' => [
                        'waktu' => (isset($apel[$key][$i]) ? $apel[$key][$i]['waktu'] : $kd_apel),
                        'kode' => $kd_apel,
                        'pot' => ($pot_apel > 0 ? $pot_apel : ''),
                        'color' => $color2
                    ],
                    'pk' => [
                        'waktu' => (isset($pulang[$key][$i]) ? $pulang[$key][$i]['waktu'] : $kd_pulang),
                        'kode' => $kd_pulang,
                        'pot' => ($pot_pulang > 0 ? $pot_pulang : ''),
                        'color' => $color3
                    ],
                    'all' => ($subtot > 0 ? $subtot : '')
                ];

                if ($hitungpot) :
                    $sum_mk += $pot_masuk;
                endif;
                $sum_ap += $pot_apel;
                $sum_pk += $pot_pulang;

                if ($kd_masuk == 'TK') :
                    $jumlah_tk++;
                endif;

                if ($jumlah_tk >= 7) :
                    $pot_penuh[] = 'TK';
                endif;
            }

            $all[$key]['pot_penuh'] = array_unique($pot_penuh);

            if (count($pot_penuh) == 0) {
                $tot = ($sum_mk + $sum_ap + $sum_pk);
            } else {
                $implode = implode(",", $all[$key]['pot_penuh']);
                $tot = "100% (" . $implode . ")";
            }

            $all[$key]['sum_pot'] = [
                'mk' => $sum_mk, 'ap' => $sum_ap, 'pk' => $sum_pk,
                'all' => $tot
            ];
        }

        $all['allverified'] = $allverified;
        return $all;
    }
    
    public function getRekapAll_v2($data, $laporan, $hitungpot = false) {
        $moderasi = $this->laporan_service->getArraymodAll($data, $laporan);
        $libur = $this->laporan_service->getLibur($data);
        $data_pot = $this->laporan_service->getArraypot();
        $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        $hitungmod = $moderasi['hitung'];

        $data['pin_absen'] = $data['personil'];
        $log = $this->getLogPersonil($data);
        $masuk = $log['masuk'];
        $pulang = $log['pulang'];
        $apel = $this->getRecordApel($data);

        $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $tot = 0;
            $key = $peg['pin_absen'];
            $sum_mk = 0;
            $sum_ap = 0;
            $sum_pk = 0;
            $pot_penuh = [];
            $jumlah_tk = 0;
            $hitungpot = true;
            for ($i = 1; $i <= $hitungtgl; $i++) {

                $tgl = $data['tahun'] . '-' . $data['bulan'] . '-' . $i;
//                $hari = date("l", strtotime($tgl));

                $kd_masuk = '';
                $kd_apel = '';
                $kd_pulang = '';
                $pot_masuk = 0;
                $pot_apel = 0;
                $pot_pulang = 0;
                $color1 = '';
                $color2 = '';
                $color3 = '';
                $hl = false;
                if (isset($masuk[$key][$i])) {

                    if ($masuk[$key][$i]['kode'] == 'HL') :
                        $hl = true;
                    else :
                        $kd_masuk = $masuk[$key][$i]['kode'];
                    endif;

                    if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0'])) :
                        $color1 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color1 = 'yellow accent-2';
                    $kd_masuk = 'M0';
                }

                if (isset($apel[$key][$i])) {
                    if ($apel[$key][$i]['kode'] != 'HL') :
                        $kd_apel = $apel[$key][$i]['kode'];
                    endif;

                    if ($kd_apel == 'A0') :
                        $color2 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color2 = 'yellow accent-2';
                    $kd_apel = 'A0';
                }

                if (isset($pulang[$key][$i])) {
                    if ($pulang[$key][$i]['kode'] != 'HL') :
                        $kd_pulang = $pulang[$key][$i]['kode'];
                    endif;

                    if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0'])) :
                        $color3 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color3 = 'yellow accent-2';
                    $kd_pulang = 'P0';
                }

                $gabung = false;
                $tampil_mod = true;
                if (in_array($i, $libur)) {
                    $tampil_mod = false;
                    $kd_masuk = 'HL';
                    $kd_apel = 'HL';
                    $kd_pulang = 'HL';
                    $color1 = '';
                    $color2 = '';
                    $color3 = '';
                    //libur nasional tpi finger
                    if (isset($masuk[$key][$i]) && $masuk[$key][$i]['kode'] != 'HL') {
                        $tampil_mod = true;
                        $kd_masuk = $masuk[$key][$i]['kode'];
                        if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0'])) :
                            $color1 = 'yellow accent-2';
                        endif;
                    }
                    if (isset($pulang[$key][$i]) && $pulang[$key][$i]['kode'] != 'HL') {
                        $tampil_mod = true;
                        $kd_pulang = $pulang[$key][$i]['kode'];
                        if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0'])) :
                            $color3 = 'yellow accent-2';
                        endif;
                    }
                } elseif (strtotime($tgl) > strtotime(date('Y-m-d'))) {
                    $tampil_mod = false;
                    $kd_masuk = '';
                    $kd_pulang = '';
                    $color1 = '';
                    $color2 = '';
                    $color3 = '';
                }

                if ($tampil_mod && isset($moderasi[$key][$i])) {
                    foreach ($moderasi[$key][$i] as $jnsmod => $modr) {
                        $ver = $moderasi[$key][$i][$jnsmod]['verified'];
                        if ($ver != null && ($ver == 0 || $ver == 3)) :
                            continue;
                        endif;

                        if ($ver == null) :
                            $allverified = false;
                        endif;

                        if ($kd_masuk && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD01')) {
                            $color1 = 'red accent-3';
                            $kd_masuk = $modr['kode'];
                        }
                        if ($kd_apel && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD02')) {
                            $color2 = 'red accent-3';
                            $kd_apel = $modr['kode'];
                        }
                        if ($kd_pulang && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD03')) {
                            $color3 = 'red accent-3';
                            $kd_pulang = $modr['kode'];
                        }

                        //jk jenisnya semuanya atau kode moderasi masuk, apel, pulang sama dalam 1 hari maka potongan dijasikan 1
                        if ($jnsmod == 'JNSMOD04' || ($kd_apel == $kd_masuk && $kd_pulang == $kd_masuk)) :
                            $gabung = true;
                        endif;
                        
                    }
                }

                //jk M0 && A0 && P0 ---> jadi TK (tidak masuk kerja tanpa alasan yg sah)
                if ($kd_masuk == 'M0' && ($kd_apel == 'A0' || $kd_apel == 'NR') && $kd_pulang == 'P0') {
                    $kd_masuk = 'TK';
                    $kd_apel = 'TK';
                    $kd_pulang = 'TK';
                    $color2 = 'yellow accent-2';
                }

                if ($hitungpot) {
                    $hitung = 1;
                    if ($kd_masuk != 'M0') :
                        $hitung = isset($hitungmod[$key][$kd_masuk]) ? $hitungmod[$key][$kd_masuk] : 1;
                    endif;

                    if ($kd_masuk && isset($data_pot[$kd_masuk])) {
                        foreach ($data_pot[$kd_masuk] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_masuk = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_masuk == 100) :
                            $pot_penuh[] = $kd_masuk;
                        endif;
                    }

                    if ($kd_apel != 'A0') :
                        $hitung = isset($hitungmod[$key][$kd_apel]) ? $hitungmod[$key][$kd_apel] : 1;
                    endif;

                    if ($kd_apel && isset($data_pot[$kd_apel])) {
                        foreach ($data_pot[$kd_apel] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_apel = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_apel == 100) :
                            $pot_penuh[] = $kd_apel;
                        endif;

                        //jk kode sama, potongan jadi 1
                        if ($kd_apel == $kd_masuk) :
                            $pot_apel = 0;
                        endif;
                    }

                    if ($kd_pulang != 'P0') :
                        $hitung = isset($hitungmod[$key][$kd_pulang]) ? $hitungmod[$key][$kd_pulang] : 1;
                    endif;

                    if ($kd_pulang && isset($data_pot[$kd_pulang])) {
                        foreach ($data_pot[$kd_pulang] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_pulang = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_pulang == 100) :
                            $pot_penuh[] = $kd_pulang;
                        endif;

                        //jk kode sama, potongan jadi 1
                        if ($kd_pulang == $kd_masuk || $kd_pulang == $kd_apel) :
                            $pot_pulang = 0;
                        endif;
                    }
                }

                if ($gabung) {
                    $pot_apel = 0;
                    $pot_pulang = 0;
                }

                $subtot = $pot_masuk + $pot_apel + $pot_pulang;
                $all[$key][$i] = [
                    'mk' => [
                        'waktu' => (isset($masuk[$key][$i]) ? $masuk[$key][$i]['waktu'] : $kd_masuk),
                        'kode' => $kd_masuk,
                        'pot' => ($pot_masuk > 0 ? $pot_masuk : ''),
                        'color' => $color1
                    ],
                    'ap' => [
                        'waktu' => (isset($apel[$key][$i]) ? $apel[$key][$i]['waktu'] : $kd_apel),
                        'kode' => $kd_apel,
                        'pot' => ($pot_apel > 0 ? $pot_apel : ''),
                        'color' => $color2
                    ],
                    'pk' => [
                        'waktu' => (isset($pulang[$key][$i]) ? $pulang[$key][$i]['waktu'] : $kd_pulang),
                        'kode' => $kd_pulang,
                        'pot' => ($pot_pulang > 0 ? $pot_pulang : ''),
                        'color' => $color3
                    ],
                    'all' => ($subtot > 0 ? $subtot : '')
                ];

                if ($hitungpot) :
                    $sum_mk += $pot_masuk;
                endif;
                $sum_ap += $pot_apel;
                $sum_pk += $pot_pulang;

                if ($kd_masuk == 'TK') :
                    $jumlah_tk++;
                endif;

                if ($jumlah_tk >= 10) :
                    $pot_penuh[] = 'TK';
                endif;
            }

            $all[$key]['pot_penuh'] = array_unique($pot_penuh);

            if (count($pot_penuh) == 0) {
                $tot = ($sum_mk + $sum_ap + $sum_pk);
            } else {
                $implode = implode(",", $all[$key]['pot_penuh']);
                $tot = "100% (" . $implode . ")";
            }

            $all[$key]['sum_pot'] = [
                'mk' => $sum_mk, 'ap' => $sum_ap, 'pk' => $sum_pk,
                'all' => $tot
            ];
        }

        $all['allverified'] = $allverified;
        return $all;
    }

//-----------------------------------------------------------------------//
    public function save_cpns($induk_id, $data, $rekap) {
        parent::setConnection('db_backup');

        $count = 0;
        $presensi = 0;
        foreach ($data['pegawai']['value'] as $peg) {
            $p = $this->getTabel('tb_personil');
            $field = array_keys($p);

            foreach ($field as $i) {
                if ($i != 'id' && isset($peg[$i]))
                    $p[$i] = $peg[$i];
            }

            $p['induk_id'] = $induk_id;
            $p['dateAdd'] = date('Y-m-d H:i:s');

            $check = $this->getData('SELECT * FROM tb_personil WHERE induk_id = ? AND nipbaru = ?', [$induk_id, $peg['nipbaru']]);

            $tbpersonil['error'] = false;
            if ($check['count'] == 0)
                $tbpersonil = $this->save('tb_personil', $p);
            else {
                $save = $check['value'][0];
                $tbpersonil['inserted_id'] = $save['id'];
                if ($save['backup_presensi'] == 0) //belum save data presensi
                    $tbpersonil['error'] = true;
            }

            if ($tbpersonil['error']) {
                $count++;
                $tbpresensi = $this->save_presensi($rekap, $peg['pin_absen'], $tbpersonil['inserted_id']);
                if ($tbpresensi['error']) {
                    $presensi++;
                    $this->update('tb_personil', ['backup_presensi' => 1], ['id' => $tbpersonil['inserted_id']]);
                }
            }
        }

        $obj = [
            'id' => '',
            'induk_id' => $induk_id,
            'kdlokasi' => $data['kdlokasi'],
            'count' => $count,
            'presensi' => $presensi,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $obj = $this->save('tb_check', $obj);

        return $obj;
    }

    public function update_presensi($rekap, $personil) {
        parent::setConnection('db_backup');
        $saveto = [];
        $pot_penuh = [];
        $sum_pot = [];
        $pin_absen = $personil['pin_absen'];
        for ($i = 1; $i <= 6; $i++) {
            $get = $rekap[$i][$pin_absen];
            $pot_penuh[$i] = $get['pot_penuh'];
            $sum_pot[$i] = $get['sum_pot'];
            foreach ($get as $tgl => $isi) {
                $saveto[$tgl][$i] = $isi;
            }
        }

        $final = ($sum_pot[6]['all'] > 100 ? 100 : $sum_pot[6]['all']);
        $presensi = [
            'id' => ($personil['presensi_id'] ? $personil['presensi_id'] : ''),
            'personil_id' => $personil['id'],
            'pot_penuh' => json_encode($pot_penuh),
            'sum_pot' => json_encode($sum_pot),
            'pot_final' => $final,
            'tpp_kotor' => $personil['nominal_tp']
        ];

        $pot = ($final / 100 * $personil['nominal_tp']);
        $tpp_kotor = $personil['nominal_tp'] - $pot;
        $pot_pajak = round($personil['pajak_tpp'] * $tpp_kotor);
        $presensi['tpp_terima'] = $tpp_kotor - $pot_pajak;

        for ($i = 1; $i <= 31; $i++) {
            $presensi['t' . $i] = (isset($saveto[$i]) ? json_encode($saveto[$i]) : "{}");
        }
        $presensi['dateAdd'] = date('Y-m-d H:i:s');
        $tbpresensi = $this->save('tb_presensi', $presensi);

        return $tbpresensi;
    }

    public function updatetampil($induk_id, $personil) {
        if (is_null($this->db)) {
            parent::setConnection('db_backup');
            $this->openConnection();
        }

        $sql = "UPDATE tb_personil SET tampil_tpp = 0 
            WHERE induk_id = '" . $induk_id . "' AND nipbaru IN (" . $personil . ")";
        try {
            $result = $this->db->prepare($sql);
            return $result->execute();
        } catch (Exception $e) {
            $response = "ERROR ON : " . $e->getMessages();
            return $response;
        }
    }

    /*     * **********************************TPP DES************************************************* */

    public function getRekapAllCustom($data, $laporan, $hitungpot = false, $custom = false) {
        $moderasi = $this->laporan_service->getArraymodAll($data, $laporan);
        $libur = $this->laporan_service->getLibur($data);
        $data_pot = $this->laporan_service->getArraypot();
        //$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        if (is_array($custom)) {
            $tglawal = $custom['awal'];
            $hitungtgl = $custom['akhir'];
        } else {
            $tglawal = 1;
            $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        }

        $hitungmod = $moderasi['hitung'];

        $data['pin_absen'] = $data['personil'];
        $log = $this->getLogPersonil($data);
        $masuk = $log['masuk'];
        $pulang = $log['pulang'];
        $apel = $this->getRecordApel($data);

        $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $tot = 0;
            $key = $peg['pin_absen'];
            $sum_mk = 0;
            $sum_ap = 0;
            $sum_pk = 0;
            $pot_penuh = [];
            $jumlah_tk = 0;
            $hitungpot = true;
            for ($i = 1; $i <= $hitungtgl; $i++) {
                //bln desember 2018 ttp dihitung brdasarkan presensi s/d tgl 14
                if ($data['bulan'] == 12 && $data['tahun'] == 2018 && $i > 14)
                    $hitungpot = false;

                $tgl = $data['tahun'] . '-' . $data['bulan'] . '-' . $i;
                $hari = date("l", strtotime($tgl));

                $kd_masuk = '';
                $kd_apel = '';
                $kd_pulang = '';
                $pot_masuk = 0;
                $pot_apel = 0;
                $pot_pulang = 0;
                $color1 = '';
                $color2 = '';
                $color3 = '';
                $hl = false;
                if (isset($masuk[$key][$i])) {
                    if ($masuk[$key][$i]['kode'] == 'HL')
                        $hl = true;
                    else
                        $kd_masuk = $masuk[$key][$i]['kode'];

                    if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                        $color1 = 'yellow accent-2';
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color1 = 'yellow accent-2';
                    $kd_masuk = 'M0';
                }

                if (isset($apel[$key][$i])) {
                    if ($apel[$key][$i]['kode'] != 'HL')
                        $kd_apel = $apel[$key][$i]['kode'];

                    if ($kd_apel == 'A0')
                        $color2 = 'yellow accent-2';
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color2 = 'yellow accent-2';
                    $kd_apel = 'A0';
                }

                if (isset($pulang[$key][$i])) {
                    if ($pulang[$key][$i]['kode'] != 'HL')
                        $kd_pulang = $pulang[$key][$i]['kode'];

                    if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0']))
                        $color3 = 'yellow accent-2';
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color3 = 'yellow accent-2';
                    $kd_pulang = 'P0';
                }

                $gabung = false;
                $tampil_mod = true;
                if (in_array($i, $libur)) {
                    $tampil_mod = false;
                    $kd_masuk = 'HL';
                    $kd_apel = 'HL';
                    $kd_pulang = 'HL';
                    $color1 = '';
                    $color2 = '';
                    $color3 = '';
                    //libur nasional tpi finger
                    if (isset($masuk[$key][$i]) && $masuk[$key][$i]['kode'] != 'HL') {
                        $tampil_mod = true;
                        $kd_masuk = $masuk[$key][$i]['kode'];
                        if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                            $color1 = 'yellow accent-2';
                    }
                    if (isset($pulang[$key][$i]) && $pulang[$key][$i]['kode'] != 'HL') {
                        $tampil_mod = true;
                        $kd_pulang = $pulang[$key][$i]['kode'];
                        if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0']))
                            $color3 = 'yellow accent-2';
                    }
                } elseif (strtotime($tgl) > strtotime(date('Y-m-d'))) {
                    $tampil_mod = false;
                    $kd_masuk = '';
                    $kd_pulang = '';
                    $color1 = '';
                    $color2 = '';
                    $color3 = '';
                }

                if ($tampil_mod && isset($moderasi[$key][$i])) {
                    foreach ($moderasi[$key][$i] as $jnsmod => $modr) {
                        $ver = $moderasi[$key][$i][$jnsmod]['verified'];
                        if ($ver != null && ($ver == 0 || $ver == 3))
                            continue;

                        if ($ver == null)
                            $allverified = false;

                        if ($kd_masuk && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD01')) {
                            $color1 = 'red accent-3';
                            $kd_masuk = $modr['kode'];
                        }
                        if ($kd_apel && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD02')) {
                            $color2 = 'red accent-3';
                            $kd_apel = $modr['kode'];
                        }
                        if ($kd_pulang && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD03')) {
                            $color3 = 'red accent-3';
                            $kd_pulang = $modr['kode'];
                        }

                        //jk jenisnya semuanya atau kode moderasi masuk, apel, pulang sama dalam 1 hari maka potongan dijasikan 1
                        if ($jnsmod == 'JNSMOD04' || ($kd_apel == $kd_masuk && $kd_pulang == $kd_masuk))
                            $gabung = true;
                        /*
                          //jk jenisnya semuanya, potongan dijadikan 1
                          if ($jnsmod == 'JNSMOD04')
                          $gabung = true;
                         */
                    }
                }

                //jk M0 && A0 && P0 ---> jadi TK (tidak masuk kerja tanpa alasan yg sah)
                if ($kd_masuk == 'M0' && ($kd_apel == 'A0' || $kd_apel == 'NR') && $kd_pulang == 'P0') {
                    $kd_masuk = 'TK';
                    $kd_apel = 'TK';
                    $kd_pulang = 'TK';
                    $color2 = 'yellow accent-2';
                }

                if ($hitungpot) {
                    $hitung = 1;
                    if ($kd_masuk != 'M0')
                        $hitung = isset($hitungmod[$key][$kd_masuk]) ? $hitungmod[$key][$kd_masuk] : 1;

                    if ($kd_masuk && isset($data_pot[$kd_masuk])) {
                        foreach ($data_pot[$kd_masuk] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_masuk = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_masuk == 100)
                            $pot_penuh[] = $kd_masuk;
                    }

                    if ($kd_apel != 'A0')
                        $hitung = isset($hitungmod[$key][$kd_apel]) ? $hitungmod[$key][$kd_apel] : 1;

                    if ($kd_apel && isset($data_pot[$kd_apel])) {
                        foreach ($data_pot[$kd_apel] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_apel = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_apel == 100)
                            $pot_penuh[] = $kd_apel;

                        //jk kode sama, potongan jadi 1
                        if ($kd_apel == $kd_masuk)
                            $pot_apel = 0;
                    }

                    if ($kd_pulang != 'P0')
                        $hitung = isset($hitungmod[$key][$kd_pulang]) ? $hitungmod[$key][$kd_pulang] : 1;

                    if ($kd_pulang && isset($data_pot[$kd_pulang])) {
                        foreach ($data_pot[$kd_pulang] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_pulang = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_pulang == 100)
                            $pot_penuh[] = $kd_pulang;

                        //jk kode sama, potongan jadi 1
                        if ($kd_pulang == $kd_masuk || $kd_pulang == $kd_apel)
                            $pot_pulang = 0;
                    }
                }

                if ($gabung) {
                    $pot_apel = 0;
                    $pot_pulang = 0;
                }

                $subtot = $pot_masuk + $pot_apel + $pot_pulang;
                $all[$key][$i] = [
                    'mk' => [
                        'waktu' => (isset($masuk[$key][$i]) ? $masuk[$key][$i]['waktu'] : $kd_masuk),
                        'kode' => $kd_masuk,
                        'pot' => ($pot_masuk > 0 ? $pot_masuk : ''),
                        'color' => $color1
                    ],
                    'ap' => [
                        'waktu' => (isset($apel[$key][$i]) ? $apel[$key][$i]['waktu'] : $kd_apel),
                        'kode' => $kd_apel,
                        'pot' => ($pot_apel > 0 ? $pot_apel : ''),
                        'color' => $color2
                    ],
                    'pk' => [
                        'waktu' => (isset($pulang[$key][$i]) ? $pulang[$key][$i]['waktu'] : $kd_pulang),
                        'kode' => $kd_pulang,
                        'pot' => ($pot_pulang > 0 ? $pot_pulang : ''),
                        'color' => $color3
                    ],
                    'all' => ($subtot > 0 ? $subtot : '')
                ];

                if ($hitungpot)
                    $sum_mk += $pot_masuk;
                $sum_ap += $pot_apel;
                $sum_pk += $pot_pulang;

                if ($kd_masuk == 'TK')
                    $jumlah_tk++;

                if ($jumlah_tk >= 7)
                    $pot_penuh[] = 'TK';
            }

            $all[$key]['pot_penuh'] = array_unique($pot_penuh);

            if (count($pot_penuh) == 0) {
                $tot = ($sum_mk + $sum_ap + $sum_pk);
            } else {
                $implode = implode(",", $all[$key]['pot_penuh']);
                $tot = "100% (" . $implode . ")";
            }

            $all[$key]['sum_pot'] = [
                'mk' => $sum_mk, 'ap' => $sum_ap, 'pk' => $sum_pk,
                'all' => $tot
            ];
        }

        $all['allverified'] = $allverified;
        return $all;
    }

    public function dobackup_des($input, $w_presensi = true) {
        //$input['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
        $lokasi = $this->pegawai_service->getData('SELECT * FROM tref_lokasi_kerja WHERE status_lokasi_kerja = 1 AND kdlokasi = "' . $input['kdlokasi'] . '"');
        if ($lokasi['count'] > 0) {
            $input['singkatan_lokasi'] = $lokasi['value'][0]['singkatan_lokasi'];
            $input['nmlokasi'] = $lokasi['value'][0]['nmlokasi'];
        }

        $custom = [
            'awal' => 1,
            'akhir' => 16
        ];
        $rekap = [];
        $laporan = $this->laporan_service->getLaporan($input);
        for ($i = 1; $i <= 3; $i++) {
            $input['tingkat'] = $i;
            $rekap[$i] = $this->getRekapAllCustom($input, $laporan, true, $custom);
        }

        //simpan induk
        parent::setConnection('db_backup');
        $tbinduk = $this->save_induk($input);

        if ($tbinduk['error']) { //jk berhasil simpan
            //simpan personil
            $tbpersonil = $this->save_personil_des($input, $tbinduk, $rekap, true);

            if (!$tbpersonil['error']) {
                $this->hapusBackup($input);
                $result['error'] = false;
                return $result;
            }
        }

        return $tbinduk;
    }

    private function save_personil_des($input, $tbinduk, $rekap, $w_presensi) {
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
            //simpan presensi
            if ($tbpersonil['error'] && $w_presensi) {
                $peg['pajak_tpp'] = $p['pajak_tpp'];
                $tbpresensi = $this->save_presensi_des($rekap, $peg, $tbpersonil['inserted_id']);
                if (!$tbpresensi['error'])
                    return $tbpresensi;
            }
        }

        return $tbpersonil;
    }

    public function save_presensi_des($rekap, $peg, $personil_id) {
        parent::setConnection('db_backup');
        $saveto = [];
        $pot_penuh = [];
        $sum_pot = [];
        $pin_absen = $peg['pin_absen'];
        for ($i = 1; $i <= 3; $i++) {
            $get = $rekap[$i][$pin_absen];
            $pot_penuh[$i] = $get['pot_penuh'];
            $sum_pot[$i] = $get['sum_pot'];
            foreach ($get as $tgl => $isi) {
                $saveto[$tgl][$i] = $isi;
            }
        }

        $final = ($sum_pot[3]['all'] > 100 ? 100 : $sum_pot[3]['all']);

        $presensi = [
            'id' => '',
            'personil_id' => $personil_id,
            'pot_penuh' => json_encode($pot_penuh),
            'sum_pot' => json_encode($sum_pot),
            'pot_final' => $final,
            'tpp_kotor' => $peg['nominal_tp']
        ];

        $pot = ($final / 100 * $peg['nominal_tp']);
        $tpp_kotor = $peg['nominal_tp'] - $pot;
        $pot_pajak = round($peg['pajak_tpp'] * $tpp_kotor);
        $presensi['tpp_terima'] = $tpp_kotor - $pot_pajak;

        for ($i = 1; $i <= 31; $i++) {
            $presensi['t' . $i] = (isset($saveto[$i]) ? json_encode($saveto[$i]) : "{}");
        }
        $presensi['dateAdd'] = date('Y-m-d H:i:s');
        $tbpresensi = $this->save('tb_presensi', $presensi);
        if ($tbpresensi['error'])
            $this->update('tb_personil', ['backup_presensi' => 1], ['id' => $personil_id]);

        return $tbpresensi;
    }

    public function update_bpjs($presensi, $kenabpjs, $gaji) {
        parent::setConnection('db_backup');
        $totgaji = $gaji[$presensi['nipbaru']];
        $bersih = $presensi['tpp_bersih'];

        $pot_bpjs = round((($bersih + $totgaji) > $kenabpjs['value']) ? ($kenabpjs['value'] - $totgaji) * 0.01 : $bersih * 0.01);
        $terima_potbpjs = $bersih - $pot_bpjs;

        $personil_id = $presensi['id'];
        $update['pot_bpjskes'] = $pot_bpjs;
        $update['tpp_terima'] = $terima_potbpjs;

        //$tbpresensi = $this->update('tb_presensi', $presensi);
        $tbpresensi = $this->update('tb_presensi', $update, ['id' => $personil_id]);

        return $tbpresensi;
    }

}
