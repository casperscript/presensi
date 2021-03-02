<?php

namespace app\pengawas\model;

use system;
use comp\FUNC;

class HusnanWModerasiModel extends system\Model {
    protected $dbPresensi = null;
    protected $dbPegawai = null;

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    public function getDaftarPengguna()
    {
        parent::setConnection("db_presensi");
        $sql = "SELECT username, nipbaru, kdlokasi, grup_pengguna_kd FROM tb_pengguna WHERE status_pengguna = 'enable'";
        return $this->getData($sql, [])["value"];
    }

    public function setPass($username, $pass)
    {
        parent::setConnection("db_presensi");
        $where = "username = '".$username."'";
        $params = ["password" => FUNC::encryptor($pass)];
        return $this->husnanWUpdate("tb_pengguna", $params, $where);
    }

    public function getFlags($mid)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT flag_operator_opd, flag_kepala_opd, flag_operator_kota, flag_kepala_kota FROM tb_moderasi WHERE id = ?";
        return $this->getData($sql, [$mid])["value"][0];
    }

    public function getDateLimit($kodeGrup)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT batas_moderasi FROM tb_grup_pengguna WHERE kd_grup_pengguna = ?";
        return $this->getData($sql, [$kodeGrup])["value"][0]["batas_moderasi"];
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

    public function getInfoModerasi($mid, $isFinal = false)
    {
        return $this->getDaftarVerMod($mid, $isFinal)[0];
    }

    public function updateModerasi($data, $isMassive = false)
    {        
        if (!$isMassive) {
            $where = "id = ".$data["mid"];
        } else {
            $where = "id IN(".implode(",", $data["mids"]).")";
        }
        
        $params = [
            "flag_operator_kota" => $data["flag"],
            "dt_flag_operator_kota" => date("Y-m-d H:i:s"),
            "dt_last_modified" => date("Y-m-d H:i:s"),
            "catatan_operator_kota" => $data["catatan"]
        ];
        return $this->husnanWUpdate("tb_moderasi", $params, $where);
    }

    public function getUserGroup($mid)
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT usergroup FROM tb_moderasi WHERE id = ?";
        return $this->getData($sql, [$mid])["value"][0]["usergroup"];
    }

    public function getDaftarOpd() 
    {
        parent::setConnection('db_pegawai');

        $sql = "SELECT tlokasi.kdlokasi, tlokasi.singkatan_lokasi FROM tref_lokasi_kerja tlokasi ORDER BY singkatan_lokasi";
        $daftarOpd = $this->getData($sql, [])["value"];

        return $daftarOpd;
    }

    public function getDaftarPegawaiModerasi($kodeLokasi) 
    {
        parent::setConnection('db_pegawai');

        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_golongan_ruang tgolruang ON tpeg.golruang = tgolruang.golruang WHERE tpeg.kdlokasi = ? ORDER BY kdgol";
        $daftarPegawai = $this->getData($sql, [$kodeLokasi])["value"];

        return $daftarPegawai;
    }

    public function getDaftarVerMod($mid = null, $isFinal = false)
    {
        parent::setConnection('db_presensi');

        $where = $isFinal ? " AND (flag_kepala_opd = '2' OR flag_kepala_opd = '3')" : " AND (flag_kepala_opd <> '2' AND flag_kepala_opd <> '3')";

        if ($mid === null || !is_numeric($mid)) {
            $params = [];

            if (!is_numeric($mid) && $mid !== null) {
                $where .= " AND kdlokasi = ?";
                $params = [$mid];
            }

            $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, (SELECT COUNT(*) FROM tb_moderasi tm WHERE tm.pin_absen = tmod.pin_absen) AS jml
            FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis WHERE flag_operator_opd IS NOT NULL AND flag_kepala_opd IS NOT NULL ".$where." ORDER BY jml DESC, pin_absen, tanggal_awal";
            
        } else {
            $sql = "SELECT tmod.*, tjm.nama_jenis, tkp.ket_kode_presensi, tkp.pot_kode_presensi, tgrup.nama_grup_pengguna AS grup_pemohon FROM tb_moderasi tmod INNER JOIN tb_kode_presensi tkp ON tmod.kode_presensi = tkp.kode_presensi INNER JOIN tb_jenis_moderasi tjm ON tmod.kd_jenis = tjm.kd_jenis INNER JOIN tb_grup_pengguna tgrup ON tmod.usergroup = tgrup.kd_grup_pengguna WHERE tmod.id = ? AND flag_operator_opd IS NOT NULL AND flag_kepala_opd IS NOT NULL ".$where;
            $params = [$mid];
        }

        $daftarModerasi = $this->getData($sql, $params)["value"];       
        $strPin = FUNC::husnanWStrImplode($daftarModerasi, "pin_absen");

        parent::setConnection('db_pegawai');        
        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen, singkatan_lokasi FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_lokasi_kerja tlokasi ON tpeg.kdlokasi = tlokasi.kdlokasi WHERE pin_absen IN (".$strPin.")";
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
        /*
        echo '<pre>';
        var_dump($daftarModPegawai);
        echo '</pre>';
        */

        return $daftarModPegawai;
    }

    public function simpanDokumenModerasi($data)
    {
        parent::setConnection('db_presensi');
        return $this->husnanWInsert("tb_dokumen_moderasi", $data);
    }

    public function simpanPemohonModerasi($data)
    {
        parent::setConnection('db_presensi');
        return $this->husnanWInsert("tb_moderasi", $data);
    }
    
    public function getJenisModerasi()
    {
        parent::setConnection('db_presensi');
        $sql = "SELECT kd_jenis, nama_jenis FROM tb_jenis_moderasi";
        return $this->getData($sql, [])["value"];
    }

    public function getCurrentPns($nip) 
    {
        parent::setConnection('db_pegawai');

        $sql = "SELECT tpeg.nipbaru, CONCAT(gelar_depan, ' ', namapeg, ' ', gelar_blkg) AS nama_lengkap, pin_absen, singkatan_lokasi, path_foto_pegawai AS foto FROM texisting_personal tperson INNER JOIN texisting_kepegawaian tpeg ON tperson.nipbaru = tpeg.nipbaru INNER JOIN tref_lokasi_kerja tlokasi ON tpeg.kdlokasi = tlokasi.kdlokasi WHERE tpeg.nipbaru = ?";
        $currentPns = $this->getData($sql, [$nip])["value"][0];
        return $currentPns;
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
        $sql = "UPDATE ".$tableName." SET ".$signs." WHERE ".$where;
//echo $sql; exit();
        try {
            $result = $this->db->prepare($sql);
            return $result->execute($vals);
        } catch (Exception $e) {            
            echo "ERROR ON husnanWUpdate: ".$e->getMessages();
        }
    }
}
