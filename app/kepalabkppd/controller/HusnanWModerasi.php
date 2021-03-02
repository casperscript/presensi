<?php

namespace app\kepalabkppd\controller;

use app\kepalabkppd\model\servicemain;
use app\kepalabkppd\model\HusnanWModerasiModel;
use app\kepalabkppd\model\pegawai_service;
use system;
use comp\FUNC;

class HusnanWModerasi extends system\Controller {

    protected $kodeGrup = null;

    public function __construct() {
        parent::__construct();

        $this->servicemaster = new HusnanWModerasiModel();
        $this->servicemain = new servicemain();
        $this->pegawai_service = new pegawai_service();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $this->kodeGrup = $this->login["grup_pengguna_kd"];
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }

        //FUNC::husnanWVarDump($this);
    }

    protected function index() {
        $this->daftarVerMod();
    }

    public function getDaftarOpd($kodeLokasi) {
        $data = $this->servicemaster->getDaftarOpd();
        echo json_encode($data);
    }

    protected function loadTabelVerMod($param) {
        $params = explode('X', $param);
        $kodeLokasi = empty($params[0]) || $params[0] === "undefined" ? null : $params[0];
        $page = $params[1];
        $status = isset($params[2]) ? $params[2] : '';
        $view = "tabel-ver-mod";
        $isFinal = false;

        if ($status === "final") {
            $isFinal = true;
            $view = "tabel-ver-mod-hasil";
        }

        $data['title'] = 'Tabel Verifikasi Moderasi';
        $daftarVerMod = $this->servicemaster->getDaftarVerMod($kodeLokasi, $isFinal, $page);

        //FUNC::husnanWVarDump($kodeLokasi);

        $data["daftarVerMod"] = $daftarVerMod["daftarModPegawai"];
        $data["page"] = $daftarVerMod["page"];
        $data["limiter"] = $daftarVerMod["limiter"];
        $data["total"] = $daftarVerMod["total"];
        $data["no"] = $daftarVerMod["no"];

        $this->subView($view, $data);
    }

    protected function daftarVerModOri($kodeLokasi) {
        $data['title'] = 'Daftar Verifikasi Moderasi';
        $daftarVerMod = $this->servicemaster->getDaftarVerMod($kodeLokasi, false, 1);
        $data["daftarVerMod"] = $daftarVerMod["daftarModPegawai"];
        $data["page"] = $daftarVerMod["page"];
        $data["limiter"] = $daftarVerMod["limiter"];
        $data["total"] = $daftarVerMod["total"];
        $data["no"] = $daftarVerMod["no"];
        $data["kodeLokasi"] = $kodeLokasi;
        $data["personNo"] = 0;
        $data["totalBelumVerifikasi"] = $this->servicemaster->getTotalBelumVerifikasi($kodeLokasi);
        $this->showView('daftar-ver-mod', $data, 'theme_admin');
    }

    protected function daftarVerMod($param = '') {
        $arrParam = explode('|', $param);
        $kodeLokasi = $arrParam[0];

        $data['title'] = 'Daftar Verifikasi Moderasi';
        $data["kodeLokasi"] = $kodeLokasi;
        $data['satker'] = $this->pegawai_service->getDataSatker($kodeLokasi)['singkatan_lokasi'];
        $data['kdBulan'] = !empty($arrParam[1]) ? $arrParam[1] : date("m", strtotime("-1 month")) - 1;
        $data['kdTahun'] = !empty($arrParam[2]) ? $arrParam[2] : date("Y", strtotime("-1 month"));

        if ($kodeLokasi == '' || $data['satker'] == '') {
            $this->redirect($this->link('laporan'));
        }

        $this->showView('index-proses-moderasi', $data, 'theme_admin');
    }

    protected function daftarVerModTabel() {
        $input = $this->post(true);
        if ($input) {
            $input['title'] = 'Daftar Proses Verifikasi Moderasi';

            if (!$this->servicemaster->checkLaporanVerif($input))
                $this->subView('daftar-ver-mod', ['notallowed' => true]);

            $input['daftarVerMod'] = $this->servicemaster->getDaftarVerMod($input);
            $input["totalBelumVerifikasi"] = $this->servicemaster->getTotalBelumVerifikasi($input);
            $this->subView('daftar-ver-mod', $input);
        }
    }

    protected function daftarVerModHasil() {
        $data['title'] = 'Daftar Hasil Akhir Verifikasi Moderasi';
        $daftarVerMod = $this->servicemaster->getDaftarVerMod(null, true, 1);
        $data["daftarVerMod"] = $daftarVerMod["daftarModPegawai"];
        $this->showView('daftar-ver-mod-hasil', $data, 'theme_admin');
    }

    public function getTotalBelumVerifikasi($kodeLokasi) {
        echo json_encode(["total" => $this->servicemaster->getTotalBelumVerifikasi($kodeLokasi)]);
    }

    public function infoModerasiOri($mid) {
        $data["info"] = $this->servicemaster->getInfoModerasi($mid);

        if ($data["info"] === false) {
            echo "<script>window.location.reload();</script>";
            return;
        }

        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function infoModerasi($mid) {
        $data["info"] = $this->servicemaster->getDetailMod($mid);
        if ($data["info"] === false) {
            echo "<script>window.location.reload();</script>";
            return;
        }

        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function infoModerasiHasil($mid) {
        $data["info"] = $this->servicemaster->getInfoModerasi($mid, true);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function massLegitPage() {
        $posts = $this->post(true);
        $data["checkedMods"] = isset($posts["checkedMods"]) ? $posts["checkedMods"] : [];
        $data["checkedMods"] = implode(',', $data["checkedMods"]);
        $this->subView('mass-legit-page', $data);
    }

    public function updateModerasi() {
        $posts = $this->post(true);
        $flags = $this->servicemaster->getFlags($posts["mid"]);

        if (($posts["flag"] !== "2") || ($flags["flag_kepala_opd"] === "2" || $flags["flag_kepala_opd"] === "3")) {
            echo json_encode(["status" => "fail", "reload" => "1"]);
            return;
        }

        if ($this->servicemaster->updateModerasi($posts) > 0) {
            echo json_encode(["status" => "success", "date" => $this->servicemaster->getDateModified($posts["mid"])]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
    }

    public function updateModerasiMassLegit() {
        $posts = $this->post(true);
        $mids = explode(',', $posts["mids"]);

        if (count($mids) === 0) {
            echo json_encode(["status" => "fail", "message" => "Tidak ada moderasi yang diproses!", "reload" => "1"]);
            return;
        }

        $validMids = [];

        foreach ($mids as $mid) {
            $flags = $this->servicemaster->getFlags($mid);

            if (($posts["flag"] !== "2") || ($flags["flag_kepala_opd"] === "2" || $flags["flag_kepala_opd"] === "3")) {
                continue;
            } else {
                $validMids[] = $mid;
            }
        }

        //$posts = ["mids" => $validMids, "flag" => "2", "catatan" => $posts["catatan"]]; -- edited by daniek
        $posts = ["mids" => $validMids, "flag" => "2", "catatan" => $posts["catatan"], 'kdlokasi' => $posts['kdlokasi']];

        if ($this->servicemaster->updateModerasi($posts, true) > 0) {
            echo json_encode(["status" => "success"]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
    }

    public function getJenisModerasi() {
        return $this->servicemaster->getJenisModerasi();
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
