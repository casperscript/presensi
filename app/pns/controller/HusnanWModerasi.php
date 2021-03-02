<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use app\pns\model\HusnanWModerasiModel;
use system;
use comp\FUNC;

class HusnanWModerasi extends system\Controller {
    protected $nip = null;
    protected $pinAbsen = null;
    protected $kodeLokasi = null;
    protected $kodeGrup = null;
    protected $dateLimit = null;

    public function __construct() {
        parent::__construct();
        $this->servicemaster = new HusnanWModerasiModel();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $this->kodeLokasi = $this->login["kdlokasi"];
            $this->nip = $this->login["nipbaru"];
            $this->pinAbsen = $this->servicemaster->getPinAbsen($this->login["nipbaru"]);
            $this->kodeGrup = $this->login["grup_pengguna_kd"];
            $this->dateLimit = $this->servicemaster->getDateLimit($this->kodeGrup);
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('admin/login'));
        }
    }

    public function checkDatesMod()
    {
        //echo json_encode(["status" => "success", "message" => ""]);
        //return;
        $posts = $this->post(true);
        $dateAwal = FUNC::toHusnanWStdDate(trim($posts["dateAwal"]));
        $dateAkhir = FUNC::toHusnanWStdDate(trim($posts["dateAkhir"]));

        $result = $this->servicemaster->checkDatesMod($dateAwal, $dateAkhir, $posts["pinAbsen"]);

        if ($result["status"] === "success") {
            echo json_encode(["status" => "success", "message" => ""]);
        } else {
            echo json_encode(["status" => "fail", "message" => "PERHATIAN:\n\rSistem mendeteksi pengajuan tanggal moderasi yang rangkap disebabkan karena:\n\r1.Tanggal awal dan atau akhir yang Anda masukkan berada diantara dua tanggal yang telah dimoderasi atau,\n\r2. Tanggal awal dan atau akhir telah mencakup sebagian atau seluruh tanggal yang telah dimoderasi(".FUNC::toHusnanWSniDate($result["tglAwal"])." - ".FUNC::toHusnanWSniDate($result["tglAkhir"]).").\n\rAnda tidak diperbolehkan melakukan moderasi diantara atau mencakup tanggal yang telah Anda moderasi sebelumnya. Mohon dikoreksi kembali."]);
        }
    }

    protected function index() {
        //var_dump($this->getDaftarPegawaiModerasi('G09011')); exit();
        $data['title'] = 'Pengajuan Moderasi';
        $data['table_title'] = 'Tabel Master Jenis Dinas';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Moderasi</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pengajuan Moderasi</a>';
          
        $data["kategoriModerasi"] = $this->servicemaster->getKategoriModerasi();
        $data["jenisModerasi"] = $this->servicemaster->getJenisModerasi("JNSMOD01");
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('index', $data, 'theme_admin');
    }

    public function delModerasi($mid)
    {
        $posts = $this->post(true);
        $flags = $this->servicemaster->getFlags($posts["mid"]);

        if ($this->servicemaster->getUserGroup($posts["mid"]) !== $this->kodeGrup) {
            echo json_encode(["status" => "fail", "message" => "Anda tidak diberikan akses hapus untuk dokumen tersebut!"]);
            return;
        } elseif ($flags["flag_kepala_opd"] === "2" || $flags["flag_kepala_opd"] === "3") {
            echo json_encode(["status" => "fail", "message" => "Proses moderasi telah dikunci karena telah disahkan/dibatalkan oleh Kepala OPD!"]);
            return;
        }

        if ($this->servicemaster->delModerasi($posts, $this->kodeLokasi) > 0) {
            echo json_encode(["status" => "success"]);
            return;
        }

        echo json_encode(["status" => "fail", "message" => ""]);
    }

    public function uploadDokumenModerasi()
    {
        //$uploaddir = __DIR__."/upload/moderasi/dokumen";        
        $allowedFileTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/vnd.oasis.opendocument.text", "application/vnd.openxmlformats-officedocument.wordprocessingml", "application/msword", "application/pdf"];
        $filenames = [];
        
        foreach ($_FILES["fileDokumenPendukung"]["name"] as $index => $filename) {
            $filename = FUNC::husnanWGenRand().'-'.$filename;
            $uploadfile = $this->husnanw_moderasi_upload_path.'/'.basename($filename);

            if (in_array($_FILES["fileDokumenPendukung"]["type"][$index], $allowedFileTypes)) {     
                if (move_uploaded_file($_FILES["fileDokumenPendukung"]["tmp_name"][$index], $uploadfile)){
                    $filenames[] = $filename;
                } 
            }
        }

        if (count($filenames) > 0) {
            if (!empty($_POST["hidLids"])) {
                $lids = explode(',', $_POST["hidLids"]);

                foreach ($filenames as $filename) {
                    foreach ($lids as $lid) {
                        $dokumen["moderasi_id"] = $lid;
                        $dokumen["filename"] = $filename;
                        $this->servicemaster->simpanDokumenModerasi($dokumen);
                    }
                }
            } else {
                $lastId = $this->servicemaster->getLastId("tb_moderasi");
                foreach ($filenames as $filename) {
                    $dokumen["moderasi_id"] = $lastId;
                    $dokumen["filename"] = $filename;
                    $this->servicemaster->simpanDokumenModerasi($dokumen);
                }
            }
            echo "success";
        } else {
            echo "fail";
        }
    }

    public function infoModerasi($mid)
    {
        $data["info"] = $this->servicemaster->getInfoModerasi($this->pinAbsen, $mid);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function infoModerasiHasil($mid)
    {
        $data["info"] = $this->servicemaster->getInfoModerasi($this->pinAbsen, $mid, true);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    protected function daftarMod() {
        $data['title'] = 'Daftar Proses Pengajuan Moderasi Anda';
        $data["daftarMod"] = $this->servicemaster->getDaftarMod($this->pinAbsen, null, false, null, null);
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('daftar-mod', $data, 'theme_admin');
    }

    protected function daftarModHasil() {
        $data['title'] = 'Daftar Hasil Pengajuan Moderasi Anda';
        $data["daftarMod"] = $this->servicemaster->getDaftarMod($this->pinAbsen, null, true, null, null);
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('daftar-mod-hasil', $data, 'theme_admin');
    }

    protected function loadTabelDaftarMod()
    {
        $posts = $this->post(true);
        $posts["dateAwal"] = FUNC::toHusnanWStdDate(trim($posts["dateAwal"]));
        $posts["dateAkhir"] = FUNC::toHusnanWStdDate(trim($posts["dateAkhir"]));
        $data["daftarMod"] = $this->servicemaster->getDaftarMod($this->pinAbsen, null, false, $posts["dateAwal"], $posts["dateAkhir"]);
        //var_dump($data); exit();
        $this->subView('tabel-daftar-mod', $data);
    }

    protected function loadTabelDaftarModHasil()
    {
        $posts = $this->post(true);
        $posts["dateAwal"] = FUNC::toHusnanWStdDate(trim($posts["dateAwal"]));
        $posts["dateAkhir"] = FUNC::toHusnanWStdDate(trim($posts["dateAkhir"]));
        $data["daftarMod"] = $this->servicemaster->getDaftarMod($this->pinAbsen, null, true, $posts["dateAwal"], $posts["dateAkhir"]);
        //var_dump($data); exit();
        $this->subView('tabel-daftar-mod-hasil', $data);
    }

    public function updateModerasi()
    {
        $posts = $this->post(true);

        if ($this->servicemaster->updateModerasi($posts, $this->kodeLokasi) > 0) {
            echo json_encode(["status" => "success", "date" => $this->servicemaster->getDateModified($posts["mid"])]);
            return;
        }

        echo json_encode(["status" => "fail"]);
    }

    public function getCurrentPns() {
        $data = $this->servicemaster->getCurrentPns($this->nip);
        echo json_encode($data);
    }

    public function getJenisModerasi($kodeKatMod) {
        $kodeKatMod = trim($kodeKatMod);

        if ($kodeKatMod === "null" || $kodeKatMod === "") {
            echo json_encode('');
            return false;
        }

        $arrKode = explode('|', $kodeKatMod);

        if (count($arrKode) > 1) {
            foreach ($arrKode as $v) {
                if ($v === "JNSMOD04") {
                    echo json_encode("WRONG CATEGORIES!");
                    return false;
                }
            }

            $kodeKatMod = "JNSMOD01";
        }

        $data = $this->servicemaster->getJenisModerasi($kodeKatMod);
        echo json_encode($data);
    }

    public function simpanPemohonModerasi()
    {
        $posts = $this->post(true);
        $posts["pin_absen"] = trim($posts["pin_absen"]);
        $katMods = $posts["katMods"];

        if (count($katMods) === 1) {
            $kodeJenis = explode('|', trim($posts["kd_jenis"]));
            $posts["kd_jenis"] = $kodeJenis[0];
            $posts["kode_presensi"] = $kodeJenis[1];
        } elseif(count($katMods) > 1) {
            if (in_array("JNSMOD04", $katMods)) {
                echo json_encode([
                    "status" => "fail",
                    "message" => "Terjadi kesalahan kumpulan kategori moderasi yang dipilih."
                ]);
    
                return;
            }
            
            $posts["kode_presensi"] = $posts["kd_jenis"];            
        } else {
            echo json_encode([
                "status" => "fail",
                "message" => "Terjadi kesalahan input kategori moderasi."
            ]);

            return;
        }

        unset($posts["katMods"]);

        $posts["tanggal_awal"] = FUNC::toHusnanWStdDate(trim($posts["tanggal_awal"]));
        $posts["tanggal_akhir"] = FUNC::toHusnanWStdDate(trim($posts["tanggal_akhir"]));
        
        $posts["keterangan"] = trim($posts["keterangan"]);
        $posts["kdlokasi"] = $this->kodeLokasi;
        $posts["usergroup"] = $this->kodeGrup;

        if (empty($posts["pin_absen"]) || empty($posts["kd_jenis"]) || empty($posts["tanggal_awal"]) || empty($posts["tanggal_akhir"])) {
            echo json_encode([
                "status" => "fail",
                "message" => "Lengkapi semua isian dengan benar!"
            ]);

            return;
        }

        $tmTglAwal = strtotime($posts["tanggal_awal"]." 23:59:59");
        $tmTglAkhir = strtotime($posts["tanggal_akhir"]." 23:59:59");

        if ($tmTglAwal > $tmTglAkhir) {
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN: Tanggal awal moderasi tidak boleh melebihi tanggal akhirnya!"
            ]);

            return;
        }

        $deltaDates = intval(FUNC::getHusnanWDeltaDates($posts["tanggal_awal"], date("Y-m-d")));
        $currDate = intval(date('d'));
        /* VALID FILTER DISABLE FOR A WHILE
        if (($currDate <= 3 && $tmTglAwal < strtotime("first day of last month")) || ($currDate > 3 && $tmTglAwal < strtotime('first day of this month')) || $tmTglAwal > strtotime("+3 days 23 hours 59 minutes 59 seconds")) {
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN:\n\rProses dibatalkan karena pemoderasian hanya dapat dilakukan mulai dari tanggal 1 bulan sebelumnya (jika sekarang tanggal 1-3 bulan berjalan) atau mulai tanggal 1 bulan berjalan (jika sekarang lebih dari tanggal 3 bulan berjalan) sampai tanggal H+3 bulan berjalan."
            ]);
            return;
        }
        */
        
        /* OLD UNUSED
        if ($tmTglAwal < strtotime('-1 months 0 days 0 hours 0 minutes 0 seconds') || $tmTglAwal > strtotime("+3 days 23 hours 59 minutes 59 seconds")) {    
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN:\n\rProses dibatalkan karena pemoderasian hanya dapat dilakukan mulai dari tiga hari terakhir bulan lalu dan paling cepat H-3 sebelum hari H bulan berjalan."
            ]);
            return;
        }
        */

        //FUNC::husnanWVarDump($posts);
        if (count($katMods) === 1) {
            if (!$this->servicemaster->isValidJumlahModerasiSatuPeriode($posts["kdlokasi"], $posts["pin_absen"], $posts["kd_jenis"], $posts["kode_presensi"], $posts["tanggal_awal"], $posts["tanggal_akhir"])) {
                echo json_encode([
                    "status" => "fail",
                    "message" => "PERHATIAN:\n\rProses dibatalkan karena sistem mendeteksi Anda telah menginputkan kategori moderasi yang sama(".$posts["kode_presensi"].") dalam suatu waktu yang sama. Pemoderasian dalam satu periode waktu yang telah Anda tentukan tidak boleh memiliki kategori moderasi yang sama atau jika Anda memilih kategori semuanya maka tidak boleh memilih kategori selain itu dan sebaliknya."
                ]);
                return;
            }

            if ($this->servicemaster->simpanPemohonModerasi($posts) > 0) {
                $status = [
                    "status" => "success",
                    "message" => "single category input",
                    "lid" => $this->servicemaster->getLastId("tb_moderasi")
                ];
            } else {
                $status = [
                    "status" => "fail",
                    "message" => "Sistem gagal menyimpan data moderasi!"
                ];
            }
        } else {
            $isInsertOk = true;
            $lids = [];

            foreach ($katMods as $katMod) {
                $posts["kd_jenis"] = $katMod;

                if (!$this->servicemaster->isValidJumlahModerasiSatuPeriode($posts["kdlokasi"], $posts["pin_absen"], $posts["kd_jenis"], $posts["kode_presensi"], $posts["tanggal_awal"], $posts["tanggal_akhir"])) {
                    echo json_encode([
                        "status" => "fail",
                        "message" => "PERHATIAN:\n\rProses semua atau sebagian dibatalkan karena sistem mendeteksi Anda telah menginputkan kategori moderasi yang sama(".$posts["kode_presensi"].") dalam suatu waktu yang sama. Pemoderasian dalam satu periode waktu yang telah Anda tentukan tidak boleh memiliki kategori moderasi yang sama atau jika Anda memilih kategori semuanya maka tidak boleh memilih kategori selain itu dan sebaliknya."
                    ]);
                    return;
                }

                if ($this->servicemaster->simpanPemohonModerasi($posts) < 1) {
                    $isInsertOk = false;
                    break;
                } else {
                    $lids[] = $this->servicemaster->getLastId("tb_moderasi");
                }
            }

            if ($isInsertOk) {
                $status = [
                    "status" => "success",
                    "message" => "multiple categories input",
                    "lid" => $lids
                ];
            } else {
                $status = [
                    "status" => "fail",
                    "message" => "Sistem gagal menyimpan kumpulan data moderasi!"
                ];
            }
        }
        
        echo json_encode($status);
    }
    
    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
