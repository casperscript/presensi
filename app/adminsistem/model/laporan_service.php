<?php

namespace app\adminsistem\model;

use system;
use comp\FUNC;
use app\adminsistem\model\apelpagi_service;

class laporan_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
        $this->apelpagi_service = new apelpagi_service();
    }

    /*     * **************** Get data personil berdasarkan kdlokasi ************************** */

    public function getDataPersonilSatker($data) {
        parent::setConnection('db_pegawai');

        $idKey = array();
        $dataArr = array();
        $q_cari = 'WHERE 1 ';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (v.kdlokasi = ?)';
            array_push($idKey, $data['kdlokasi']);
        }

        //sementara hanya PNS SAJA
        $q_cari .= 'AND (v.status = "PNS")';
        /*
          if (!empty($data['status'])) {
          $q_cari .= 'AND (status = ?)';
          array_push($idKey, $data['status']);
          } */

        if (!empty($data['cari'])) {
            $cari = '%' . $data['cari'] . '%';
            $q_cari .= ' AND ((nama_personil LIKE ?) || (pin_absen LIKE ?)) ';
            array_push($idKey, $cari, $cari);
        }

        $query = 'SELECT v.*, s.urutan_sotk,
            (CASE 
                WHEN LOCATE("I/", golruang) = 0 THEN 4
                WHEN LOCATE("I/", golruang) = 1 THEN 1
                WHEN LOCATE("I/", golruang) = 2 THEN 2
                WHEN LOCATE("I/", golruang) = 3 THEN 3
                ELSE NULL
            END) AS golruang_1,
            RIGHT(golruang, 1) AS golruang_2

            FROM view_presensi_personal v 
            LEFT JOIN tref_sotk s ON s.kdsotk = v.kdsotk
        ' . $q_cari;

        $query .= ' ORDER BY ISNULL(urutan_sotk), urutan_sotk ASC, IF(v.kd_jabatan = "" OR v.kd_jabatan = "-" OR v.kd_jabatan IS NULL, 1, 0), golruang_1 DESC, golruang_2 DESC, nipbaru ASC';
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }
    
    public function getDataPersonilSatker_v2($data) {
        parent::setConnection('db_pegawai');

        $idKey = [];
        $q_carigaji = '';
        if (!empty($data['bulan']) && !empty($data['tahun'])) {
            $bulan = ($data['bulan'] == 12) ? '"' . 1 . '"' : '"' . $data['bulan'] + 1 . '"';
            $tahun = ($data['bulan'] == 12) ? '"' . $data['tahun'] + 1 . '"' : '"' . $data['tahun'] . '"';
            $q_carigaji .= 'AND (MONTH(gaji.periode) = ? AND YEAR(gaji.periode) = ?) ';
            array_push($idKey, $bulan);
            array_push($idKey, $tahun);
        }
        
        $q_cari = 'WHERE 1 ';
        if (!empty($data['kdlokasi'])) :
            $q_cari .= 'AND ((pegawai.kdlokasi = ?) OR (pegawai.kdsublokasi = ?)) ';
            array_push($idKey, $data['kdlokasi'], $data['kdlokasi']);
        endif;
        
        if (!empty($data['cari'])) :
            $cari = '%' . $data['cari'] . '%';
            $q_cari .= 'AND ((personal.nama_personil = ?) OR (pegawai.pin_absen = ?)) ';
            array_push($idKey, $cari, $cari);
        endif;
        
        $query = 'SELECT
            pegawai.nipbaru            AS nipbaru,
            pegawai.pin_absen          AS pin_absen,
            CONCAT(
                    personal.gelar_depan,
                    IF((personal.gelar_depan <> ""), " ", ""),
                    personal.namapeg,
                    IF((personal.gelar_blkg <> ""), " ", ""),
                    personal.gelar_blkg
                ) AS nama_personil,
            IF((pegawai.kdsublokasi = ""), pegawai.kdlokasi, pegawai.kdsublokasi) AS kdlokasi,
            pegawai.kd_jabatan         AS kd_jabatan,
            personal.npwp              AS npwp,
            personal.nama_R_jabatan    AS nama_R_jabatan,
            pegawai.golruang           AS golruang,
            personal.path_foto_pegawai AS foto_pegawai,
            gaji.total               AS totgaji
        FROM texisting_kepegawaian pegawai
            JOIN texisting_personal personal
                ON pegawai.nipbaru = personal.nipbaru
            LEFT JOIN data_gaji gaji 
                ON pegawai.nipbaru = gaji.nipbaru ' . $q_carigaji . '
            LEFT JOIN tref_jabatan_campur jabatan
                ON jabatan.kd_jabatan = pegawai.kd_jabatan ' . $q_cari;
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }
    
    public function getDataPersonilTpp_v2($data, $backup = false) {
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
        
        $q_offbackup = '';
        if ($backup == false) {
            $q_offbackup = 'AND pegawai.`kd_stspeg` IN ("04", "29") AND pegawai.`tunjangan_jabatan` = 0 ';
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
                pegawai.`tunjangan_jabatan`     AS tunjangan_jabatan,
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
            WHERE 1 ' . $q_cari . $q_offbackup . '
                AND (pegawai.`kode_sert_guru` != "01" OR jabatan.`kelas` IS NOT NULL)
            ORDER BY ISNULL(kelas.`kelas`), IF(COALESCE(pegawai.`kelas_on_pegawai`), pegawai.`kelas_on_pegawai`, kelas.`kelas`) DESC, nama_personil ASC';
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }
    
    public function getDataPersonilTppToBackup_v3($data, $backup = false) {
        parent::setConnection('db_pegawai');
        
        $idKey = array();
        $q_carigaji = '';
        if (!empty($data['bulan']) && !empty($data['tahun'])) {
            $bulan = ($data['bulan'] == 12) ? 1 : $data['bulan'] + 1;
            $tahun = ($data['bulan'] == 12) ? $data['tahun'] + 1 : $data['tahun'];
            $q_carigaji .= 'AND gaji.bulan = ? AND gaji.tahun = ? ';
            array_push($idKey, $bulan, $tahun);
        }
        
        $q_cari = '';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND ((pegawai.kdlokasi = ?) OR (pegawai.kdsublokasi = ?)) ';
            array_push($idKey, $data['kdlokasi'], $data['kdlokasi']);
        }
        
        $q_offbackup = '';
        if ($backup == false) {
            $q_offbackup = 'AND pegawai.`kd_stspeg` IN ("04", "29") AND pegawai.`tunjangan_jabatan` = 0 ';
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
                pegawai.`tunjangan_jabatan`     AS tunjangan_jabatan,
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
            WHERE 1 ' . $q_cari . $q_offbackup . '
            ORDER BY ISNULL(kelas.`kelas`), IF(COALESCE(pegawai.`kelas_on_pegawai`), pegawai.`kelas_on_pegawai`, kelas.`kelas`) DESC, nama_personil ASC';
        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }

    /*     * **************** Get data personil batch where in ************************** */

    public function getDataPersonilBatch($ids, $raw = false) {
        parent::setConnection('db_pegawai');

        if ($ids == '') :
            return [];
        endif;

        $query = 'SELECT * FROM view_presensi_personal WHERE pin_absen IN (' . $ids . ')';
        $result = $this->getData($query);

        if ($raw) :
            return $result;
        endif;

        $dataArr = [];
        if (isset($result['value'])) :
            foreach ($result['value'] as $i) {
                $dataArr[$i['pin_absen']] = [
                    'nip' => $i['nipbaru'],
                    'nama' => $i['nama_personil']
                ];
            }
        endif;

        return $dataArr;
    }

    public function getPilKelSatker($cari = array()) {   // daftar kelompok lokasi kerja
        parent::setConnection('db_pegawai');

        $field = $this->getTabel('tref_kelompok_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        foreach ($field as $key => $val) {
            if (isset($cari[$key])) {
                $q_cari = 'AND (' . $key . ' = ?) ';
                array_push($idKey, $cari[$key]);
            }
        }
        $data = $this->getData('SELECT * FROM tref_kelompok_lokasi_kerja WHERE 1 ' . $q_cari . ' ORDER BY kd_kelompok_lokasi_kerja', $idKey);
        if ($data['count'] > 0) {
            foreach ($data['value'] as $kol) {
                $result[$kol['kd_kelompok_lokasi_kerja']] = $kol['nama_kelompok_lokasi_kerja'];
            }
        } else {
            $result = array('' => '');
        }
        return $result;
    }

    public function getPilLokasi($input = array()) {
        parent::setConnection('db_pegawai');
        $field = $this->getTabel('tref_lokasi_kerja');
        $idKey = array();
        $q_cari = '';
        if (isset($input['cari'])) {
            $q_cari = 'AND (singkatan_lokasi LIKE ? OR nmlokasi LIKE ? OR kdlokasi LIKE ?) ';
            $cari = '%' . $input['cari'] . '%';
            array_push($idKey, $cari, $cari, $cari);
        }
        foreach ($field as $key => $val) {
            if (isset($input[$key])) {
                $q_cari .= 'AND (' . $key . ' = ?) ';
                array_push($idKey, $input[$key]);
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

    /*     * **************** Get Data ************************** */

    public function getDataPersonil($id) {
        parent::setConnection('db_pegawai');

        $query = 'SELECT * FROM view_presensi_personal WHERE (pin_absen = ?)';
        $result = $this->getData($query, array($id));
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('view_presensi_personal');
        }
    }

    /*     * **************** Get Tabel ************************** */

    public function getTabelPersonil($data) {
        parent::setConnection('db_pegawai');

        $idKey = array();
        $page = (!empty($data['page'])) ? $data['page'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $q_cari = 'WHERE 1 ';
        if (!empty($data['kdlokasi'])) {
            $q_cari .= 'AND (kdlokasi = ?) ';
            array_push($idKey, $data['kdlokasi']);
        }

        if (!empty($data['cari'])) {
            $q_cari .= 'AND (nama_personil LIKE "%' . $data['cari'] . '%") ';
        }

        //hanya pns
        $q_cari .= 'AND (status = "PNS")';

        $query = 'SELECT * FROM view_presensi_personal ' . $q_cari;
        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM view_presensi_personal ' . $q_cari;

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

    public function getRecordPersonil($data) {
        parent::setConnection('db_presensi');
        $idKey = array($data['bulan'], $data['tahun']);
        $query = 'SELECT * FROM tb_record_apel '
                . 'WHERE (MONTH(tanggal_apel) = ?) AND (YEAR(tanggal_apel) = ?) AND pin_absen in (' . $data['personil'] . ') '
                . 'ORDER BY tanggal_apel DESC, jam_apel DESC';
        $dataArr = $this->getData($query, $idKey);

        $result = [];
        if ($dataArr['count'] == 0)
            return $result;

        foreach ($dataArr['value'] as $i) {
            $tgl = (int) date('d', strtotime($i['tanggal_apel']));
            $pin_absen = $i['pin_absen'];
            if ($i['status_apel']) {
                if ($data['format'] == 'A')
                    $result[$pin_absen][$tgl] = 'A1';
                elseif ($data['format'] == 'B')
                    $result[$pin_absen][$tgl] = substr($i['jam_apel'], 0, 5);
            } elseif ($data['format'] == 'A')
                $result[$pin_absen][$tgl] = 'A0';
        }

        return $result;
    }

    public function getLibur($data) {
        parent::setConnection('db_presensi');
        $kdGrubSatker = isset($data['satker']) ? $data['satker']['kd_kelompok_lokasi_kerja'] : '';
        $idKey = array($data['bulan'], $data['tahun'], $kdGrubSatker, 'red');
        $libur = [];
        $dataLibur = $this->getData('SELECT * FROM tb_libur '
                . 'WHERE (MONTH(tgl_libur) = ?) AND (YEAR(tgl_libur) = ?) AND (FIND_IN_SET(?, kdlokasi) OR kdlokasi = ?)', $idKey);
        if ($dataLibur['count'] > 0)
            $libur = array_map(function ($i) {
                $tgl = (int) date('d', strtotime($i['tgl_libur']));
                return $tgl;
            }, $dataLibur['value']);

        return $libur;
    }

    //---moderasi----//
    public function getUserGroup($mid) {
        parent::setConnection('db_presensi');
        $sql = "SELECT usergroup FROM tb_moderasi WHERE id = ?";
        return $this->getData($sql, [$mid])["value"][0]["usergroup"];
    }

    public function getModerasi($data, $datalap = []) {
        parent::setConnection('db_presensi');
        $data['periode'] = $data['bulan'] . $data['tahun'];
        //$idKey = [$data['kdlokasi'], $data['periode'], $data['periode']];

        $status = "";
        /* if ($data['tingkat'] == 2 && isset($datalap['ver_admin_opd'])) 
          $status = "AND (flag_operator_opd IS NULL OR flag_operator_opd = '1')";
          elseif ($data['tingkat'] == 6 && isset($datalap['sah_kepala_bkppd'])) //final
          $status = "AND flag_kepala_opd != '3'";
          elseif ($data['tingkat'] > 2 && isset($datalap['sah_kepala_opd']))
          $status = "AND (flag_kepala_opd IS NULL OR flag_kepala_opd = '1')";
         */

        $jenis_mod = "";
        /* if (in_array($data['format'], ['A', 'B']) && $data['jenis']) {
          $kd_jenis = 'JNSMOD0' . $data['jenis'];
          $jenis_mod = "AND (kd_jenis = 'JNSMOD04' OR kd_jenis = '".$kd_jenis."')";
          }
         */

        $pin = "";
        if (isset($data['pin_absen']) && $data['pin_absen']) :
            $pin = " AND pin_absen in (" . $data['pin_absen'] . ")";
        endif;

        /*
          $sql = "SELECT * FROM tb_moderasi tmod
          INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi
          WHERE kdlokasi = ? ".$status."
          AND (CONCAT(MONTH(tanggal_awal), YEAR(tanggal_awal)) = ? OR CONCAT(MONTH(tanggal_akhir), YEAR(tanggal_akhir)) = ?) ".$jenis_mod.$pin."
          "; */
        $Ym = $data['tahun'] . '-' . $data['bulan'] . '-';
        $awal = $Ym . '01';
        $akhir = $Ym . $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        //$idKey = [$data['kdlokasi'], $awal, $akhir, $awal, $akhir];

        /* $sql = "SELECT * FROM tb_moderasi tmod
          INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi
          WHERE kdlokasi = ? ".$status."
          AND ((tanggal_awal BETWEEN ? AND ?) OR (tanggal_akhir BETWEEN ? AND ?)) ".$jenis_mod.$pin."
          "; */


        $sql = "SELECT * FROM tb_moderasi tmod
            INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi
            WHERE kdlokasi = '" . $data['kdlokasi'] . "' " . $status . "
            AND ('" . $awal . "' BETWEEN tanggal_awal AND tanggal_akhir OR '" . $akhir . "' BETWEEN tanggal_awal AND tanggal_akhir  OR tanggal_awal BETWEEN '" . $awal . "' AND '" . $akhir . "'  OR tanggal_akhir BETWEEN '" . $awal . "' AND '" . $akhir . "') " . $jenis_mod . $pin . "
        ";

        $get = $this->getData($sql);
        //$get['hitung'] = $this->hitungHari($get['value'], $idKey);
        $get['hitung'] = $this->hitungHari($get['value']);

        /* $j_query = $sql = "SELECT * FROM tb_moderasi tmod WHERE kdlokasi = ? AND ((tanggal_awal BETWEEN ? AND ?) OR (tanggal_akhir BETWEEN ? AND ?)) AND flag_operator_opd IS NULL"; */

        $j_query = $sql = "SELECT * FROM tb_moderasi tmod WHERE kdlokasi = '" . $data['kdlokasi'] . "' AND ('" . $awal . "' BETWEEN tanggal_awal AND tanggal_akhir OR '" . $akhir . "' BETWEEN tanggal_awal AND tanggal_akhir  OR tanggal_awal BETWEEN '" . $awal . "' AND '" . $akhir . "'  OR tanggal_akhir BETWEEN '" . $awal . "' AND '" . $akhir . "') AND flag_operator_opd IS NULL";

        //$get['unverified'] = $this->getData($j_query, $idKey)['count'];
        $get['unverified'] = $this->getData($j_query)['count'];
        ///////////////
        return $get;
    }

    public function hitungHari($data) {
        $hitung = [];
        foreach ($data as $i) {
            if ($i['kd_jenis'] != 'JNSMOD04') :
                continue;
            endif;

            $pin = $i['pin_absen'];
            $kode = $i['kode_presensi'];
            $awal = date_create($i['tanggal_awal']);
            $akhir = date_create($i['tanggal_akhir']);
            $diff = date_diff($awal, $akhir)->d + 1;

            if (!isset($hitung[$kode])) :
                $hitung[$pin][$kode] = $diff;
            else :
                $hitung[$pin][$kode] += $diff;
            endif;
        }
        return $hitung;
    }

    public function getDaftarVerMod($data) {
        parent::setConnection('db_presensi');

        $data['periode'] = $data['bulan'] . $data['tahun'];
        $where = "AND (CONCAT(MONTH(tanggal_awal), YEAR(tanggal_awal)) = ? OR CONCAT(MONTH(tanggal_akhir), YEAR(tanggal_akhir)) = ?)";

        $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, (SELECT COUNT(*) FROM tb_moderasi tm WHERE tm.pin_absen = tmod.pin_absen) AS jml
            FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis WHERE pin_absen in (" . $data['pin_absen'] . ") " . $where . " ORDER BY jml DESC, tanggal_awal";

        $params = [$data['periode'], $data['periode']];

        $daftarModerasi = $this->getData($sql, $params)["value"];

        parent::setConnection('db_pegawai');
        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen, singkatan_lokasi FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_lokasi_kerja tlokasi ON tpeg.kdlokasi = tlokasi.kdlokasi WHERE pin_absen in (" . $data['pin_absen'] . ")";
        $daftarPegawai = $this->getData($sql, [])["value"];

        $daftarModPegawai = [];

        foreach ($daftarModerasi as $mod) {
            foreach ($daftarPegawai as $peg) {
                if ($mod["pin_absen"] === $peg["pin_absen"]) {
                    $daftarModPegawai[] = array_merge($mod, $peg);
                    break;
                }
            }
        }

        return $daftarModPegawai;
    }

    public function getJadwalkerja($data) {
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

        $jadwal['masuk'] = [];
        $jadwal['pulang'] = [];
        foreach ($get['value'] as $i) {
            $tgl = (int) date('d', strtotime($i['tanggal']));
            $pin = $i['pin_absen'];

            if ($i['masuk'] == '00:00:00' && $i['pulang'] == '00:00:00') {
                $jadwal['masuk'][$pin][$tgl] = 'HL';
                $jadwal['pulang'][$pin][$tgl] = 'HL';
                continue;
            } else
                $masuk = \DateTime::createFromFormat('H:i:s', $i['masuk']);

            $jadwal['masuk'][$pin][$tgl] = [
                'awal' => $i['mulai_masuk'],
                'batas' => $masuk->modify('+1 minutes')->format('H:i:s'),
                'akhir' => $i['akhir_masuk']
            ];

            $shiftmalam = false;
            if (strtotime($i['pulang']) < strtotime($i['masuk']))
                $shiftmalam = true;

            $jadwal['pulang'][$pin][$tgl] = [
                'awal' => $i['mulai_pulang'],
                'batas' => $i['pulang'],
                'akhir' => $i['akhir_pulang'],
                'shiftmalam' => $shiftmalam
            ];
        }

        /*
          if (!isset($data['jenis']))
          return $jadwal;
          elseif ($data['jenis'] == 1)
          return $jadwal['masuk'];
          elseif ($data['jenis'] == 3)
          return $jadwal['pulang'];
         */
        return $jadwal;
    }

    public function checkMasuk($data, $format, $jadwal = []) {
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

            if ($libur) //libur tapi finger
                $masuk = ($format == 'A' ? 'M1' : substr($i['jam_log_presensi'], 0, 5));
            elseif ($finger < $awal || $finger > $akhir)
                $masuk = 'M0';
            elseif ($finger <= $batas)
                $masuk = ($format == 'A' ? 'M1' : substr($i['jam_log_presensi'], 0, 5));
            else {
                $telat = $finger - $batas;
                $masuk = substr($i['jam_log_presensi'], 0, 5);

                if ($telat < 960)  //15*60*60
                    $masuk = 'M2';
                elseif ($telat < 1860)
                    $masuk = 'M3';
                elseif ($telat < 3600)
                    $masuk = 'M4';
                else
                    $masuk = 'M5';
            }

            //handle multiple fingerprint
            if (isset($check[$pin][$tgl]) && !in_array($check[$pin][$tgl], ['M2', 'M3', 'M4', 'M5', 'M0']))
                continue;

            if (isset($check[$pin][$tgl]) && in_array($masuk, ['M2', 'M3', 'M4', 'M5', 'M0'])) {
                $isi = $check[$pin][$tgl];
                $angka_isi = substr($isi, 1, 1);
                $angka_masuk = substr($masuk, 1, 1);

                if ($masuk == 'M0' || ($isi != 'M0' && $angka_masuk > $angka_isi))
                    continue;
            }

            $check[$pin][$tgl] = $masuk;
        }

        foreach ($jadwal as $key => $i) {
            foreach ($i as $tgl => $val) {
                if (!is_array($val) && $val == 'HL' && !isset($check[$key][$tgl]))
                    $check[$key][$tgl] = 'HL';
            }
        }

        return $check;
    }

    public function checkPulang($data, $format, $jadwal = []) {
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

            if ($libur) //libur tapi finger
                $pulang = ($format == 'A' ? 'P1' : substr($i['jam_log_presensi'], 0, 5));
            elseif ($finger < $awal || $finger > $akhir) //jk finger tidak sesuai ketentuan
                $pulang = 'P0';
            elseif ($finger >= $batas)
                $pulang = ($format == 'A' ? 'P1' : substr($i['jam_log_presensi'], 0, 5));
            else {
                $dahulu = $batas - $finger;
                $pulang = substr($i['jam_log_presensi'], 0, 5);

                if ($dahulu < 960)  //15*60*60
                    $pulang = 'P2';
                elseif ($dahulu < 1860)
                    $pulang = 'P3';
                elseif ($dahulu < 3600)
                    $pulang = 'P4';
                else
                    $pulang = 'P5';
            }

            //handle multiple fingerprint
            if (isset($check[$pin][$tgl]) && !in_array($check[$pin][$tgl], ['P2', 'P3', 'P4', 'P5', 'P0']))
                continue;

            if (isset($check[$pin][$tgl]) && in_array($pulang, ['P2', 'P3', 'P4', 'P5', 'P0'])) {
                $isi = $check[$pin][$tgl];
                $angka_isi = substr($isi, 1, 1);
                $angka_pulang = substr($pulang, 1, 1);

                if ($pulang == 'P0' || ($isi != 'P0' && $angka_pulang > $angka_isi))
                    continue;
            }

            $check[$pin][$tgl] = $pulang;
        }

        foreach ($jadwal as $key => $i) {
            foreach ($i as $tgl => $val) {
                if (!is_array($val) && $val == 'HL' && !isset($check[$key][$tgl]))
                    $check[$key][$tgl] = 'HL';
            }
        }

        return $check;
    }

    public function getLogSatker($data) {
        parent::setConnection('db_presensi');
        if ($data['jenis'] == 1) {
            $params = [$data['bulan'], $data['tahun']];
            $sql = "SELECT * FROM tb_log_presensi WHERE pin_absen in (" . $data['personil'] . ")
                 AND MONTH(tanggal_log_presensi) = ? AND YEAR(tanggal_log_presensi) = ?
                 AND status_log_presensi = 0 ORDER BY tanggal_log_presensi ASC, jam_log_presensi ASC
            ";

            $func = 'checkMasuk';
        } else {
            //ambil data bulan itu s.d tgl 1 bln brikutnya
            $batas_awal = $data['tahun'] . '-' . $data['bulan'] . '-1';
            $thn_akhir = $data['bulan'] == 12 ? ($data['tahun'] + 1) : $data['tahun'];
            $bln_akhir = $data['bulan'] == 12 ? 1 : ($data['bulan'] + 1);
            $batas_akhir = $thn_akhir . '-' . $bln_akhir . '-1';

            $params = [$batas_awal, $batas_akhir];
            $sql = "SELECT * FROM tb_log_presensi WHERE pin_absen in (" . $data['personil'] . ")
                 AND (tanggal_log_presensi BETWEEN ? AND ?) AND status_log_presensi = 1 ORDER BY 
                 tanggal_log_presensi ASC, jam_log_presensi DESC";

            $func = 'checkPulang';
        }

        $get = $this->getData($sql, $params);

        $jadwal = $this->getJadwalkerja($data);
        $check = $this->$func($get['value'], $data['format'], $jadwal);

        return $check;
    }

    public function getLogPersonil($data, $format) {
        parent::setConnection('db_presensi');
        //$params = [$data['bulan'], $data['tahun']];
        /* $sql = "SELECT * FROM tb_log_presensi WHERE pin_absen in (".$data['pin_absen'].")
          AND MONTH(tanggal_log_presensi) = ? AND YEAR(tanggal_log_presensi) = ?
          "; */

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

        $jadwal = $this->getJadwalkerja($data);
        $check['masuk'] = $this->checkMasuk($get_masuk['value'], $format, $jadwal['masuk']);
        $check['pulang'] = $this->checkPulang($get_pulang['value'], $format, $jadwal['pulang']);

        return $check;
    }

    public function getLaporan($data) {
        parent::setConnection('db_presensi');
        //$format = $data['format'] == 'TPP' ? $data['format'] : $data['format'].$data['jenis'];
        // $params = [$data['kdlokasi'], $format, $data['bulan'], $data['tahun']];
        //$format = $data['format'] == 'C' ? $data['format'].$data['jenis'] : $data['jenis'];
        $params = [$data['kdlokasi'], $data['bulan'], $data['tahun']];

        //$sql = "SELECT * FROM tb_laporan WHERE kdlokasi = ? AND format = ? AND bulan = ? AND tahun = ?".$pin;
        $sql = "SELECT * FROM tb_laporan WHERE kdlokasi = ? AND bulan = ? AND tahun = ? AND pin_absen IS NULL";
        $get = $this->getData($sql, $params);

        if ($get['count'] == 0 && isset($data['pin_absen']) && $data['pin_absen'] != '') {
            $pin = " AND pin_absen in (" . $data['pin_absen'] . ")";
            $sql = "SELECT * FROM tb_laporan WHERE kdlokasi = ? AND bulan = ? AND tahun = ?" . $pin;
            $get = $this->getData($sql, $params);
        }

        if ($get['count'] > 0) {
            $laporan = $get['value'][0];
            $p = [$laporan['ver_admin_opd'], $laporan['sah_kepala_opd'], $laporan['ver_admin_kota'], $laporan['sah_kepala_bkppd'], $laporan['sah_final']];
            $p = implode(',', array_filter($p));

            parent::setConnection('db_pegawai');
            $person_ver = $this->getData("SELECT vp.nipbaru, vp.nama_personil,
                ttd.ttdFilename AS ttd, lk.stempel
                FROM view_presensi_personal vp
                LEFT JOIN ttd ON ttd.nip = vp.nipbaru
                LEFT JOIN tref_lokasi_kerja lk ON lk.kdlokasi = vp.kdlokasi
                WHERE (vp.nipbaru in (" . $p . "))", []);

            parent::setConnection('db_presensi'); //ambil jabatan pengguna
            $laporan['ver'] = [];
            foreach ($person_ver['value'] as $i) {
                if ($laporan['ver_admin_opd'] == $i['nipbaru']) {
                    $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                        WHERE grup_pengguna_kd = 'KDGRUP01' AND nipbaru = '" . $i['nipbaru'] . "' 
                        AND kdlokasi = '" . $data['kdlokasi'] . "' AND jabatan_pengguna IS NOT NULL
                    ", []);

                    $i['jabatan_pengguna'] = '';
                    if ($pengguna['count'] > 0)
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                    $laporan['admin_opd'] = $i;
                }
                if ($laporan['sah_kepala_opd'] == $i['nipbaru']) {
                    $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                        WHERE grup_pengguna_kd = 'KDGRUP02' AND nipbaru = '" . $i['nipbaru'] . "' 
                        AND kdlokasi = '" . $data['kdlokasi'] . "' AND jabatan_pengguna IS NOT NULL
                    ", []);

                    $i['jabatan_pengguna'] = '';
                    if ($pengguna['count'] > 0)
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                    $i['stempel'] = $data['kdlokasi'] . '.png';
                    $laporan['kepala_opd'] = $i;
                }
                if ($laporan['ver_admin_kota'] == $i['nipbaru']) {
                    $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                        WHERE grup_pengguna_kd = 'KDGRUP03' AND nipbaru = '" . $i['nipbaru'] . "' 
                        AND jabatan_pengguna IS NOT NULL
                    ", []);

                    $i['jabatan_pengguna'] = '';
                    if ($pengguna['count'] > 0)
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                    $laporan['admin_kota'] = $i;
                }
                if ($laporan['sah_kepala_bkppd'] == $i['nipbaru']) {
                    $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                        WHERE grup_pengguna_kd = 'KDGRUP04' AND nipbaru = '" . $i['nipbaru'] . "' 
                        AND jabatan_pengguna IS NOT NULL
                    ", []);

                    $i['jabatan_pengguna'] = '';
                    if ($pengguna['count'] > 0)
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                    $laporan['kepala_bkppd'] = $i;
                }

                if ($laporan['sah_final'] == $i['nipbaru']) {
                    $pengguna = $this->getData("SELECT * FROM tb_pengguna 
                        WHERE grup_pengguna_kd = 'KDGRUP02' AND nipbaru = '" . $i['nipbaru'] . "' 
                        AND kdlokasi = '" . $data['kdlokasi'] . "' AND jabatan_pengguna IS NOT NULL
                    ", []);

                    $i['jabatan_pengguna'] = '';
                    if ($pengguna['count'] > 0)
                        $i['jabatan_pengguna'] = $pengguna['value'][0]['jabatan_pengguna'];

                    $laporan['final'] = $i;
                }
            }

            return $laporan;
        } else {
            //simpan
            /*
              $params = [
              'kdlokasi' => $data['kdlokasi'],
              'pin_absen' => $data['format'] == 'C' ? $data['pin_absen'] : null,
              'format' => $format,
              'bulan' => $data['bulan'],
              'tahun' => $data['tahun']
              ];
              $new = $this->save('tb_laporan(kdlokasi,pin_absen,format,bulan,tahun)', $params);
              return $this->getLaporan($data); */
            return [];
        }
    }

    public function getArraymod($input, $laporan = []) {
        $data = $this->getModerasi($input, $laporan);

        $dataArr = [];
        foreach ($data['value'] as $mod) {
            $sah = $mod['flag_operator_kota'];
            if ($input['tingkat'] == 6 && $mod['flag_kepala_opd'] != 2)
                $sah = null;

            for ($a = strtotime($mod['tanggal_awal']); $a <= strtotime($mod['tanggal_akhir']);) {
                $tgl = (int) date('d', $a);
                if ((int) date('m', $a) != $input['bulan']) {
                    $a = $a + 86400;
                    continue;
                }

                $pin_absen = $mod['pin_absen'];
                $dataArr[$pin_absen][$tgl] = [
                    'jenis' => $mod['kd_jenis'],
                    'kode' => $mod['kode_presensi'],
                    'verified' => $sah
                ];

                $a = $a + 86400;
            }
        }
        $dataArr['hitung'] = $data['hitung'];
        $dataArr['unverified'] = $data['unverified'];

        return $dataArr;
    }

    public function getArraymodAll($input, $laporan = []) {
        $data = $this->getModerasi($input, $laporan);

        $dataArr = [];
        foreach ($data['value'] as $mod) {
            if ($input['tingkat'] == 1)
                $verified = 1;
            elseif ($input['tingkat'] == 2) {
                $verified = $mod['flag_operator_opd'];
                // jk lap sudah diverifikasi tpi moderasi masih null, mk dianggap ditolak
                if (count($laporan) > 0 && $laporan['ver_admin_opd'] && $verified == null)
                    $verified = '0';
            } elseif ($input['tingkat'] >= 3) {
                $verified = $mod['flag_kepala_opd'];
                // jk lap sudah diverifikasi tpi moderasi masih null, mk dianggap ditolak
                if (count($laporan) > 0 && $laporan['sah_kepala_opd'] && $verified == null)
                    $verified = '0';
            }


            for ($a = strtotime($mod['tanggal_awal']); $a <= strtotime($mod['tanggal_akhir']);) {
                $tgl = (int) date('d', $a);
                if ((int) date('m', $a) != $input['bulan']) {
                    $a = $a + 86400;
                    continue;
                }

                $pin_absen = $mod['pin_absen'];
                $dataArr[$pin_absen][$tgl][$mod['kd_jenis']] = [
                    'kode' => $mod['kode_presensi'],
                    'verified' => $verified
                ];

                $a = $a + 86400;
            }
        }
        $dataArr['hitung'] = $data['hitung'];

        return $dataArr;
    }

    public function getArraypot() {
        parent::setConnection('db_presensi');
        $data = $this->getData("SELECT * FROM tb_kode_presensi ORDER BY minimal DESC", []);

        $dataArr = [];
        foreach ($data['value'] as $i) {
            $dataArr[$i['kode_presensi']][] = [
                'pot' => $i['pot_kode_presensi'] * 100,
                'minimal' => $i['minimal']
            ];
        }
        return $dataArr;
    }

    public function getDataPersonilTpp($data) {
        parent::setConnection('db_pegawai');

        $idKey = array();
        $dataArr = array();
        $q_cari = 'WHERE 1 ';
        $q_carigaji = '';
        /* acil 20200802 */
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

        $query = 'SELECT pin_absen, pp.*, gaji.total AS totgaji, npwp, gol_jbtn, nominal_tp, tunjangan_jabatan, s.`urutan_sotk`, v.`kd_jabatan_khusus`, v.`kd_tp`, v.`kd_ruang_jab`,
            (CASE 
                WHEN LOCATE("I/", pp.golruang) = 0 THEN 4
                WHEN LOCATE("I/", pp.golruang) = 1 THEN 1
                WHEN LOCATE("I/", pp.golruang) = 2 THEN 2
                WHEN LOCATE("I/", pp.golruang) = 3 THEN 3
                ELSE NULL
            END) AS golruang_1,
            RIGHT(pp.golruang, 1) AS golruang_2

            FROM view_presensi_personal pp 
            LEFT JOIN tref_sotk s ON s.kdsotk = pp.kdsotk
            LEFT JOIN data_gaji gaji ON pp.nipbaru = gaji.nipbaru ' . $q_carigaji . '
            JOIN view_tpp_pegawai v ON v.nipbaru = pp.nipbaru ' . $q_cari;
        /* END acil 20200802 */

        /* $query = 'SELECT pin_absen, pp.*, npwp, gol_jbtn, nominal_tp, tunjangan_jabatan, s.urutan_sotk, v.kd_jabatan_khusus, v.kd_tp, v.kd_ruang_jab,
          (CASE
          WHEN LOCATE("I/", pp.golruang) = 0 THEN 4
          WHEN LOCATE("I/", pp.golruang) = 1 THEN 1
          WHEN LOCATE("I/", pp.golruang) = 2 THEN 2
          WHEN LOCATE("I/", pp.golruang) = 3 THEN 3
          ELSE NULL
          END) AS golruang_1,
          RIGHT(pp.golruang, 1) AS golruang_2

          FROM view_presensi_personal pp
          LEFT JOIN tref_sotk s ON s.kdsotk = pp.kdsotk
          JOIN view_tpp_pegawai v ON v.nipbaru = pp.nipbaru ' . $q_cari; */

        $query .= ' ORDER BY ISNULL(s.urutan_sotk), s.urutan_sotk ASC, IF(pp.kd_jabatan = "" OR pp.kd_jabatan = "-" OR pp.kd_jabatan IS NULL, 1, 0), v.nominal_tp DESC, golruang_1 DESC, golruang_2 DESC, pp.nipbaru ASC';

        $dataArr = $this->getData($query, $idKey);
        return $dataArr;
    }

    public function getTpp($nip) {
        parent::setConnection('db_pegawai');

        $q_cari = "WHERE pp.nipbaru in (" . $nip . ")";
        ;
        $query = 'SELECT pin_absen, pp.nama_personil, pp.nipbaru, npwp, gol_jbtn, pp.golruang, nominal_tp, tunjangan_jabatan 
            FROM view_presensi_personal pp 
            JOIN view_tpp_pegawai v ON v.nipbaru = pp.nipbaru ' . $q_cari . ' ORDER BY ISNULL(v.kdsotk), v.kdsotk ASC, nipbaru ASC';
        $dataArr = $this->getData($query, []);

        $tpp = null;
        foreach ($dataArr['value'] as $i) {
            $key = $i['nipbaru'];
            $tpp[$key] = $i['nominal_tp'];
        }
        return $tpp;
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

    public function getRekapAll($data, $laporan, $hitungpot = false) {
        $moderasi = $this->getArraymodAll($data, $laporan);
        $libur = $this->getLibur($data);
        $data_pot = $this->getArraypot();
        $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        $hitungmod = $moderasi['hitung'];

        $data['pin_absen'] = $data['personil'];
        $log = $this->getLogPersonil($data, ($data['format'] == 'TPP' ? 'A' : $data['format']));
        $masuk = $log['masuk'];
        $pulang = $log['pulang'];

        //apel
        $data['format'] = ($data['format'] == 'TPP' ? 'A' : $data['format']);
        //$apel = $this->getRecordPersonil($data);
        $apel = $this->apelpagi_service->getRecordApel($data);

        $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $tot = 0;
            $key = $peg['pin_absen'];
            $sum_mk = 0;
            $sum_ap = 0;
            $sum_pk = 0;
            $pot_penuh = [];
            $jumlah_tk = 0;
            for ($i = 1; $i <= $hitungtgl; $i++) {
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
                    if ($masuk[$key][$i] == 'HL')
                        $hl = true;
                    else
                        $kd_masuk = $masuk[$key][$i];

                    if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                        $color1 = 'yellow accent-2';
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color1 = 'yellow accent-2';
                    $kd_masuk = 'M0';
                }

                if (isset($apel[$key][$i])) {
                    if ($apel[$key][$i] != 'HL')
                        $kd_apel = $apel[$key][$i];

                    if ($kd_apel == 'A0')
                        $color2 = 'yellow accent-2';
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color2 = 'yellow accent-2';
                    $kd_apel = 'A0';
                }

                if (isset($pulang[$key][$i])) {
                    if ($pulang[$key][$i] != 'HL')
                        $kd_pulang = $pulang[$key][$i];

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
                    if (isset($masuk[$key][$i]) && $masuk[$key][$i] != 'HL') {
                        $tampil_mod = true;
                        $kd_masuk = $masuk[$key][$i];
                        if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                            $color1 = 'yellow accent-2';
                    }
                    if (isset($pulang[$key][$i]) && $pulang[$key][$i] != 'HL') {
                        $tampil_mod = true;
                        $kd_pulang = $pulang[$key][$i];
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
                        'kode' => $kd_masuk,
                        'pot' => ($pot_masuk > 0 ? $pot_masuk : ''),
                        'color' => $color1
                    ],
                    'ap' => [
                        'kode' => $kd_apel,
                        'pot' => ($pot_apel > 0 ? $pot_apel : ''),
                        'color' => $color2
                    ],
                    'pk' => [
                        'kode' => $kd_pulang,
                        'pot' => ($pot_pulang > 0 ? $pot_pulang : ''),
                        'color' => $color3
                    ],
                    'all' => ($subtot > 0 ? $subtot : '')
                ];

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
    
    public function getRekapAll_v3($data, $laporan, $hitungpot = false, $custom = false) {
        $moderasi = $this->getArraymodAll($data, $laporan);
        $libur = $this->getLibur($data);
        $data_pot = $this->getArraypot();
        if (is_array($custom)) {
            $tglawal = $custom['awal'];
            $hitungtgl = $custom['akhir'];
        } else {
            $tglawal = 1;
            $hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        }

        $hitungmod = $moderasi['hitung'];
        $data['pin_absen'] = $data['personil'];
        $log = $this->getLogPersonil($data, ($data['format'] == 'TPP' ? 'A' : $data['format']));
        $masuk = $log['masuk'];
        $pulang = $log['pulang'];

        //apel
        $data['format'] = ($data['format'] == 'TPP' ? 'A' : $data['format']);
        $apel = $this->apelpagi_service->getRecordApel($data);

        $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $tot = 0;
            $tot2 = 0;
            $key = $peg['pin_absen'];
            $sum_mk = 0;
            $sum_ap = 0;
            $sum_pk = 0;
            $pot_penuh = [];
            $jumlah_tk = 0;
            for ($i = $tglawal; $i <= $hitungtgl; $i++) {
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
                    if ($masuk[$key][$i] == 'HL') :
                        $hl = true;
                    else :
                        $kd_masuk = $masuk[$key][$i];
                    endif;

                    if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0'])) :
                        $color1 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color1 = 'yellow accent-2';
                    $kd_masuk = 'M0';
                }

                if (isset($apel[$key][$i])) {
                    if ($apel[$key][$i] != 'HL') :
                        $kd_apel = $apel[$key][$i];
                    endif;

                    if ($kd_apel == 'A0') :
                        $color2 = 'yellow accent-2';
                    endif;
                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color2 = 'yellow accent-2';
                    $kd_apel = 'A0';
                }

                if (isset($pulang[$key][$i])) {
                    if ($pulang[$key][$i] != 'HL') {
                        $kd_pulang = $pulang[$key][$i];
                    }

                    if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0'])) {
                        $color3 = 'yellow accent-2';
                    }
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
                    if (isset($masuk[$key][$i]) && $masuk[$key][$i] != 'HL') {
                        $tampil_mod = true;
                        $kd_masuk = $masuk[$key][$i];
                        if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0'])) {
                            $color1 = 'yellow accent-2';
                        }
                    }
                    if (isset($pulang[$key][$i]) && $pulang[$key][$i] != 'HL') {
                        $tampil_mod = true;
                        $kd_pulang = $pulang[$key][$i];
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
                        'kode' => $kd_masuk,
                        'pot' => ($pot_masuk > 0 ? $pot_masuk : ''),
                        'color' => $color1
                    ],
                    'ap' => [
                        'kode' => $kd_apel,
                        'pot' => ($pot_apel > 0 ? $pot_apel : ''),
                        'color' => $color2
                    ],
                    'pk' => [
                        'kode' => $kd_pulang,
                        'pot' => ($pot_pulang > 0 ? $pot_pulang : ''),
                        'color' => $color3
                    ],
                    'all' => ($subtot > 0 ? $subtot : '')
                ];

                $sum_mk += $pot_masuk;
                $sum_ap += $pot_apel;
                $sum_pk += $pot_pulang;

                if ($hitungpot && $kd_masuk == 'TK') {
                    $jumlah_tk++;
                }

                if ($jumlah_tk >= 10) {
                    $pot_penuh[] = 'TK';
                }
            }

            $all[$key]['pot_penuh'] = array_unique($pot_penuh);

            if (count($pot_penuh) == 0) {
                $tot = ($sum_mk + $sum_ap + $sum_pk);
            } else {
                $implode = implode(",", $all[$key]['pot_penuh']);
                $tot = "100% (" . $implode . ")";
                $tot2 = 100;
            }

            $all[$key]['sum_pot'] = [
                'mk' => $sum_mk, 'ap' => $sum_ap, 'pk' => $sum_pk,
                'all' => $tot, 'tot2' => $tot2, 'tk' => $jumlah_tk
            ];
        }

        $all['allverified'] = $allverified;
        return $all;
    }

    public function getKepala($kdlokasi, $parent = true) {
        //checkparent
        parent::setConnection('db_pegawai');
        $getLoKer = $this->getData('SELECT * FROM tref_lokasi_kerja WHERE kdlokasi = ?', [$kdlokasi]);
        //khusus setda, yg ttd kabag umum
        //kecuali bag.hukum - G08002 --- gk jadi minta dibalikin lg ke kabag umum
        //if ($kdlokasi != 'G08002' && $get['count'] > 0 && $get['value'][0]['kdlokasi_parent']) {
        if ($parent && $getLoKer['count'] > 0 && $getLoKer['value'][0]['kdlokasi_parent']) {
            $kdlokasi = $getLoKer['value'][0]['kdlokasi_parent'];
        }

        parent::setConnection('db_presensi');
        $getPengguna = $this->getData('SELECT * FROM tb_pengguna WHERE kdlokasi = ? AND grup_pengguna_kd = ?', [$kdlokasi, 'KDGRUP02']); //KEPALA

        $respon = '';
        if ($getPengguna['count'] > 0) {
            parent::setConnection('db_pegawai');
            $namanya = $this->getData('SELECT * FROM tref_lokasi_kerja WHERE kdlokasi = ?', [$kdlokasi]);

            foreach ($getPengguna['value'] as $i) {
                $peg = $this->getData("SELECT nipbaru, nama_personil FROM view_presensi_personal WHERE nipbaru = '" . $i['nipbaru'] . "'", []);

                if ($i['nipbaru'] != '' && $peg['count'] > 0) {
                    $jabatan = $i['jabatan_pengguna'] ? $i['jabatan_pengguna'] : '';
                    $respon = $peg['value'][0];
                    $respon['namanya'] = $jabatan . (empty($jabatan) ? '' : ' ') . $namanya['value'][0]['kepalaskpd'];
                    $respon['jabatan_pengguna'] = $jabatan;
                    break;
                }
            }
        }
        return $respon;
    }

    public function getBendahara($kdlokasi) {
        parent::setConnection('db_presensi');
        $get = $this->getData("SELECT * FROM tb_bendahara WHERE kdlokasi = '" . $kdlokasi . "' ORDER BY id DESC", []);

        $respon = '';
        if ($get['count'] > 0) {
            $id = $get['value'][0]['id'];
            parent::setConnection('db_pegawai');
            $peg = $this->getData("SELECT nipbaru, nama_personil FROM view_presensi_personal WHERE nipbaru = '" . $get['value'][0]['nipbaru'] . "'", []);

            if ($peg['count'] > 0) {
                $peg['value'][0]['id_bendahara'] = $id;
                $respon = $peg['value'][0];
            }
        }

        return $respon;
    }

    public function getAllLaporan($data) {
        parent::setConnection('db_presensi');
        $get = $this->getData("SELECT * FROM tb_laporan WHERE bulan = ? AND tahun = ?", [$data['bulan'], $data['tahun']]);
        return $get;
    }
    
    public function getDataSatker($id) {
        parent::setConnection('db_pegawai');
        $query = 'SELECT * FROM tref_lokasi_kerja WHERE (kdlokasi = ?)';
        $result = $this->getData($query, array($id));

        parent::setConnection('db_presensi');
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('tref_lokasi_kerja');
        }
    }

    public function getDataSetting($id) {
        parent::setConnection('db_presensi');
        $data = $this->getData('SELECT * FROM tb_setting WHERE `variable` = ?', [$id]);
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_setting');
        }
    }

    public function getDataVersi($id, $param) {
        parent::setConnection('db_presensi');
        $data = $this->getData('SELECT * FROM tb_variabel_versi '
                . 'WHERE 1 '
                . ' AND kode_variabel = ? '
                . ' AND ? BETWEEN YEAR(tgl_mulai) AND YEAR(tgl_akhir) '
                . ' AND ? BETWEEN MONTH(tgl_mulai) AND MONTH(tgl_akhir)', [$id, $param['tahun'], $param['bulan']]);
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_variabel_versi');
        }
    }

}
