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
//        comp\FUNC::showPre($this->login);
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
            // comp\FUNC::showPre($data);
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
            $data['kodeMod'] = $this->servicemain->getPilKrit('db_presensi', 'tb_kode_presensi', ['moderasi_kode_presensi' => '1'], ['key' => 'kode_presensi', 'value' => 'CONCAT(kode_presensi, " - ", ket_kode_presensi)']);
            $this->subView('formDesktop', $data);
            //comp\FUNC::showPre($data);
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
            //comp\FUNC::showPre($data);
        }
    }

    protected function detailModerasiDesktop($id) {
        if (!empty($id)) {
            $data['pegawai'] = $this->getSession('SESSION_TEMP');
            $data['moderasi'] = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', ['id' => $id]);
            $data['kodeMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_kode_presensi', ['kode_presensi' => $data['moderasi']['kode_presensi']]);
            $data['jenisMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_jenis_moderasi', $data['moderasi']);
            $this->subView('detailDesktop', $data);
        }
    }

    protected function detailModerasiMobile($id) {
        if (!empty($id)) {
            $data['pegawai'] = $this->getSession('SESSION_TEMP');
            $data['moderasi'] = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', ['id' => $id]);
            $data['kodeMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_kode_presensi', ['kode_presensi' => $data['moderasi']['kode_presensi']]);
            $data['jenisMod'] = $this->servicemain->getDataKrit('db_presensi', 'tb_jenis_moderasi', $data['moderasi']);
            $this->subView('detailMobile', $data);
        }
    }

    protected function test() {
    	$input['tanggal_awal'] = '2018-11-10';
    	$input['tanggal_akhir'] = '2019-03-12';
    	$input['kdlokasi'] = 'G09011';
    	$result = $this->dbmoderasi->getReport($input);
    	comp\FUNC::showPre($result);
    }

    protected function simpan() {
        $input = $this->post(true);
        if ($input) {
            $dataPegawai = $this->getSession('SESSION_TEMP');
            $dataLogin = $this->getSession('SESSION_LOGIN');

            $data = $this->servicemain->getDataKrit('db_presensi', 'tb_moderasi', ['id' => $input['id']]);
            foreach ($data as $key => $val) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }

            $input['pin_absen'] = $dataPegawai['pin_absen'];
            $input['kdlokasi'] = $dataLogin['kdlokasi'];
            $chkExistMod = $this->dbmoderasi->chkExistMod($input);
            $chkLockReport = $this->dbmoderasi->getReport($input);
            $chkLockException = $this->dbmoderasi->getExceptPeriodMod($input);
            $limitTanggal = strtotime('2018-01-01');
            $chkTglAwal = strtotime($input['tanggal_awal']) >= $limitTanggal ? true : false;
            $chkTglAkhir = strtotime($input['tanggal_akhir']) >= $limitTanggal ? true : false;

            if ($input['tanggal_akhir'] < $input['tanggal_awal']) { // Check tanggal
                $error_msg = ['title' => 'Tanggal Salah', 'status' => 'warning', 'message' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal'];
            } else if ($chkTglAwal == false || $chkTglAkhir == false) {
                $error_msg = ['title' => 'Tanggal Salah', 'status' => 'warning', 'message' => 'Format tanggal salah atau tanggal kurang dari 1 Januari 2018'];
            } else if ($chkExistMod['count'] > 0) { // Check input baru agar tidak konflik
                $error_msg = ['title' => 'Moderasi Ganda', 'status' => 'warning', 'message' => 'Sudah ada moderasi pada tanggal tersebut'];
            } else if ($chkLockReport['count'] > 0) {
                $error_msg = ['title' => 'Laporan Dikunci', 'status' => 'warning', 'message' => 'Laporan moderasi pada tanggal tersebut sudah diverifikasi oleh Admin OPD.', 'query' => $chkLockReport['query']];
            } else if ($chkLockException['count'] > 0) {
            // } else if ($chkLockException['count'] > 0 && $this->login['pin_absen'] != '196702232006041001') { 
                $error_msg = ['title' => 'Moderasi Ditolak', 'status' => 'warning', 'message' => 'Tanggal moderasi berada pada periode yang dikunci.'];
            } else { // Insert dan Update data
                $data['pin_absen'] = $dataPegawai['pin_absen'];
                $data['kdlokasi'] = $dataLogin['kdlokasi'];
                $data['usergroup'] = $dataLogin['grup_pengguna_kd'];
                $data['status_final'] = 'DIPROSES';
                $data['flag_operator_opd'] = NULL;
                $data['flag_kepala_opd'] = NULL;
                $data['flag_operator_kota'] = NULL;
                $data['flag_kepala_kota'] = NULL;

                $data['dt_created'] = (empty($input['id'])) ? date('Y-m-d H:i:s') : $data['dt_created']; // Data baru
                $data['dt_last_modified'] = (!empty($input['id'])) ? date('Y-m-d H:i:s') : $data['dt_last_modified']; // Data mengedit yang sudah ada

                $result = $this->dbmoderasi->save_update('tb_moderasi', $data);
                $error_msg = ($result['error']) ?
                        ['title' => 'Sukses', 'status' => 'success', 'message' => 'Moderasi berhasil dibuat', 'queryLock' => $chkLockReport['query']] :
                        ['title' => 'Moderasi Error', 'status' => 'error', 'message' => 'Moderasi gagal dibuat, terdapat kesalahan sistem ketika menyimpan'];

                // comp\FUNC::showPre($chkExistMod);
            }
            echo json_encode($error_msg);
//            echo json_encode($chkLockException);
//            print_r($chkLockException);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = ['id' => $input['id']];
            $result = $this->dbmoderasi->delete('tb_moderasi', $idKey);
            $error_msg = ($result['error']) ?
                    ['title' => 'Sukses', 'status' => 'success', 'message' => 'Moderasi berhasil dihapus'] :
                    ['title' => 'Gagal', 'status' => 'warning', 'message' => 'Terjadi kesalahan ketika menghapus'];
            echo json_encode($error_msg);
            // comp\FUNC::showPre($input);
        }
    }

    protected function script() {
        $this->subView('script', []);
    }

}
