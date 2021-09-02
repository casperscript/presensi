<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use app\pns\model\moderasi_service;
use app\pns\model\pegawai_service;
//use app\pns\model\presensi_service;
use system;
use comp;

class moderasi extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->dbmoderasi = new moderasi_service();
            $this->dbpegawai = new pegawai_service();
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $field = ['pin_absen', 'nama_personil', 'nipbaru'];
        $infoPegawai = $this->servicemain->getDataKrit('db_pegawai', 'view_personal_pns', ['nipbaru' => $this->login['nipbaru']], [], $field);
        $this->setSession('SESSION_TEMP', $infoPegawai);

        $data['title'] = 'Daftar Moderasi';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabelDesktop() {
        $input = $this->post(true);
        if ($input) {
            $input['pin_absen'] = $this->getSession('SESSION_TEMP')['pin_absen'];
            $data['dataTabel'] = $this->dbmoderasi->getDaftarModerasi($input);

            $this->subView('tabelDesktop', $data);
        }
    }

    protected function tabelMobile() {
        $input = $this->post(true);
        if ($input) {
            $input['pin_absen'] = $this->getSession('SESSION_TEMP')['pin_absen'];
            $data['dataTabel'] = $this->dbmoderasi->getDaftarModerasi($input);
            $this->subView('tabelMobile', $data);
        }
    }

    protected function formDesktop($id) {
        $input = $this->post(true);
        if ($input) {
            $input['pin_absen'] = $this->getSession('SESSION_TEMP')['pin_absen'];

            $data['title'] = empty($input['id']) ? 'Input Moderasi' : 'Edit Moderasi';
            $data['moderasi'] = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', $input);
            $data['jenisMod'] = $this->servicemain->getPilKrit('db_presensi', 'tb_jenis_moderasi', [], ['key' => 'kd_jenis', 'value' => 'nama_jenis']);

            $kodeMod = $this->dbmoderasi->getJenisModerasi([$data['moderasi']['kd_jenis']])['value'];
            $data['kodeMod'] = array_combine(array_column($kodeMod, 'kode_presensi'), array_column($kodeMod, 'ket_kode_presensi'));
            $data['route'] = $this->link($this->getProject() . $this->getController());

            $this->subView('formDesktop', $data);
        }
    }

    protected function formMobile($id) {
        $input = $this->post(true);
        if ($input) {
            $input['pin_absen'] = $this->getSession('SESSION_TEMP')['pin_absen'];
            $data['title'] = empty($input['id']) ? 'Input Moderasi' : 'Edit Moderasi';
            $data['moderasi'] = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', $input);
            $data['jenisMod'] = $this->servicemain->getPilKrit('db_presensi', 'tb_jenis_moderasi', [], ['key' => 'kd_jenis', 'value' => 'nama_jenis']);
            $data['kodeMod'] = $this->servicemain->getPilKrit('db_presensi', 'tb_kode_presensi', ['moderasi_kode_presensi' => '1'], ['key' => 'kode_presensi', 'value' => 'CONCAT(kode_presensi, " - ", ket_kode_presensi)']);

            $this->subView('formMobile', $data);
        }
    }

    protected function detailModerasiDesktop($id) {
        if (!empty($id)) {
            $data['pegawai'] = $this->getSession('SESSION_TEMP');

            $data['moderasi'] = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', ['id' => $id]);
            $data['kodeMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_kode_presensi', ['kode_presensi' => $data['moderasi']['kode_presensi']]);
            $data['jenisMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_jenis_moderasi', $data['moderasi']);
            $data['lampiran'] = $this->servicemain->getTabelKrit('db_presensi', 'tb_dokumen_moderasi', ['moderasi_id' => $id])['dataTabel'];

            $kdlokasi = isset($data['moderasi']['kdlokasi']) ? $data['moderasi']['kdlokasi'] : '';
            $year = isset($data['moderasi']['tanggal_awal']) ? date('Y', strtotime($data['moderasi']['tanggal_awal'])) : '';
            $month = isset($data['moderasi']['tanggal_awal']) ? date('n', strtotime($data['moderasi']['tanggal_awal'])) : '';
            $data['lockMod'] = $this->dbmoderasi->chkLockMod($kdlokasi, $year, $month);

            $this->subView('detailDesktop', $data);
        }
    }

    protected function detailModerasiMobile($id) {
        if (!empty($id)) {
            $data['pegawai'] = $this->getSession('SESSION_TEMP');
            $data['moderasi'] = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', ['id' => $id]);
            $data['kodeMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_kode_presensi', ['kode_presensi' => $data['moderasi']['kode_presensi']]);
            $data['jenisMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_jenis_moderasi', $data['moderasi']);
            $data['lampiran'] = $this->servicemain->getTabelKrit('db_presensi', 'tb_dokumen_moderasi', ['moderasi_id' => $id])['dataTabel'];

            /* Cek Report */
            $kdlokasi = isset($data['moderasi']['kdlokasi']) ? $data['moderasi']['kdlokasi'] : '';
            $year = isset($data['moderasi']['tanggal_awal']) ? date('Y', strtotime($data['moderasi']['tanggal_awal'])) : '';
            $month = isset($data['moderasi']['tanggal_awal']) ? date('n', strtotime($data['moderasi']['tanggal_awal'])) : '';
            $data['lockMod'] = $this->dbmoderasi->chkLockMod($kdlokasi, $year, $month);

            $this->subView('detailMobile', $data);
        }
    }

    protected function simpan() {
        $input = $this->post(true);
        if ($input) {
            $dataPegawai = $this->getSession('SESSION_TEMP');
            $dataLogin = $this->getSession('SESSION_LOGIN');
            $input['tanggal_awal'] = (empty($input['tanggal_awal']) && !empty($input['tanggal_akhir'])) ? $input['tanggal_akhir'] : $input['tanggal_awal'];
            $input['tanggal_akhir'] = (empty($input['tanggal_akhir']) && !empty($input['tanggal_awal'])) ? $input['tanggal_awal'] : $input['tanggal_akhir'];

            $data = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', ['id' => $input['id']]);

            foreach ($data as $key => $val) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }

            $input['pin_absen'] = $dataPegawai['pin_absen'];
            $input['kdlokasi'] = $dataLogin['kdlokasi'];

            // Rentang waktu maksimal pengajuan moderasi di hari esok
            $getBatasAtasMod = $this->servicemain->getDataSetting('batas_atas_moderasi');
            $tglBatasAtasMod = date('Y-m-d', strtotime('+' . $getBatasAtasMod['value'] . ' day'));
            $chkBatasAtasMod = $input['tanggal_awal'] > $tglBatasAtasMod ? false : true;

            // Mewajibkan jenis moderasi tertentu untuk melampirkan dokumen
            $arrModWajibLampiran = ['CD','DD'];
            if (in_array($input['kode_presensi'], $arrModWajibLampiran) && ($_FILES['lampiran']['error'] != 0)) :
                $impModWajibLampiran = implode(', ', $arrModWajibLampiran);
                $error_msg = [
                    'title' => 'Moderasi Ditolak',
                    'status' => 'warning',
                    'message' => 'Moderasi ' . $impModWajibLampiran . ' diharuskan melampirkan surat keterangan'
                ];
                echo json_encode($error_msg);
                exit;
            endif;

            // Batas akhir pengajuan moderasi
//            $getBatasInputMod = $this->servicemain->getDataSetting('batas_input_moderasi');
//            $tglBatasInputMod = (date('Ym', strtotime($input['tanggal_awal'])) < date('Ym')) ?
//                    $getBatasInputMod['value'] : date('Y-m-d', strtotime($getBatasInputMod['value'] . '+1 month'));
//            $chkBatasInputMod = (date('Y-m-d') > $tglBatasInputMod) ? true : false;

            // Batas maksimal pengajuan moderasi lupa
            $modLupa = ['L1', 'L2', 'L3', 'L4', 'L5'];
            $modJenis = ['JNSMOD01', 'JNSMOD02', 'JNSMOD03'];
            $chkMaksModLupa = false;
            $chkKdJenisMod = count(array_intersect($modJenis, $input['kd_jenis']));
            if (in_array($input['kode_presensi'], $modLupa) && $chkKdJenisMod > 0) :
                $paramMD01['kd_jenis'] = $modJenis;
                $paramMD01['tahun'] = date('Y', strtotime($input['tanggal_awal']));
                $paramMD01['bulan'] = date('m', strtotime($input['tanggal_awal']));
                $paramMD01['pin_absen'] = $dataPegawai['pin_absen'];
//                $paramMD01['not'] = $input['tanggal_awal'];
                $getMaksModLupa = $this->servicemain->getDataSetting('maks_mod_lupa');
                $getCountModLupa = $this->dbmoderasi->getCountMod($paramMD01, $modLupa, 'GROUP BY `kd_jenis`');
                
                foreach ($input['kd_jenis'] as $val) {
                    if ($getCountModLupa[$val] >= $getMaksModLupa['value'] && $chkMaksModLupa == false) {
                        $chkMaksModLupa = true;
                    }
                }
            endif;

            $chkExistMod = $this->dbmoderasi->chkExistMod($input);
            $chkLockReport = $this->dbmoderasi->getReport($input);
            $chkLockException = $this->dbmoderasi->getExceptPeriodMod($input);
            $limitTanggal = strtotime('2019-01-01');
            $chkTglAwal = strtotime($input['tanggal_awal']) >= $limitTanggal ? true : false;
            $chkTglAkhir = strtotime($input['tanggal_akhir']) >= $limitTanggal ? true : false;

            if (strtotime($input['tanggal_akhir']) < strtotime($input['tanggal_awal'])) { // Check tanggal
                $error_msg = [
                    'title' => 'Tanggal Salah',
                    'status' => 'warning',
                    'message' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal'
                ];
            } else if ($chkTglAwal == false || $chkTglAkhir == false) {
                $error_msg = [
                    'title' => 'Tanggal Salah',
                    'status' => 'warning',
                    'message' => 'Format tanggal salah atau tanggal kurang dari 1 Januari 2018'
                ];
            } else if (isset($chkMaksModLupa) && $chkMaksModLupa == true) {
                $error_msg = [
                    'title' => 'Moderasi Lupa Terbatas',
                    'status' => 'warning',
//                    'message' => 'Maksimal pengajuan moderasi lupa adalah ' . $getMaksModLupa['value'] . ' hari dalam 1 bulan'
                    'message' => 'Maksimal pengajuan moderasi lupa adalah ' . $getMaksModLupa['value'] . ' kali dalam 1 bulan. '
                    . 'Anda sudah menggunakan moderasi lupa untuk Masuk ' . $getCountModLupa['JNSMOD01'] . ', Apel ' . $getCountModLupa['JNSMOD02'] . ', dan Pulang ' . $getCountModLupa['JNSMOD03']
                ];
            } else if ($chkBatasAtasMod == false) {
                $error_msg = [
                    'title' => 'Tanggal pengajuan melebihi ketentuan',
                    'status' => 'warning',
                    'message' => 'Maksimal tanggal pengajuan adalah ' . $getBatasAtasMod['value'] . ' hari dari sekarang, yaitu ' . comp\FUNC::tanggal($tglBatasAtasMod, 'long_date')
                ];
//            } else if ($chkBatasInputMod) {
//                $error_msg = [
//                    'title' => 'Moderasi ditolak',
//                    'status' => 'warning',
//                    'message' => 'Batas akhir pengajuan moderasi untuk bulan ' . comp\FUNC::$namabulan1[date('m', strtotime($input['tanggal_awal']))] . ' adalah: ' . comp\FUNC::tanggal($tglBatasInputMod, 'long_date'),
//                ];
            } else if ($chkExistMod['count'] > 0) { // Check input baru agar tidak konflik
                $error_msg = [
                    'title' => 'Moderasi Ganda',
                    'status' => 'warning',
                    'message' => 'Sudah ada moderasi pada tanggal tersebut'
                ];
            } else if ($chkLockReport['count'] > 0) {
                $error_msg = [
                    'title' => 'Laporan Dikunci',
                    'status' => 'warning',
                    'message' => 'Laporan moderasi pada tanggal tersebut sudah diverifikasi oleh Admin OPD.',
                ];
            } else if ($chkLockException['count'] > 0) {
                $error_msg = [
                    'title' => 'Moderasi Ditolak',
                    'status' => 'warning',
                    'message' => 'Tanggal moderasi berada pada periode yang dilarang.'
                ];
            } else { // Insert dan Update data
                $allowedFileTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/vnd.oasis.opendocument.text", "application/vnd.openxmlformats-officedocument.wordprocessingml", "application/msword", "application/pdf"];

                // Jika melampirkan file
                if (!empty($_FILES['lampiran']['error'] == 0)) {

                    // Cek apakah file lampiran sesuai ekstensinya
                    if (in_array($_FILES["lampiran"]["type"], $allowedFileTypes)) {

                        // upload dokument
                        $filename = comp\FUNC::husnanWGenRand() . '-' . $_FILES['lampiran']['name'];
                        $uploadfile = $this->husnanw_moderasi_upload_path . '/' . basename($filename);

                        if (move_uploaded_file($_FILES["lampiran"]["tmp_name"], $uploadfile)) {
                            $dataFile['id'] = (NULL);
                            $dataFile['filename'] = $filename;
                            $dataFile['dt_created'] = date('Y-m-d H:i:s');
                            $dataFile['dt_modified'] = date('Y-m-d H:i:s');
                        }
                    } else {
                        $error_msg = ['title' => 'Lampiran Tidak Diijinkan', 'status' => 'warning', 'message' => ''];
                        echo json_encode($error_msg);
                        exit;
                    }
                }

                foreach ($input['kd_jenis'] as $key => $val) {

                    // Insert data moderasi ke database
                    $data['id'] = empty($input['id']) ? date('YmdHis') . $dataPegawai['pin_absen'] . rand(0, 99) : $input['id'];
                    $data['kd_jenis'] = $val;
                    $data['pin_absen'] = $dataPegawai['pin_absen'];
                    $data['kdlokasi'] = $dataLogin['kdlokasi'];
                    $data['usergroup'] = $dataLogin['grup_pengguna_kd'];
                    $data['status_final'] = 'DIPROSES';
                    $data['flag_operator_opd'] = NULL;
                    $data['flag_kepala_opd'] = NULL;
                    $data['flag_operator_kota'] = NULL;
                    $data['flag_kepala_kota'] = NULL;

                    $data['author'] = $this->login['username'];
                    $data['dt_created'] = (empty($input['id'])) ? date('Y-m-d H:i:s') : $data['dt_created']; // Data baru
                    $data['dt_last_modified'] = (!empty($input['id'])) ? date('Y-m-d H:i:s') : $data['dt_last_modified']; // Data mengedit yang sudah ada

                    $result = $this->dbmoderasi->save_update('tb_moderasi', $data);
                }

                if ($result['error'] && isset($dataFile)) {
                    $dataFile['moderasi_id'] = $data['id'];
                    $this->dbmoderasi->save_update('tb_dokumen_moderasi', $dataFile);
                }

                $error_msg = ($result['error']) ?
                        ['title' => 'Sukses', 'status' => 'success', 'message' => 'Moderasi berhasil dibuat'] :
                        ['title' => 'Moderasi Error', 'status' => 'error', 'message' => 'Moderasi gagal dibuat, terdapat kesalahan sistem ketika menyimpan', 'query' => $dataFile];
            }
            echo json_encode($error_msg);
        }
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

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = ['id' => $input['id']];

            // Cek ijin hapus moderasi
            $modInfo = $this->dbmoderasi->getDataKrit('db_presensi', 'tb_moderasi', $idKey);
            $kdlokasi = $this->login['kdlokasi'];
            $month = date('n', strtotime($modInfo['tanggal_awal']));
            $year = date('Y', strtotime($modInfo['tanggal_awal']));
            $cekLaporan = $this->dbmoderasi->chkLockMod($kdlokasi, $year, $month);

            if ($cekLaporan) {
                $error_msg = [
                    'title' => 'Dikunci',
                    'status' => 'warning',
                    'message' => 'Moderasi bulan ' . comp\FUNC::$namabulan1[$month] . ' tahun ' . $year . ' sudah diverifikasi'
                ];
                echo json_encode($error_msg);
                return;
            }

            // List dan hapus file moderasi
            $arrLamp = $this->servicemain->getTabelKrit('db_presensi', 'tb_dokumen_moderasi', ['moderasi_id' => $input['id']])['dataTabel'];
            foreach ($arrLamp as $val) {
                $filepath = $this->husnanw_moderasi_upload_path . '/' . $val['filename'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                $this->dbmoderasi->delete('tb_dokumen_moderasi', ['id' => $val['id']]);
            }

            // Hapus data moderasi
            $result = $this->dbmoderasi->delete('tb_moderasi', $idKey);
            $error_msg = ($result['error']) ?
                    ['title' => 'Sukses', 'status' => 'success', 'message' => 'Moderasi berhasil dihapus'] :
                    ['title' => 'Gagal', 'status' => 'warning', 'message' => 'Terjadi kesalahan ketika menghapus'];

            echo json_encode($error_msg);
        }
    }

    protected function hapusDokumen() {
        $input = $this->post(true);
        if ($input) {
            $idKey = ['id' => $input['id']];
            $filename = $this->servicemain->getDataKrit('db_presensi', 'tb_dokumen_moderasi', $idKey)['filename'];
            $filepath = $this->husnanw_moderasi_upload_path . '/' . $filename;

            if (file_exists($filepath)) {
                unlink($filepath);

                $result = $this->dbmoderasi->delete('tb_dokumen_moderasi', $idKey);
                $error_msg = ($result['error']) ?
                        ['title' => 'Sukses', 'status' => 'success', 'message' => 'Dokumen moderasi berhasil dihapus'] :
                        ['title' => 'Gagal', 'status' => 'warning', 'message' => 'Terjadi kesalahan ketika menghapus'];
            } else {
                $error_msg = ['title' => 'Gagal', 'status' => 'warning', 'message' => 'File lampiran tidak ditemukan'];
            }
            echo json_encode($error_msg);
        }
    }

    protected function script() {
        $this->subView('script', []);
    }

    /* Get JSON */

    public function getJenisModerasi($kodeKatMod) {
        $kodeKatMod = trim($kodeKatMod);
        if ($kodeKatMod === "null" || $kodeKatMod === "") {
            echo json_encode('');
            return false;
        }

        $arrKode = explode('|', $kodeKatMod);
        if (count($arrKode) > 1 && in_array('JNSMOD04', $arrKode)) {
            echo json_encode("WRONG CATEGORIES!");
            return false;
        }

        $data = $this->dbmoderasi->getJenisModerasi($arrKode);
        echo json_encode($data['value']);
    }

    public function getTahunModerasi() {
        $tahun = date('Y');
        $arrTahun = array($tahun => $tahun);
        $pegawai = $this->dbmoderasi->getPegawai($this->login['nipbaru']);

        $pinabsen = (count($pegawai) > 0) ? $pegawai[0]['pin_absen'] : array();
        $listTahun = $this->dbmoderasi->getListTahun($pinabsen);
        $currentTahun = (in_array($tahun, $listTahun) == false) ? $arrTahun : array();
        $result = $listTahun + $currentTahun;

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function getBulanModerasi($tahun) {
        $bulan = date('n');
        $arrBulan = array($bulan => comp\FUNC::$namabulan1[$bulan]);
        $pegawai = $this->dbmoderasi->getPegawai($this->login['nipbaru']);
        $listBulan = $this->dbmoderasi->getListBulan($pegawai, $tahun);

        $currentBulan = (in_array($bulan, $listBulan) == false && $tahun == date('Y')) ? $arrBulan : array();
        $result = $listBulan + $currentBulan;

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    protected function test() {
        // $tesHadir = $this->dbmoderasi->getRekapHadir('10000951');
        comp\FUNC::showPre($this->login);
    }

//Untuk adminopd
//    public function getTahunModerasi() {
//        $tahun = date('Y');
//        $arrTahun = array($tahun => $tahun);
//        $kdlokasi = $this->login['kdlokasi'];
//        $pegawai = $this->dbmoderasi->getListPegawai($kdlokasi);
//        
//        $listTahun = $this->dbmoderasi->getListTahun($pegawai);
//        $tes = (in_array($tahun, $listTahun) == false) ? $arrTahun : array();
//        $result = $listTahun + $tes;
//        echo json_encode($result);
//    }
//Untuk adminopd
//    public function getBulanModerasi($tahun) {
//        $kdlokasi = $this->login['kdlokasi'];
//        $pegawai = $this->dbmoderasi->getListPegawai($kdlokasi);
//        
//        $result = $this->dbmoderasi->getListBulan($pegawai, $tahun);
//        echo json_encode($result);
//    }
}
