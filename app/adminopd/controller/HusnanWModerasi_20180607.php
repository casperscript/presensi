<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\HusnanWModerasiModel;
use system;
use comp\FUNC;
use comp;

//use comp\MATERIALIZE;

class HusnanWModerasi extends system\Controller {

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
            $this->kodeGrup = $this->login["grup_pengguna_kd"];
            $this->dateLimit = $this->servicemaster->getDateLimit($this->kodeGrup);
            $this->kodeLokasi = $this->login["kdlokasi"];
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    public function checkDatesMod() {
        //echo json_encode(["status" => "success", "message" => ""]);
        //return;
        $posts = $this->post(true);
        $dateAwal = FUNC::toHusnanWStdDate(trim($posts["dateAwal"]));
        $dateAkhir = FUNC::toHusnanWStdDate(trim($posts["dateAkhir"]));

        $result = $this->servicemaster->checkDatesMod($dateAwal, $dateAkhir, $posts["pinAbsen"]);

        if ($result["status"] === "success") {
            echo json_encode(["status" => "success", "message" => ""]);
        } else {
            echo json_encode(["status" => "fail", "message" => "PERHATIAN:\n\rSistem mendeteksi pengajuan tanggal moderasi yang rangkap disebabkan karena:\n\r1.Tanggal awal dan atau akhir yang Anda masukkan berada diantara dua tanggal yang telah dimoderasi atau,\n\r2. Tanggal awal dan atau akhir telah mencakup sebagian atau seluruh tanggal yang telah dimoderasi(" . FUNC::toHusnanWSniDate($result["tglAwal"]) . " - " . FUNC::toHusnanWSniDate($result["tglAkhir"]) . ").\n\rAnda tidak diperbolehkan melakukan moderasi diantara atau mencakup tanggal yang telah Anda moderasi sebelumnya. Mohon dikoreksi kembali."]);
        }
    }

    protected function index() {
        //var_dump($this->getDaftarPegawaiModerasi('G09011')); exit();
        $data['title'] = 'Pengajuan Moderasi';
        $data['table_title'] = '';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Moderasi</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pengajuan Moderasi</a>';
        $data["dateLimit"] = $this->dateLimit;
        $data["kategoriModerasi"] = $this->servicemaster->getKategoriModerasi();
        $data["jenisModerasi"] = $this->servicemaster->getJenisModerasi("JNSMOD01");
        $data["daftarPns"] = $this->servicemaster->getDaftarPegawaiModerasi($this->kodeLokasi);
        $this->showView('index', $data, 'theme_admin');
    }

    public function massVerifPage() {
        $posts = $this->post(true);
        $data["checkedMods"] = isset($posts["checkedMods"]) ? $posts["checkedMods"] : [];
        $data["checkedMods"] = implode(',', $data["checkedMods"]);
        $data["flag"] = $posts["flag"];
        $this->subView('mass-verif-page', $data);
    }

    public function delModerasi($mid) {
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

    public function uploadDokumenModerasi() {
        //$uploaddir = __DIR__."/upload/moderasi/dokumen";        
        $allowedFileTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/vnd.oasis.opendocument.text", "application/vnd.openxmlformats-officedocument.wordprocessingml", "application/msword", "application/pdf"];
        $filenames = [];

        foreach ($_FILES["fileDokumenPendukung"]["name"] as $index => $filename) {
            $filename = FUNC::husnanWGenRand() . '-' . $filename;
            $uploadfile = $this->husnanw_moderasi_upload_path . '/' . basename($filename);

            if (in_array($_FILES["fileDokumenPendukung"]["type"][$index], $allowedFileTypes)) {
                if (move_uploaded_file($_FILES["fileDokumenPendukung"]["tmp_name"][$index], $uploadfile)) {
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

    public function infoModerasi($mid) {
        $data["info"] = $this->servicemaster->getInfoModerasi($this->kodeLokasi, $mid);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function infoModerasiHasil($mid) {
        $data["info"] = $this->servicemaster->getInfoModerasi($this->kodeLokasi, $mid, true);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }



    protected function daftarVerModHasil() {
        $data['title'] = 'Daftar Hasil Pengajuan Moderasi Anda';
        $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($this->kodeLokasi, null, true);
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('daftar-ver-mod-hasil', $data, 'theme_admin');
    }

    public function updateModerasi() {
        $posts = $this->post(true);
        $flags = $this->servicemaster->getFlags($posts["mid"]);

        if (($posts["flag"] === "0" || $posts["flag"] === "1") && (!is_null($flags["flag_kepala_opd"]))) {
            echo json_encode(["status" => "fail", "reload" => "1"]);
            return;
        }

        $result = $this->servicemaster->updateModerasi($posts, $this->kodeLokasi);
        if ($result > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Status moderasi berhasil diperbarui', 'symbolMod' => comp\FUNC::modSymbol($posts['flag'], $posts['catatan'])]); // acil
            return;
        }

        echo json_encode(['status' => 'fail', 'message' => 'Status moderasi gagal dirubah']);
//        echo json_encode(["status" => "fail", "reload" => "0"]);
    }

    public function updateModerasiMassVerif() {
        $posts = $this->post(true);
        $mids = explode(',', $posts["mids"]);

        if (count($mids) === 0) {
            echo json_encode(["status" => "fail", "message" => "Tidak ada moderasi yang diproses!", "reload" => "1"]);
            return;
        }

        $validMids = [];

        foreach ($mids as $mid) {
            $flags = $this->servicemaster->getFlags($mid);

            if (($posts["flag"] !== "0" && $posts["flag"] !== "1") || (!is_null($flags["flag_kepala_opd"]))) {
                continue;
            } else {
                $validMids[] = $mid;
            }
        }

        $posts = ["mids" => $validMids, "flag" => $posts["flag"], "catatan" => $posts["catatan"]];

        if ($this->servicemaster->updateModerasi($posts, $this->kodeLokasi, true) > 0) {
            echo json_encode(["status" => "success"]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
    }

    public function getDaftarPegawaiModerasi() {
        $data = $this->servicemaster->getDaftarPegawaiModerasi($this->kodeLokasi);
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

    public function simpanPemohonModerasi() {
        $posts = $this->post(true);
        $pinAbsens = array_filter(explode(',', str_replace(' ', '', trim($posts["pin_absen"]))));
        $katMods = $posts["katMods"];
//FUNC::husnanWVarDump($posts["kd_jenis"]);
        if (count($katMods) === 1) {
            $kodeJenis = explode('|', trim($posts["kd_jenis"]));
            $posts["kd_jenis"] = $kodeJenis[0];
            $posts["kode_presensi"] = $kodeJenis[1];
        } elseif (count($katMods) > 1) {
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

        if (empty($pinAbsens) || empty($posts["kd_jenis"]) || empty($posts["tanggal_awal"]) || empty($posts["tanggal_akhir"])) {
            echo json_encode([
                "status" => "fail",
                "message" => "Lengkapi semua isian dengan benar!"
            ]);

            return;
        }

        $tmTglAwal = strtotime($posts["tanggal_awal"] . " 23:59:59");
        $tmTglAkhir = strtotime($posts["tanggal_akhir"] . " 23:59:59");

        if ($tmTglAwal > $tmTglAkhir) {
            echo json_encode([
                "status" => "fail",
                "message" => "PERHATIAN: Tanggal awal moderasi tidak boleh melebihi tanggal akhirnya!"
            ]);

            return;
        }

        $deltaDates = intval(FUNC::getHusnanWDeltaDates($posts["tanggal_awal"], date("Y-m-d")));

        $currDate = intval(date('d'));

        $isInsertOk = true;
        $lids = [];

        if (count($katMods) === 1) {
            foreach ($pinAbsens as $pinAbsen) {
                $posts["pin_absen"] = $pinAbsen;
                if (!$this->servicemaster->isValidJumlahModerasiSatuPeriode($posts["kdlokasi"], $posts["pin_absen"], $posts["kd_jenis"], $posts["kode_presensi"], $posts["tanggal_awal"], $posts["tanggal_akhir"])) {
                    if (count($pinAbsens) <= 1) {
                        echo json_encode([
                            "status" => "fail",
                            "message" => "PERHATIAN:\n\rProses dibatalkan karena sistem mendeteksi Anda telah menginputkan kategori moderasi yang sama(" . $posts["kode_presensi"] . ") dalam suatu waktu yang sama. Pemoderasian dalam satu periode waktu yang telah Anda tentukan tidak boleh memiliki kategori moderasi yang sama atau jika Anda memilih kategori semuanya maka tidak boleh memilih kategori selain itu dan sebaliknya."
                        ]);
                        return;
                    } else {
                        continue; // skip input
                    }
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
                    "message" => "single category input",
                    "lid" => $lids
                ];
            } else {
                $status = [
                    "status" => "fail",
                    "message" => "Sistem gagal menyimpan data moderasi!"
                ];
            }
        } else {
            foreach ($katMods as $katMod) {
                $posts["kd_jenis"] = $katMod;
                foreach ($pinAbsens as $pinAbsen) {
                    $posts["pin_absen"] = $pinAbsen;
                    if (!$this->servicemaster->isValidJumlahModerasiSatuPeriode($posts["kdlokasi"], $posts["pin_absen"], $posts["kd_jenis"], $posts["kode_presensi"], $posts["tanggal_awal"], $posts["tanggal_akhir"])) {
                        if (count($pinAbsens) <= 1) {
                            echo json_encode([
                                "status" => "fail",
                                "message" => "PERHATIAN:\n\rProses semua atau sebagian dibatalkan karena sistem mendeteksi Anda telah menginputkan kategori moderasi yang sama(" . $posts["kode_presensi"] . ") dalam suatu waktu yang sama. Pemoderasian dalam satu periode waktu yang telah Anda tentukan tidak boleh memiliki kategori moderasi yang sama atau jika Anda memilih kategori semuanya maka tidak boleh memilih kategori selain itu dan sebaliknya."
                            ]);
                            return;
                        } else {
                            continue;
                        }
                    }

                    if ($this->servicemaster->simpanPemohonModerasi($posts) < 1) {
                        $isInsertOk = false;
                        break;
                    } else {
                        $lids[] = $this->servicemaster->getLastId("tb_moderasi");
                    }
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

    /*     * ** Added by Zaenal *** */
    protected function daftarVerMod() {
		$data['title'] = 'Daftar Proses Pengajuan Moderasi Anda';
        $data["dateLimit"] = $this->dateLimit;
        $this->showView('index-proses-moderasi', $data, 'theme_admin');
    }

    protected function daftarVerModMobile() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = 'Daftar Proses Pengajuan Moderasi Anda';
            $input['kdlokasi'] = $this->kodeLokasi;

            $input['field'] = '*';
            $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($input);

            /* Daftar personil */
            $input['arrPin'] = (count($data['daftarVerMod']) > 0) ? $data['daftarVerMod']['pegawai'] : array();
            $input['field'] = 'CONCAT(gelar_depan, " ", namapeg , " ", gelar_blkg) AS nama_personil, pin_absen';
            $data['daftarPegawai'] = $this->servicemaster->getDaftarPegawaiMod($input);

            $data['pilJenMod'] = $this->servicemaster->getPilKrit('db_presensi', 'tb_jenis_moderasi', [], array('key'=> 'kd_jenis', 'value' => 'nama_jenis'));

            $data["dateLimit"] = $this->dateLimit;
            $this->subView('daftar-moderasi-mobile', $data);
        }
    }

    protected function daftarVerModDesktop() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = 'Daftar Proses Pengajuan Moderasi Anda';
            $input['kdlokasi'] = $this->kodeLokasi;

            /* Daftar moderasi */
            $input['field'] = '*';
            $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($input);

            /* Daftar personil */
            $input['arrPin'] = (count($data['daftarVerMod']) > 0) ? $data['daftarVerMod']['pegawai'] : array();
            $input['field'] = 'CONCAT(gelar_depan, " ", namapeg , " ", gelar_blkg) AS nama_personil, pin_absen';
            $data['daftarPegawai'] = $this->servicemaster->getDaftarPegawaiMod($input);

            $data["dateLimit"] = $this->dateLimit;
            $this->subView('daftar-moderasi-desktop', $data);
        }
    }

    protected function simpanVerif() {
        $input = $this->post(true);
        if ($input) {
            $mids = explode(',', $input['mods']);
            if (count($mids) === 0) {
                echo json_encode(["status" => "fail", "message" => "Tidak ada moderasi yang diproses!", "reload" => "1"]);
                return;
            }

            $validMids = [];

            foreach ($mids as $mid) {
                $flags = $this->servicemaster->getFlags($mid);

                if (($input["flag"] !== "0" && $input["flag"] !== "1") || (!is_null($flags["flag_kepala_opd"]))) {
                    continue;
                } else {
                    $validMids[] = $mid;
                }
            }

            $posts = ["mids" => $validMids, "flag" => $input["flag"], "catatan" => $input["catatan"]];

            if ($this->servicemaster->updateModerasi($posts, $this->kodeLokasi, true) > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Moderasi berhasil dirubah', 'reload' => '1']);
                return;
            }

            echo json_encode(['status' => 'fail', 'message' => 'Data gagal disimpan', 'reload' => '0']);
            return;
        }
    }

    protected function simpanVerifDesktop () {
        $input = $this->post(true);
        if ($input) {
            $moderasi = $this->servicemaster->getDataKrit('db_presensi', 'tb_moderasi', array('id' => $input['id']));
            $moderasi['dt_flag_operator_opd'] = date('Y-m-d H:i:s');
            $moderasi['dt_last_modified'] = date('Y-m-d H:i:s');

            foreach ($moderasi as $key => $val) {
                if (isset($input[$key])) {
                    $moderasi[$key] = $input[$key];
                }
            }

            $result = $this->servicemaster->save_update('tb_moderasi', $moderasi);
            $errMsg = ($result['error']) ? ['status' => 'success', 'message' => 'Data berhasil diperbarui', 'id' => $moderasi['id'], 'badge' => comp\FUNC::modSymbol($moderasi['flag_operator_opd'], $moderasi['catatan_operator_opd'])] : ['status' => 'error', 'message' => 'Data gagal disimpan'];
            echo json_encode($errMsg);
            //comp\FUNC::showPre($result);

        } else {
            echo "kosong";
        }
    }

    protected function detailModerasiDesktop($id) {
        if (!empty($id)) {
            $input['id'] = $id;
            $data['moderasi'] = $this->servicemaster->getDataKrit('db_presensi', 'tb_moderasi', $input);
            $data['kodeMod'] = $this->servicemaster->getDataKrit('db_presensi', 'tb_kode_presensi', ['kode_presensi' => $data['moderasi']['kode_presensi']]);
            $input['arrPin'] = [$data['moderasi']['pin_absen']];
            $data['pegawai'] = $this->servicemaster->getDaftarPegawaiMod($input)['value'][0];
            $data['jenisMod'] = $this->servicemaster->getDataKrit('db_presensi', 'tb_jenis_moderasi', $data['moderasi']);
            
            $this->subView('info-detail-desktop', $data);
        }
    }

    protected function detailModerasiMobile () {
        $input = $this->post(true);
        if ($input) {
            $data['moderasi'] = $this->servicemaster->getDataKrit('db_presensi', 'tb_moderasi', $input);
            $input['arrPin'] = [$data['moderasi']['pin_absen']];
            $data['pegawai'] = $this->servicemaster->getDaftarPegawaiMod($input)['value'][0];
            $data['jenisMod'] = $this->servicemaster->getDataKrit('db_presensi', 'tb_jenis_moderasi', $data['moderasi']);
            $this->subView('info-detail-mobile', $data);
            //echo json_encode('content' => $html);
        }
    }

    protected function simpanVerifMobile () {
        $input = $this->post(true);
        if ($input) {
            $moderasi = $this->servicemaster->getDataKrit('db_presensi', 'tb_moderasi', array('id' => $input['id']));
            $moderasi['dt_flag_operator_opd'] = date('Y-m-d H:i:s');
            $moderasi['dt_last_modified'] = date('Y-m-d H:i:s');

            foreach ($moderasi as $key => $val) {
                if (isset($input[$key])) {
                    $moderasi[$key] = $input[$key];
                }
            }

            $result = $this->servicemaster->save_update('tb_moderasi', $moderasi);
            $errMsg = ($result['error']) ? ['status' => 'success', 'message' => 'Data berhasil diperbarui', 'id' => $moderasi['id'], 'badge' => comp\FUNC::modStatus($moderasi['flag_operator_opd'])] : ['status' => 'error', 'message' => 'Data gagal disimpan'];
            echo json_encode($errMsg);
            //comp\FUNC::showPre($result);

        } else {
            echo "kosong";
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
    /*     * ** End *** */

}
