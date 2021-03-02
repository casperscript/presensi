<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\HusnanWModerasiModel;
use system;
use comp\FUNC;

class HusnanWModerasi extends system\Controller {
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
            $this->kodeGrup = $this->login["grup_pengguna_kd"];
            $this->dateLimit = $this->servicemaster->getDateLimit($this->kodeGrup);
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() { 
        //var_dump($this->getDaftarPegawaiModerasi('G09011')); exit();
        $data['title'] = 'Pengajuan Moderasi';
        $data['table_title'] = '';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Moderasi</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pengajuan Moderasi</a>';
        $data["jenisModerasi"] = $this->getJenisModerasi();
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('index', $data, 'theme_admin');
    }

    protected function loadTabelVerMod($kdlokasi) {
        //FUNC::husnanWVarDump($_GET);
        $status = isset($_GET["p4"]) ? $_GET["p4"] : null;
        $view = "tabel-ver-mod";
        $isFinal = false;

        if ($status === "final") {
            $isFinal = true;
            $view = "tabel-ver-mod-hasil";
        }

        $data['title'] = 'Tabel Verifikasi Moderasi';
        $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($kdlokasi, $isFinal);
        $data["dateLimit"] = $this->dateLimit;
        $this->subView($view, $data);
    }

    protected function daftarVerMod() {
        $data['title'] = 'Daftar Verifikasi Moderasi';
        $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod();
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('daftar-ver-mod', $data, 'theme_admin');
    }

    protected function daftarVerModHasil() {
        $data['title'] = 'Daftar Hasul Verifikasi Moderasi';
        $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod(null, true);
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('daftar-ver-mod-hasil', $data, 'theme_admin');
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
        
        $lastId = $this->servicemaster->getLastId("tb_moderasi");
        
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
            foreach ($filenames as $filename) {
                $dokumen["moderasi_id"] = $lastId;
                $dokumen["filename"] = $filename;
                $this->servicemaster->simpanDokumenModerasi($dokumen);
            }
            echo "success";
        } else {
            echo "fail";
        }
    }

    public function simpanPemohonModerasi()
    {
        $posts = $this->post(true);
        $posts["kdlokasi"] = trim($posts["kdlokasi"]);
        $posts["pin_absen"] = trim($posts["pin_absen"]);
        $posts["kd_jenis"] = trim($posts["kd_jenis"]);
        $posts["tanggal_awal"] = FUNC::toHusnanWStdDate(trim($posts["tanggal_awal"]));
        $posts["tanggal_akhir"] = FUNC::toHusnanWStdDate(trim($posts["tanggal_akhir"]));
        $posts["keterangan"] = trim($posts["keterangan"]);
        $posts["flag_operator_opd"] = '1';
        $posts["dt_flag_operator_opd"] = date("Y-m-d H:i:s");
        $posts["flag_kepala_opd"] = '1';
        $posts["dt_flag_kepala_opd"] = date("Y-m-d H:i:s");
        $posts["flag_operator_kota"] = '2';
        $posts["dt_flag_operator_kota"] = date("Y-m-d H:i:s");
        $posts["kdlokasi"] = $posts["kdlokasi"];
        $posts["usergroup"] = $this->kodeGrup;

        if (empty($posts["kdlokasi"]) || empty($posts["pin_absen"]) || empty($posts["kd_jenis"]) || empty($posts["tanggal_awal"]) || empty($posts["tanggal_akhir"])) {
            echo json_encode([
                "status" => "fail",
                "message" => "Lengkapi semua isian dengan benar!"
            ]);

            return;
        }

        $tmTglAwal = strtotime($posts["tanggal_awal"]);
        $tmTglAkhir = strtotime($posts["tanggal_akhir"]);

        if ($tmTglAwal > $tmTglAkhir) {
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN: Tanggal awal moderasi tidak boleh melebihi tanggal akhirnya!"
            ]);

            return;
        }

        if (intval(date('m')) === 1 && $tmTglAwal < strtotime(date("Y")."-01-01")) {
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN:\n\rProses dibatalkan karena untuk bulan Januari moderasi dimulai dari tanggal 1 Januari dan tidak boleh dari bulan-bulan dari tahun sebelumnya."
            ]);
            return;
        }

        $today = intval(date("d"));
        $minimumDate = date('Y-m').'-01';        

        if ($today >= 1 && $today <= $this->dateLimit)
        {
            $currDate = new \DateTime(date("Y-m-d"));
            $currDate->modify('-1 month');
            $minimumDate = $currDate->format('Y-m').'-01';
        }

        $strMinDate = date("Y-m-d", strtotime($minimumDate));
        $strTglAwal = date("Y-m-d", strtotime($posts["tanggal_awal"]));

        if ($strTglAwal < $strMinDate) {
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN:\n\rProses dibatalkan karena tidak sesuai dengan tanggal dibolehkannya pengajuan!\n\rBatas awal Moderasi: ".FUNC::toHusnanWSniDate($strMinDate)."\n\rTgl awal pengajuan Anda: ".FUNC::toHusnanWSniDate($posts["tanggal_awal"])
            ]);

            return;
        }

        if ($this->servicemaster->simpanPemohonModerasi($posts) > 0) {
            $status = [
                "status" => "success",
                "message" => "",
                "lid" => $this->servicemaster->getLastId("tb_moderasi")
            ];
        } else {
            $status = [
                "status" => "fail",
                "message" => "Sistem gagal menyimpan data moderasi!"
            ];
        }
        
        echo json_encode($status);
    }


    public function infoModerasi($mid)
    {
        $data["info"] = $this->servicemaster->getInfoModerasi($mid);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function infoModerasiHasil($mid)
    {
        $data["info"] = $this->servicemaster->getInfoModerasi($mid, true);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function massLegitPage()
    {
        $posts = $this->post(true);
        $data["checkedMods"] = isset($posts["checkedMods"]) ? $posts["checkedMods"] : [];
        $data["checkedMods"] = implode(',', $data["checkedMods"]);
        $this->subView('mass-legit-page', $data);
    }

    public function updateModerasi()
    {
        $posts = $this->post(true);
        $flags = $this->servicemaster->getFlags($posts["mid"]);
        
        if (($posts["flag"] !== "2") || (!is_null($flags["flag_kepala_kota"]))) {
            echo json_encode(["status" => "fail", "reload" => "1"]);
            return;
        }

        if ($this->servicemaster->updateModerasi($posts) > 0) {
            echo json_encode(["status" => "success", "date" => $this->servicemaster->getDateModified($posts["mid"])]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
    }

    public function updateModerasiMassLegit()
    {
        $posts = $this->post(true);
        $mids = explode(',', $posts["mids"]);

        if (count($mids) === 0) {
            echo json_encode(["status" => "fail", "message" => "Tidak ada moderasi yang diproses!", "reload" => "1"]);
            return;
        }

        $validMids = [];

        foreach ($mids as $mid) {
            $flags = $this->servicemaster->getFlags($mid);

            if (($posts["flag"] !== "2") || (!is_null($flags["flag_kepala_kota"]))) {
                continue;
            } else {
                $validMids[] = $mid;
            }
        }

        $posts = ["mids" => $validMids, "flag" => "2", "catatan" => $posts["catatan"]];
        
        if ($this->servicemaster->updateModerasi($posts, true) > 0) {
            echo json_encode(["status" => "success"]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
        
    }

    public function getDaftarOpd($kodeLokasi) {
        $data = $this->servicemaster->getDaftarOpd();
        echo json_encode($data);
    }

    public function getDaftarPegawaiModerasi($kodeLokasi) {
        $data = $this->servicemaster->getDaftarPegawaiModerasi($kodeLokasi);
        echo json_encode($data);
    }
    
    public function getJenisModerasi() {
        return $this->servicemaster->getJenisModerasi();
    }

    public function husnanWRandomize()
    {
        $pass = '';
        $vocals = ['a', 'i', 'u', 'e', 'o'];
        $consonants = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'w', 'y', 'z'];
        $nVocals = count($vocals);
        $nConsonants = count($consonants);
        
        for ($i = 0; $i < 6; $i++) {
            if ($i % 2 !== 0) { 
                $pass .= $vocals[rand(0, $nVocals - 1)];
            } else {
                $pass .= $consonants[rand(0, $nConsonants - 1)];
            }
        }
        
        return $pass.rand(100, 999);
    }

    public function husnanWSetAllPassRand($execute)
    {
        if (empty($execute) || $execute !== "execute") {
            echo '<h3 style="text-align: center;">Halaman Pengesetan Password Acak SEMUA Akun Presensi.<br> Tambahkan parameter: execute=1 pada url untuk menggunakan fitur ini.</h3>';
            exit();
        }

        $users = $this->servicemaster->getDaftarPengguna();

        foreach ($users as $user) {
            $passRand = $this->husnanWRandomize();
            if ($this->servicemaster->setPass($user["username"], $passRand)) {
                echo $user["username"].' => '.$passRand."<br>";
            } else {
                echo "<br>".$user["username"]." => FAILED<br><br>";
            }
        }
    }

    public function husnanWSetUsernamePassRand($username)
    {
        if (empty($username)) {
            echo '<h3 style="text-align: center;">Halaman Pengesetan Password Acak Akun Presensi yang Terpilih.<br> Tambahkan parameter: username=namauser pada url untuk mengubah password username tertentu.</h3>';
            exit();
        }

        $passRand = $this->husnanWRandomize();
        if ($this->servicemaster->setPass($username, $passRand)) {
            echo $user["username"].' => '.$passRand."<br>";
        } else {
            echo "<br>".$user["username"]." => FAILED<br><br>";
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}
