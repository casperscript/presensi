<?php

namespace app\kepalaopd\model;

use system;
use comp\FUNC;

class HusnanWModerasiModel extends system\Model {
    protected $dbPresensi = null;
    protected $dbPegawai = null;

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    public function getFlags($mid)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT flag_operator_opd, flag_kepala_opd, flag_operator_kota, flag_kepala_kota FROM tb_moderasi WHERE id = ?";
        return $this->getData($sql, [$mid])["value"][0];
    }

    public function getDateModified($mid)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT dt_flag_operator_opd, dt_flag_kepala_opd, dt_flag_operator_kota, dt_flag_kepala_kota FROM tb_moderasi WHERE id = ?";
        return $this->getData($sql, [$mid])["value"][0];
    }

    public function getDokumenModerasi($mid)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT filename FROM tb_dokumen_moderasi WHERE moderasi_id = ?";
        return $this->getData($sql, [$mid])["value"];
    }

    public function getInfoModerasi($kodeLokasi, $mid, $isFinal = false)
    {
        return $this->getDaftarVerMod($kodeLokasi, $mid, $isFinal)[0];
    }

    public function getCurrentPns($nip) 
    {
        parent::setConnection('db_pegawai');

        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen, singkatan_lokasi, path_foto_pegawai AS foto FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_lokasi_kerja tlokasi ON tpeg.kdlokasi = tlokasi.kdlokasi WHERE tpeg.nipbaru = ?";
        $currentPns = $this->getData($sql, [$nip])["value"][0];
        return $currentPns;
    }

    public function updateModerasi($data, $kodeLokasi, $isMassive = false)
    {
        if (!$isMassive) {
            $where = "id = ".$data["mid"]." AND kdlokasi = '".$kodeLokasi."'";
        } else {
            $where = "id IN(".implode(",", $data["mids"]).") AND kdlokasi = '".$kodeLokasi."'";
        }       

        $params = [];

        if ($data["flag"] === "2") {
            $statusFinal = "DISAHKAN";
            $params["catatan_final_kepala_opd"] = $data["catatan"];
        } elseif ($data["flag"] === "3") {
            $statusFinal = "DIBATALKAN";
            $params["catatan_final_kepala_opd"] = $data["catatan"];
        } else {
            $statusFinal = "DIPROSES";
            $params["catatan_kepala_opd"] = $data["catatan"];
        }

        $params = array_merge([
            "flag_kepala_opd" => $data["flag"],
            "dt_flag_kepala_opd" => date("Y-m-d H:i:s"),
            "dt_last_modified" => date("Y-m-d H:i:s"),
            "status_final" => $statusFinal
        ], $params);
        //FUNC::husnanWVarDump($where);
        return $this->husnanWUpdate("tb_moderasi", $params, $where);
    }

    public function getDaftarVerModOri($kodeLokasi, $mid = null, $isFinal = false)
    {
        parent::setConnection('db_presensi');

        $where = $isFinal ? " AND (flag_kepala_opd = '2' OR flag_kepala_opd = '3')" : " AND (flag_kepala_opd <> '2' AND flag_kepala_opd <> '3' OR flag_kepala_opd IS NULL)";

        if ($mid === null) {
            $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, (SELECT COUNT(*) FROM tb_moderasi tm WHERE tm.pin_absen = tmod.pin_absen) AS jml
            FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis WHERE kdlokasi = ? AND flag_operator_opd IS NOT NULL ".$where." ORDER BY jml DESC, pin_absen, tanggal_awal";
            $params = [$kodeLokasi];
        } else {
            $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, tgrup.nama_grup_pengguna AS grup_pemohon FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis INNER JOIN tb_grup_pengguna tgrup ON tmod.usergroup = tgrup.kd_grup_pengguna WHERE kdlokasi = ? AND tmod.id = ? AND flag_operator_opd IS NOT NULL ".$where;
            $params = [$kodeLokasi, $mid];
        }

        $daftarModerasi = $this->getData($sql, $params)["value"];        
        $strPin = FUNC::husnanWStrImplode($daftarModerasi, "pin_absen");

        parent::setConnection('db_pegawai');        
        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen, singkatan_lokasi FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_lokasi_kerja tlokasi ON tpeg.kdlokasi = tlokasi.kdlokasi WHERE pin_absen IN (".$strPin.") AND tpeg.kdlokasi = ?";
        $daftarPegawai = $this->getData($sql, [$kodeLokasi])["value"];

        $daftarModPegawai = [];

        foreach ($daftarModerasi as $mod) {
            foreach ($daftarPegawai as $peg) {
                if ($mod["pin_absen"] === $peg["pin_absen"]) {
                    $daftarModPegawai[] = array_merge($mod, $peg);
                    break;
                }
            }
        }
        /*
        echo '<pre>';
        var_dump($daftarModPegawai);
        echo '</pre>';
        */

        return $daftarModPegawai;
    }

    public function getDaftarVerMod($input, $mid = null, $isFinal = false)
    {
        parent::setConnection('db_presensi');

        ///$where = $isFinal ? " AND (flag_kepala_opd = '2' OR flag_kepala_opd = '3')" : "";
        $where = '';

        if ($mid === null) {
            $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, (SELECT COUNT(*) FROM tb_moderasi tm WHERE tm.pin_absen = tmod.pin_absen) AS jml
            FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis WHERE kdlokasi = ? AND flag_operator_opd IS NOT NULL ".$where;
            $params = [$input['kdlokasi']];
        } else {
            $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, tgrup.nama_grup_pengguna AS grup_pemohon 
            FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis INNER JOIN tb_grup_pengguna tgrup ON tmod.usergroup = tgrup.kd_grup_pengguna WHERE kdlokasi = ? AND tmod.id = ? AND flag_operator_opd IS NOT NULL ".$where;
            $params = [$input['kdlokasi'], $mid];
        }

        if (isset($input['bulan'])) {
            $sql .= ' AND MONTH(tanggal_awal) = ? ';
            array_push($params, $input['bulan'] + 1);
        }

        if (isset($input['tahun'])) {
            $sql .= '  AND YEAR(tanggal_awal) = ? ';
            array_push($params, $input['tahun']);
        }

        if (isset($input['status'])) {
            switch ($input['status']) {
                case 'semua':
                    $c_status = '';
                    break;
                case 'tolak':
                    $c_status = 'AND (flag_kepala_opd = "0" || flag_kepala_opd = "3")';
                    break;
                case 'terima':
                    $c_status = 'AND (flag_kepala_opd = "1" || flag_kepala_opd = "2")';
                    break;
                case 'null':
                    $c_status = 'AND flag_kepala_opd IS NULL';
                    break;
            }
            $sql .= $c_status . ' ';
        }

        if ($mid === null)
            $sql .= ' ORDER BY jml DESC, pin_absen, tanggal_awal';

        $daftarModerasi = $this->getData($sql, $params)["value"];
        // $daftarModerasi = $this->getData($sql, $params);
        // return $daftarModerasi;    
        $strPin = FUNC::husnanWStrImplode($daftarModerasi, "pin_absen");

        parent::setConnection('db_pegawai');        
        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen, singkatan_lokasi FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_lokasi_kerja tlokasi ON tpeg.kdlokasi = tlokasi.kdlokasi WHERE pin_absen IN (".$strPin.") AND (tpeg.kdlokasi = ? OR tpeg.kdsublokasi = ?)";

        if (isset($input['cari']) && $input['cari'] != '') {
            $sql .= ' AND (tperson.nipbaru LIKE "%'.$input['cari'].'%" OR tperson.namapeg LIKE "%'.$input['cari'].'%")';
        }
        $daftarPegawai = $this->getData($sql, [$input['kdlokasi'], $input['kdlokasi']])["value"];
        // $daftarPegawai = $this->getData($sql, [$input['kdlokasi'], $input['kdlokasi']]);
        // return $daftarPegawai;

        $daftarModPegawai = [];

        foreach ($daftarModerasi as $mod) {
            foreach ($daftarPegawai as $peg) {
                if ($mod["pin_absen"] === $peg["pin_absen"]) {
                    $daftarModPegawai[] = array_merge($mod, $peg);
                    break;
                }
            }
        }
        /*
        echo '<pre>';
        var_dump($daftarModPegawai);
        echo '</pre>';
        */

        return $daftarModPegawai;
    }

    public function getLastId($tableName)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT id FROM ".$tableName." ORDER BY id DESC LIMIT 1";
        return $this->getData($sql, [])["value"][0]["id"];
    }

    public function husnanWDelete($tableName, Array $params)
    {
        if (is_null($this->db)) {
            parent::setConnection('db_presensi');
            $this->openConnection();
        }

        $keys = array_keys($params);
        $vals = array_values($params);
        $where = '';

        foreach ($keys as $key) {
            $where .= $key.' = ? AND ';
        }

        $where = substr($where, 0, strlen($where) - 5);

        $sql = "DELETE FROM ".$tableName." WHERE ".$where;
        
        try {
            $result = $this->db->prepare($sql);
            return $result->execute($vals);
        } catch (Exception $e) {            
            echo "ERROR ON husnanWDelete: ".$e->getMessages();
        }
    }
    
    public function husnanWInsert($tableName, Array $params)
    {
        if (is_null($this->db)) {
            parent::setConnection('db_presensi');
            $this->openConnection();
        }
        
        $keys = implode(',', array_keys($params));
        $vals = array_values($params);
        $signs = FUNC::husnanWStrRepeater('?', count($params));
        $sql = "INSERT INTO ".$tableName."(".$keys.") VALUES(".$signs.")";

        try {
            $result = $this->db->prepare($sql);
            return $result->execute($vals);
        } catch (Exception $e) {            
            echo "ERROR ON husnanWInsert: ".$e->getMessages();
        }
    }

    public function husnanWUpdate($tableName, Array $params, $where)
    {
        if (is_null($this->db)) {
            parent::setConnection('db_presensi');
            $this->openConnection();
        }
        
        $keys = array_keys($params);
        $vals = array_values($params);
        $signs = "";

        foreach ($keys as $k) {
            $signs .= $k." = ?,";
        }

        $signs = substr($signs, 0, strlen($signs) - 1);
        $sql = "UPDATE ".$tableName." SET ".$signs." WHERE ".$where. "  AND flag_operator_opd IS NOT NULL";
//echo $sql; exit();
        try {
            $result = $this->db->prepare($sql);
            return $result->execute($vals);
        } catch (Exception $e) {            
            echo "ERROR ON husnanWUpdate: ".$e->getMessages();
        }
    }
}
